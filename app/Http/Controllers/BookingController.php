<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $query = Treatment::with(['details','category']);

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $treatments = $query->orderBy('name', 'asc')->get();

        // Cek jam operasional (09:00 - 18:00)
        $now = Carbon::now();
        $start = Carbon::createFromTime(9, 0, 0);
        $end = Carbon::createFromTime(18, 0, 0);
        $isOpen = $now->between($start, $end);

        if ($request->ajax() || $request->has('is_ajax')) {
            return view('booking.partials._treatment_list', compact('treatments'));
        }

        return view('booking.index', compact('treatments', 'categories', 'isOpen'));
    }

    // STEP 1: Pilih stylist & waktu
    public function select($treatmentId)
    {
        $treatment = Treatment::with('details')->findOrFail($treatmentId);
        $stylists = User::where('role', 'karyawan')->get();
        $allTreatments = Treatment::with(['details', 'category'])->get();
        $categories = Category::all();
        
        // Ambil tanggal libur
        $holidays = \App\Models\Holiday::pluck('date')->toArray();

        return view('booking.select', compact('treatment', 'stylists', 'allTreatments', 'categories', 'holidays'));
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
            'payment_method' => 'required|in:cash,transfer'
        ]);

        // Validasi Hari Libur
        $isHoliday = \App\Models\Holiday::where('date', $request->reservation_date)->exists();
        if ($isHoliday) {
            $msg = 'Mohon maaf, salon tutup pada tanggal tersebut (Hari Libur).';
            if ($request->ajax()) return response()->json(['message' => $msg], 422);
            return redirect()->back()->with('error', $msg);
        }

        // Validasi Jam Operasional (09:00 - 18:00)
        $dateTime = Carbon::parse($request->reservation_date.' '.$request->reservation_time);
        $hour = $dateTime->hour;
        
        if ($hour < 9 || $hour >= 18) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Mohon maaf, jam reservasi harus di antara 09:00 - 18:00.'], 400);
            }
            return redirect()->back()->with('error', 'Mohon maaf, jam reservasi harus di antara 09:00 - 18:00.');
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
                    $dur = $d->treatmentDetail->duration;
                    $currEnd = $currStart->copy()->addMinutes($dur);
                    if ($d->stylist_id) {
                        $stylistWindows[$d->stylist_id][] = ['start' => $currStart->copy(), 'end' => $currEnd->copy()];
                    }
                    $currStart = $currEnd->copy();
                }
            }
        }

        $tempRequestedStart = $startCheckpoint->copy();
        foreach ($request->treatment_detail_ids as $index => $dId) {
            $sId = $request->stylist_ids[$index] ?? null;
            if (!$sId) continue;

            $detail = \App\Models\TreatmentDetail::find($dId);
            if (!$detail) continue;

            $dur = $detail->duration;
            $tempRequestedEnd = $tempRequestedStart->copy()->addMinutes($dur);

            if (isset($stylistWindows[$sId])) {
                foreach ($stylistWindows[$sId] as $win) {
                    if ($tempRequestedStart->lt($win['end']) && $tempRequestedEnd->gt($win['start'])) {
                        $stylistName = \App\Models\User::find($sId)->name ?? 'Stylist';
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

        foreach ($detailIds as $index => $dId) {
            $detail = TreatmentDetail::findOrFail($dId);
            $sId = $stylistIds[$index] ?? null;
            $stylist = $sId ? User::find($sId) : null;
            
            $price = $detail->price;
            
            if ($detail->has_stylist_price && $stylist) {
                if (strtolower($stylist->kategori) == 'senior') {
                    $price = $detail->price_senior ?? $price;
                } elseif (strtolower($stylist->kategori) == 'junior') {
                    $price = $detail->price_junior ?? $price;
                }
            }

            $booking_details_data[] = [
                'treatment_detail_id' => $detail->id,
                'stylist_id' => $sId,
                'price' => $price,
                'parent_treatment_id' => $detail->treatment_id
            ];
            $total_price += $price;
        }

        $isStaff = in_array(Auth::user()->role, ['admin', 'karyawan']);
        $paymentStatus = ($isStaff && $request->payment_method === 'cash') ? 'paid' : 'unpaid';

        $booking = Booking::create([
            'user_id' => $isStaff ? null : Auth::id(),
            'customer_name' => $request->customer_name,
            'cashier_id' => $isStaff ? Auth::id() : null,
            'stylist_id' => $booking_details_data[0]['stylist_id'], // Primary stylist fallback
            'treatment_id' => $booking_details_data[0]['parent_treatment_id'], // Primary treatment
            'reservation_datetime' => Carbon::parse($request->reservation_date.' '.$request->reservation_time),
            'total_price' => $total_price,
            'status' => 'pending',
            'payment_status' => $paymentStatus,
            'payment_method' => $request->payment_method
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
        if ($request->payment_method === 'transfer') {
            $params = [
                'transaction_details' => [
                    'order_id' => 'BOOK-' . $booking->id . '-' . time(),
                    'gross_amount' => (int) $total_price,
                ],
                'customer_details' => [
                    'first_name' => $request->customer_name,
                    'email' => Auth::user()->role === 'pelanggan' ? Auth::user()->email : 'info@indahsarisalon.com', // Fallback email for staff bookings
                ],
            ];

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
            'stylists' => User::where('role', 'karyawan')->get(), // optional
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

        if ($user->role === 'pelanggan') {
            $query->where('user_id', $user->id);
        } elseif ($user->role === 'karyawan') {
            $query->where(function ($q) use ($user) {
                $q->where('cashier_id', $user->id)
                    ->orWhere('stylist_id', $user->id);
            });
        }

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

        // Tentukan view berdasarkan role
        $view = (Auth::user()->role === 'karyawan') ? 'karyawan.bookings.index' : 'admin.bookings.index';

        return view($view, compact('bookings', 'status', 'stats'));
    }

    // ADMIN: Update status booking
    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,berhasil,dibatalkan'
        ]);

        $booking->update(['status' => $request->status]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status booking berhasil diperbarui.',
                'status' => $booking->status
            ]);
        }

        return redirect()->back()->with('success', 'Status booking berhasil diperbarui.');
    }

    // ADMIN: View detail booking (JSON)
    public function show($id)
    {
        $booking = Booking::with(['user', 'stylist', 'treatment', 'cashier', 'details.treatmentDetail', 'details.stylist'])->findOrFail($id);
        return response()->json($booking);
    }

    // Update metode pembayaran (untuk fitur ganti mind/cancel midtrans)
    public function updatePaymentMethod(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $request->validate(['payment_method' => 'required|in:cash,transfer']);

        $isStaff = in_array(Auth::user()->role, ['admin', 'karyawan']);
        $paymentStatus = ($isStaff && $request->payment_method === 'cash') ? 'paid' : 'unpaid';

        $booking->update([
            'payment_method' => $request->payment_method,
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
                            $duration = $d->treatmentDetail->duration;
                            $currentEnd = $currentStart->copy()->addMinutes($duration);
                            
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

                    $duration = $detail->duration;
                    $currentRequestedEnd = $currentRequestedStart->copy()->addMinutes($duration);

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
