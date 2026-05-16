<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\User;
use App\Models\Treatment;
use App\Models\TreatmentDetail;
use Carbon\Carbon;
use Midtrans\Config;
use Midtrans\Snap;

class BookingController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');
    }
    // STEP 0: Halaman daftar treatment
    public function index(Request $request)
    {
        $categories = Category::all();
        $query = Treatment::with(['details', 'category'])
            ->join('categories', 'treatments.category_id', '=', 'categories.id')
            ->where('treatments.is_active', true)
            ->select('treatments.*');

        if ($request->filled('category')) {
            $query->where('treatments.category_id', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('treatments.name', 'like', '%' . $request->search . '%');
        }

        Log::info('Booking Index Request', [
            'category' => $request->category,
            'search' => $request->search,
            'is_ajax' => $request->has('is_ajax') || $request->ajax()
        ]);

        $treatments = $query->orderByRaw("CASE WHEN categories.name = 'Promo' THEN 0 ELSE 1 END")
            ->orderBy('treatments.is_promo', 'desc')
            ->orderBy('treatments.name', 'asc')
            ->get();

        Log::info('Treatments Found: ' . $treatments->count());

        // Cek jam operasional (09:00 - 18:00)
        $now = Carbon::now();
        $start = Carbon::createFromTime(9, 0, 0);
        $end = Carbon::createFromTime(18, 0, 0);
        $isOpen = $now->between($start, $end);

        if ($request->ajax() || $request->has('is_ajax') || $request->expectsJson() || $request->is('api/*')) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $treatments,
                    'categories' => $categories,
                    'is_open' => $isOpen
                ]);
            }
            return view('booking.partials._treatment_list', compact('treatments'));
        }

        return view('booking.index', compact('treatments', 'categories', 'isOpen'));
    }

    // STEP 1: Pilih stylist & waktu
    public function select(Request $request, $treatmentId)
    {
        $treatment = Treatment::with('details')->findOrFail($treatmentId);
        $stylists = User::whereIn('role', ['admin', 'karyawan'])->get();
        $allTreatments = Treatment::with(['details', 'category'])->get();
        $categories = Category::all();

        // Handle pre-selected details from query param ?details=1,2,3
        $preSelectedDetails = collect();
        if ($request->filled('details')) {
            $ids = explode(',', $request->details);
            $preSelectedDetails = TreatmentDetail::with('treatment.category')
                ->whereIn('id', $ids)
                ->get();
        }
        
        // Ambil data staff untuk UI (Hanya Admin dan Karyawan)
        $isStaff = in_array(strtolower(Auth::user()->role ?? ''), ['owner', 'admin', 'karyawan']);
        $customers = [];
        if ($isStaff) {
            $customers = User::where('role', 'pelanggan')
                ->orderBy('name', 'asc')
                ->get(['id', 'name', 'email', 'phone'])
                ->map(function($user) {
                    return $user->append('has_coloring_loyalty');
                });
        }

        // Ambil tanggal libur
        $holidays = \App\Models\Holiday::pluck('date')->toArray();

        return view('booking.select', compact('treatment', 'stylists', 'allTreatments', 'categories', 'holidays', 'customers', 'isStaff', 'preSelectedDetails'));
    }
    // STEP 2: Simpan booking
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'treatment_detail_ids' => 'required|array',
            'treatment_detail_ids.*' => 'exists:treatment_details,id',
            'stylist_ids' => 'required|array',
            'stylist_ids.*' => 'nullable|exists:users,id',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required',
            'payment_method' => 'required|in:Tunai,Transfer,QRIS'
        ]);

        // Server-side validation: Ensure one variant per treatment
        $treatmentIds = \App\Models\TreatmentDetail::whereIn('id', $request->treatment_detail_ids)
            ->pluck('treatment_id')
            ->toArray();
        
        if (count($treatmentIds) !== count(array_unique($treatmentIds))) {
            $msg = 'Mohon maaf, Anda hanya dapat memilih satu jenis layanan untuk setiap kategori treatment yang sama demi keamanan perawatan.';
            if ($request->ajax()) return response()->json(['message' => $msg], 422);
            return redirect()->back()->with('error', $msg);
        }

        // Validasi Hari Libur
        $isHoliday = \App\Models\Holiday::where('date', $request->reservation_date)->exists();
        if ($isHoliday) {
            $msg = 'Mohon maaf, salon tutup pada tanggal tersebut (Hari Libur).';
            if ($request->ajax()) return response()->json(['message' => $msg], 422);
            return redirect()->back()->with('error', $msg);
        }

        // Validasi Jam Operasional (09:00 - 10:30 sesuai permintaan client)
        $dateTime = Carbon::parse($request->reservation_date.' '.$request->reservation_time);
        $hour = $dateTime->hour;
        $minute = $dateTime->minute;
        
        // Cek apakah lebih dari jam 10:30
        if ($hour < 9 || $hour > 10 || ($hour === 10 && $minute > 30)) {
            $errorMsg = 'Mohon maaf, jam reservasi maksimal adalah pukul 10:30.';
            if ($request->ajax()) {
                return response()->json(['message' => $errorMsg], 400);
            }
            return redirect()->back()->with('error', $errorMsg);
        }

        
        // VALIDASI KETERSEDIAAN STYLIST
        $requestedDate = $request->reservation_date;
        $requestedTime = $request->reservation_time;
        $startCheckpoint = \Carbon\Carbon::parse($requestedDate . ' ' . $requestedTime);

        // Ambil semua booking yang aktif hari ini
        $existingBookings = \App\Models\Booking::whereDate('reservation_datetime', $requestedDate)
            ->whereNotIn('status', ['dibatalkan'])
            ->with(['details.treatmentDetail'])
            ->get();

        $stylistWindows = [];
        foreach ($existingBookings as $b) {
            $currStart = \Carbon\Carbon::parse($b->reservation_datetime);
            foreach ($b->details as $d) {
                if ($d->treatmentDetail) {
                    // Sistem otomatis memblokir 7 jam dari jam terpilih (permintaan client)
                    $currEnd = $currStart->copy()->addHours(7);
                    if ($d->stylist_id) {
                        $stylistWindows[$d->stylist_id][] = ['start' => $currStart->copy(), 'end' => $currEnd->copy()];
                    }
                    $currStart = $currEnd->copy();
                }
            }
        }

        // Pre-fetch all needed data BEFORE any loops to avoid N+1 queries
        $allDetailIds = collect($request->treatment_detail_ids)->filter()->unique()->toArray();
        $allStylistIds = collect($request->stylist_ids)->filter()->unique()->toArray();
        $preloadedDetails = \App\Models\TreatmentDetail::with(['treatment.category'])->whereIn('id', $allDetailIds)->get()->keyBy('id');
        $preloadedStylists = \App\Models\User::whereIn('id', $allStylistIds)->get()->keyBy('id');

        $tempRequestedStart = $startCheckpoint->copy();
        foreach ($request->treatment_detail_ids as $index => $dId) {
            $sId = $request->stylist_ids[$index] ?? null;
            if (!$sId) continue;

            $detail = $preloadedDetails->get($dId);
            if (!$detail) continue;

            // Blokir 7 jam untuk pengecekan ketersediaan (permintaan client)
            $tempRequestedEnd = $tempRequestedStart->copy()->addHours(7);

            if (isset($stylistWindows[$sId])) {
                foreach ($stylistWindows[$sId] as $win) {
                    if ($tempRequestedStart->lt($win['end']) && $tempRequestedEnd->gt($win['start'])) {
                        $stylistName = $preloadedStylists->get($sId)->name ?? 'Stylist';
                        $msg = "Mohon maaf, $stylistName sudah memiliki jadwal pada jam tersebut (layanan ke-" . ($index+1) . "). Silakan pilih stylist lain atau geser jam reservasi.";
                        if ($request->ajax()) return response()->json(['message' => $msg], 422);
                        return redirect()->back()->with('error', $msg);
                    }
                }
            }
            $tempRequestedStart = $tempRequestedEnd->copy();
        }

        $detailIds = $request->treatment_detail_ids;
        $stylistIds = $request->stylist_ids;

        $total_price = 0;
        $booking_details_data = [];
        $customPrices = $request->custom_prices ?? [];
        
        // Robust Staff Detection
        $authUser = Auth::user();
        $isStaff = false;
        if ($authUser) {
            $role = strtolower(trim($authUser->role ?? ''));
            $type = strtolower(trim($authUser->type ?? ''));
            if ($role === 'owner' || $role === 'admin' || $type === 'karyawan' || $authUser->id === 1) {
                $isStaff = true;
            }
        }

        $customer = null;
        if ($request->selected_user_id) {
            $customer = User::find($request->selected_user_id);
        } elseif (!$isStaff && Auth::check()) {
            $customer = $authUser;
        }


        // Pre-fetch all needed data to avoid N+1 queries
        foreach ($detailIds as $index => $dId) {
            $detail = $preloadedDetails->get($dId);
            if (!$detail) continue;
            
            $sId = $stylistIds[$index] ?? null;
            $stylist = $sId ? $preloadedStylists->get($sId) : null;
            
            $price = $detail->price;
            
            // 1. Cek Harga berdasarkan Kategori Stylist (jika ada)
            if ($detail->has_stylist_price && $stylist) {
                if (strtolower($stylist->kategori) == 'senior') {
                    $price = $detail->price_senior ?? $price;
                } elseif (strtolower($stylist->kategori) == 'junior') {
                    $price = $detail->price_junior ?? $price;
                }
            }

            // 2. LOGIKA DISKON OTOMATIS (MEMBERSHIP & LOYALTY)
            $bestDiscount = 0;
            if ($customer) {
                // Diskon Loyalitas Coloring (35%)
                $isColoring = false;
                if ($detail->treatment && $detail->treatment->category) {
                    if (stripos($detail->treatment->category->name, 'Coloring') !== false) {
                        $isColoring = true;
                    }
                }

                if ($isColoring && $customer->has_coloring_loyalty) {
                    $bestDiscount = 35;
                }
            }

            // 3. Terapkan Potongan Promo (Date-Aware)
            $parentTreatment = $detail->treatment;
            $fixedPromoPrice = null;
            if ($parentTreatment && $parentTreatment->is_promo) {
                $resDate = Carbon::parse($request->reservation_date)->toDateString();
                $isWithinPromo = true;

                if ($parentTreatment->promo_start_date && $resDate < $parentTreatment->promo_start_date) {
                    $isWithinPromo = false;
                }
                if ($parentTreatment->promo_end_date && $resDate > $parentTreatment->promo_end_date) {
                    $isWithinPromo = false;
                }

                if ($isWithinPromo) {
                    $pType = strtolower($parentTreatment->promo_type);
                    if (in_array($pType, ['percentage', 'percent', 'persen'])) {
                        $promoVal = $parentTreatment->promo_value;
                        if ($promoVal > $bestDiscount) $bestDiscount = $promoVal;
                    } else {
                        $fixedPromoPrice = (float) $parentTreatment->promo_value;
                    }
                }
            }

            // Eksekusi Diskon Terbaik
            if ($bestDiscount > 0) {
                $discountAmount = ($price * $bestDiscount) / 100;
                $price = $price - $discountAmount;
            }

            if ($fixedPromoPrice !== null && $fixedPromoPrice < $price) {
                $price = $fixedPromoPrice;
            }

            // 4. Gunakan harga kustom jika disediakan oleh staff (timpa semua diskon)
            if (isset($customPrices[$index]) && $customPrices[$index] !== '') {
                $price = (int)$customPrices[$index];
            }

            $booking_details_data[] = [
                'treatment_detail_id' => $detail->id,
                'stylist_id' => $sId,
                'price' => (int)max(0, $price),
                'parent_treatment_id' => $detail->treatment_id
            ];
            $total_price += (int)max(0, $price);
        }

        Log::info('Booking Attempt', [
            'booking_id_potential' => 'next',
            'auth_id' => Auth::id(),
            'role' => $authUser->role ?? 'N/A',
            'type' => $authUser->type ?? 'N/A',
            'is_staff_detected' => $isStaff,
            'payment_method' => $request->payment_method,
            'user_agent' => $request->userAgent()
        ]);
        
        $paymentMethod = strtolower($request->payment_method);
        $paymentStatus = ($isStaff && $paymentMethod === 'tunai') ? 'paid' : 'unpaid';

        // Log Debug untuk investigasi masalah 'unpaid'
        try {
            $debugData = [
                'timestamp' => now()->toDateTimeString(),
                'auth_id' => Auth::id(),
                'auth_user' => $authUser ? ['id' => $authUser->id, 'role' => $authUser->role, 'type' => $authUser->type] : 'NULL',
                'is_staff' => $isStaff,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'request' => $request->all()
            ];
            Storage::disk('local')->put('debug_booking.json', json_encode($debugData, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {}

        $booking = Booking::create([
            'user_id' => $request->selected_user_id ?: ($isStaff ? null : $authUser->id),
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_email' => $request->customer_email,
            'cashier_id' => $isStaff ? $authUser->id : null,
            'stylist_id' => $booking_details_data[0]['stylist_id'],
            'treatment_id' => $booking_details_data[0]['parent_treatment_id'],
            'reservation_datetime' => Carbon::parse($request->reservation_date.' '.$request->reservation_time),
            'total_price' => (int)$total_price,
            'status' => 'pending',
            'payment_status' => $paymentStatus,
            'payment_method' => strtolower($request->payment_method) === 'tunai' ? 'Tunai' : $request->payment_method
        ]);

        foreach ($booking_details_data as $item) {
            BookingDetail::create([
                'booking_id' => $booking->id,
                'treatment_detail_id' => $item['treatment_detail_id'],
                'stylist_id' => $item['stylist_id'],
                'price' => $item['price']
            ]);
        }

        // Midtrans Logic
        $snapToken = null;
        if ($request->payment_method === 'Transfer' || $request->payment_method === 'QRIS') {
            $params = [
                'transaction_details' => [
                    'order_id' => 'BOOK-' . $booking->id . '-' . time(),
                    'gross_amount' => (int) $total_price,
                ],
                'customer_details' => [
                    'first_name' => $request->customer_name,
                    'email' => (strtolower($authUser->role) === 'pelanggan' && $authUser->type !== 'karyawan') ? $authUser->email : 'info@indahsarisalon.com', // Fallback email for staff bookings
                ],
            ];

            if ($request->payment_method === 'QRIS') {
                $params['enabled_payments'] = ['gopay', 'shopeepay', 'qris'];
            }

            try {
                $snapToken = Snap::getSnapToken($params);
                $booking->update(['snap_token' => $snapToken]);
            } catch (\Exception $e) {
                if ($request->ajax()) {
                    return response()->json(['message' => 'Gagal terhubung ke Midtrans: ' . $e->getMessage()], 500);
                }
                return redirect()->back()->with('error', 'Gagal terhubung ke Midtrans: ' . $e->getMessage());
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'booking_id' => $booking->id,
                'snap_token' => $snapToken,
                'payment_method' => $request->payment_method
            ]);
        }

        return redirect()->route('booking.summary', $booking->id);
    }

    // STEP 3: Ringkasan booking
    public function summary($bookingId)
    {
        $booking = Booking::with(['user', 'stylist', 'treatment', 'details.treatmentDetail'])
            ->findOrFail($bookingId);

        return view('booking.summary', [
            'booking' => $booking,
            'treatment' => $booking->treatment,
            'stylists' => User::where('role', 'admin')->get(), // optional
            'stylist' => $booking->stylist,
            'reservation_datetime' => $booking->reservation_datetime,
            'total_price' => $booking->total_price
        ]);
    }

    // STEP 4: Bayar
    public function pay(Request $request, $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        $booking->payment_status = 'paid';
        // Status tetap 'pending' agar muncul di "Dalam Proses", hanya payment_status yang lunas
        if ($booking->status === 'confirmed') {
            $booking->status = 'pending'; // kembalikan ke pending agar bisa dikelola admin
        }
        $booking->save();

        return redirect()->route('booking.history')->with('success','Pembayaran berhasil!');
    }

    // Riwayat booking
    public function history()
    {
        $user = Auth::user();
        $query = Booking::with(['treatment', 'stylist', 'cashier', 'details.treatmentDetail.treatment', 'details.stylist']);

        if (strtolower($user->role) === 'pelanggan' && $user->type !== 'karyawan') {
            $query->where('user_id', $user->id);
        } elseif ($user->type === 'karyawan' && !in_array(strtolower($user->role), ['owner', 'admin'])) {
            $query->where(function ($q) use ($user) {
                $q->where('cashier_id', $user->id)
                    ->orWhere('stylist_id', $user->id);
            });
        }
        // Owner and Admin will bypass the where clauses to see ALL bookings.

        $allBookings = $query->orderBy('reservation_datetime', 'desc')->get();

        // Bagi data untuk Pelanggan (Proses vs Riwayat)
        $inProcess = $allBookings->whereIn('status', ['pending', 'confirmed']);
        $history = $allBookings->whereIn('status', ['berhasil', 'dibatalkan']);

        return view('booking.history', compact('inProcess', 'history', 'allBookings'));
    }

    // Batalkan booking (Pelanggan)
    public function cancel(Request $request, $id)
    {
        $booking = Booking::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($booking->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Pemesanan ini tidak dapat dibatalkan.'], 400);
        }

        $booking->update([
            'status' => 'dibatalkan',
            'cancel_reason' => $request->reason ?? 'Dibatalkan oleh pelanggan'
        ]);

        return response()->json(['success' => true, 'message' => 'Booking berhasil dibatalkan.']);
    }

    // Midtrans Webhook
    public function handleNotification(Request $request)
    {
        $payload = $request->getContent();
        $notification = json_decode($payload);

        $validSignatureKey = hash("sha512", $notification->order_id . $notification->status_code . $notification->gross_amount . config('services.midtrans.server_key'));

        if ($notification->signature_key != $validSignatureKey) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $orderIdParts = explode('-', $notification->order_id);
        $bookingId = $orderIdParts[1];
        $booking = Booking::findOrFail($bookingId);

        $transactionStatus = $notification->transaction_status;
        $type = $notification->payment_type;

        if ($transactionStatus == 'capture') {
            if ($type == 'credit_card') {
                if ($notification->fraud_status == 'accept') {
                    $booking->update(['payment_status' => 'paid']);
                }
            }
        } elseif ($transactionStatus == 'settlement') {
            $booking->update(['payment_status' => 'paid']);
        } elseif ($transactionStatus == 'pending') {
            $booking->update(['payment_status' => 'pending']);
        } elseif ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
            $booking->update(['payment_status' => 'failed']);
        }

        return response()->json(['message' => 'Success'], 200);
    }

    // ADMIN: Daftar semua booking
    public function adminIndex(Request $request)
    {
        $status = $request->get('status', 'pending'); // Default to pending

        $query = Booking::with(['user', 'stylist', 'treatment', 'cashier']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Filter Tanggal (Harian, Bulanan, Tahunan)
        $filter_mode = $request->get('filter_mode');
        $filter_value = $request->get('filter_value');

        if ($filter_mode && $filter_value) {
            if ($filter_mode === 'daily') {
                $query->whereDate('reservation_datetime', $filter_value);
            } elseif ($filter_mode === 'monthly') {
                $query->whereRaw("TO_CHAR(reservation_datetime, 'YYYY-MM') = ?", [$filter_value]);
            } elseif ($filter_mode === 'yearly') {
                $query->whereRaw("TO_CHAR(reservation_datetime, 'YYYY') = ?", [$filter_value]);
            }
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(10);

        // Stats
        $stats = [
            'total' => Booking::count(),
            'pending' => Booking::where('status', 'pending')->count(),
            'berhasil' => Booking::where('status', 'berhasil')->count(),
            'dibatalkan' => Booking::where('status', 'dibatalkan')->count(),
        ];

        return view('admin.bookings.index', compact('bookings', 'status', 'stats'));
    }

    // ADMIN: Update status booking
    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,berhasil,dibatalkan'
        ]);

        $updateData = ['status' => $request->status];

        // Jika status diubah menjadi berhasil (Selesai), maka status pembayaran otomatis Paid
        if ($request->status === 'berhasil') {
            $updateData['payment_status'] = 'paid';
        }

        $booking->update($updateData);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status booking berhasil diperbarui.',
                'status' => $booking->status
            ]);
        }

        return redirect()->back()->with('success', 'Status booking berhasil diperbarui.');
    }

    public function reschedule(Request $request, Booking $booking)
    {
        $request->validate([
            'reservation_datetime' => 'required|date'
        ]);

        $booking->update([
            'reservation_datetime' => $request->reservation_datetime
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Jadwal booking berhasil diperbarui.',
                'new_datetime' => $booking->reservation_datetime
            ]);
        }

        return redirect()->back()->with('success', 'Jadwal booking berhasil diperbarui.');
    }

    // ADMIN: View detail booking (JSON)
    public function show($id)
    {
        try {
            $booking = Booking::with([
                'user', 
                'stylist', 
                'treatment', 
                'cashier', 
                'details.treatmentDetail', 
                'details.stylist'
            ])->findOrFail($id);
            
            // Disable appends to prevent expensive calculations and infinite recursion during serialization
            $booking->setAppends([]);
            if ($booking->treatment) $booking->treatment->setAppends([]);
            
            $booking->details->each(function($detail) {
                $detail->setAppends([]);
                if ($detail->treatmentDetail) {
                    $detail->treatmentDetail->setAppends([]);
                }
            });

            return response()->json($booking);
        } catch (\Throwable $e) { // Use Throwable to catch both Exception and Error
            \Log::error("Error showing booking detail: " . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    // Update metode pembayaran (untuk fitur ganti mind/cancel midtrans)
    public function updatePaymentMethod(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $request->validate(['payment_method' => 'required|in:Tunai,Transfer,QRIS']);

        $authId = Auth::id();
        $authUser = $authId ? \App\Models\User::find($authId) : null;
        $isStaff = false;
        if ($authUser) {
            $role = strtolower(trim($authUser->role));
            $type = strtolower(trim($authUser->type));
            if ($role === 'owner' || $role === 'admin' || $type === 'karyawan' || $authUser->id === 1) {
                $isStaff = true;
            }
        }

        Log::info('Payment Method Update Attempt', [
            'booking_id' => $id,
            'auth_id' => $authId,
            'role' => $authUser->role ?? 'N/A',
            'is_staff_detected' => $isStaff,
            'new_method' => $request->payment_method
        ]);

        $paymentStatus = ($isStaff && strtolower($request->payment_method) === 'tunai') ? 'paid' : 'unpaid';

        $booking->update([
            'payment_method' => strtolower($request->payment_method) === 'tunai' ? 'Tunai' : $request->payment_method,
            'payment_status' => $paymentStatus
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Metode pembayaran berhasil diubah ke ' . strtoupper($request->payment_method),
            'payment_method' => $request->payment_method,
            'is_staff' => $isStaff
        ]);
    }

    /**
     * AJAX: Check stylist availability based on date, time, and selection duration.
     */
    public function checkStylistAvailability(Request $request)
    {
        $date = $request->reservation_date;
        $time = $request->reservation_time;
        $selection = $request->selected_details; 

        if (!$date || empty($selection)) {
            return response()->json(['conflicts' => [], 'off_work_ids' => []]);
        }

        try {
            $startTime = Carbon::parse($date . ' ' . $time);
            $hour = $startTime->hour;
            $minute = $startTime->minute;

            // Validasi 10:30 di AJAX juga
            if ($hour < 9 || $hour > 10 || ($hour === 10 && $minute > 30)) {
                return response()->json([
                    'conflicts' => [], 
                    'off_work_ids' => [],
                    'message' => 'Maksimal booking jam 10:30'
                ]);
            }
            
            // 0. Check if it's a holiday
            $isHoliday = \App\Models\Holiday::where('date', $date)->exists();
            if ($isHoliday) {
                return response()->json([
                    'is_holiday' => true,
                    'message' => 'Salon tutup pada tanggal ini.',
                    'conflicts' => []
                ]);
            }

            // 0a. Check for stylists who are "Off Work" or "Libur"
            $offWorkIds = \App\Models\Absensi::whereDate('tanggal', $date)
                ->whereIn('status', ['Off Work', 'Libur', 'libur', 'off work'])
                ->pluck('user_id')
                ->map(fn($id) => (int)$id)
                ->toArray();

            $conflicts = [];
            if ($time) {
                $startTime = Carbon::parse($date . ' ' . $time);
                
                // 1. Get ALL bookings for that day (except dibatalkan)
                $existingBookings = Booking::whereDate('reservation_datetime', $date)
                    ->whereNotIn('status', ['dibatalkan'])
                    ->with(['details.treatmentDetail'])
                    ->get();

                // 2. Map existing busy windows for each stylist
                $stylistWindows = [];
                foreach ($existingBookings as $b) {
                    // Determine starting point for this booking
                    $currentStart = Carbon::parse($b->reservation_datetime);
                    
                    // Details are sequential
                    foreach ($b->details as $d) {
                        if ($d->treatmentDetail) {
                            // Blokir 7 jam (permintaan client)
                            $currentEnd = $currentStart->copy()->addHours(7);
                            
                            if ($d->stylist_id) {
                                $stylistWindows[$d->stylist_id][] = [
                                    'start' => $currentStart->copy(),
                                    'end' => $currentEnd->copy()
                                ];
                            }
                            
                            $currentStart = $currentEnd->copy();
                        }
                    }
                }

                // 3. Check requested selection window for each detail index
                $currentRequestedStart = $startTime->copy();
                
                foreach ($selection as $index => $item) {
                    // Item might be just an ID or an object
                    $id = is_array($item) ? $item['id'] : $item;
                    $detail = TreatmentDetail::find($id);
                    
                    if (!$detail) {
                        $conflicts[$index] = [];
                        continue;
                    }

                    // Blokir 7 jam (permintaan client)
                    $currentRequestedEnd = $currentRequestedStart->copy()->addHours(7);

                    $busyIds = [];
                    foreach ($stylistWindows as $stylistId => $windows) {
                        foreach ($windows as $win) {
                            // Overlap check: start1 < end2 AND end1 > start2
                            if ($currentRequestedStart->lt($win['end']) && $currentRequestedEnd->gt($win['start'])) {
                                $busyIds[] = (int)$stylistId;
                                break;
                            }
                        }
                    }
                    
                    $conflicts[$index] = array_values(array_unique($busyIds));
                    
                    // Increment for next treatment in selection
                    $currentRequestedStart = $currentRequestedEnd->copy();
                }
            }

            return response()->json([
                'conflicts' => $conflicts,
                'off_work_ids' => $offWorkIds
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
