<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\User;
use App\Models\Treatment;
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

        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->search) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $treatments = $query->get();

        return view('booking.index', compact('treatments', 'categories'));
    }

    // STEP 1: Pilih stylist & waktu
    public function select($treatmentId)
{
    $treatment = Treatment::with('details')->findOrFail($treatmentId);
    $stylists = User::where('role', 'karyawan')->get();

    return view('booking.select', compact('treatment', 'stylists'));
}
    // STEP 2: Simpan booking
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'treatment_id' => 'required|exists:treatments,id',
            'stylist_id' => 'nullable|exists:users,id',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required',
            'payment_method' => 'required|in:cash,transfer'
        ]);

        $treatment = Treatment::with('details')->findOrFail($request->treatment_id);
        $stylist = $request->stylist_id ? User::find($request->stylist_id) : null;

        $total_price = 0;
        $booking_details = [];

        foreach ($treatment->details as $detail) {
            $price = $detail->price;
            
            if ($detail->has_stylist_price) {
                if ($stylist && strtolower($stylist->kategori) == 'senior') {
                    $price = $detail->price_senior ?? $price;
                } elseif ($stylist && strtolower($stylist->kategori) == 'junior') {
                    $price = $detail->price_junior ?? $price;
                }
            }

            $booking_details[] = [
                'treatment_detail_id' => $detail->id,
                'price' => $price
            ];
            $total_price += $price;
        }

        $isStaff = in_array(Auth::user()->role, ['admin', 'karyawan']);
        $paymentStatus = ($isStaff && $request->payment_method === 'cash') ? 'paid' : 'unpaid';

        $booking = Booking::create([
            'user_id' => $isStaff ? null : Auth::id(),
            'customer_name' => $request->customer_name,
            'cashier_id' => $isStaff ? Auth::id() : null,
            'stylist_id' => $request->stylist_id,
            'treatment_id' => $request->treatment_id,
            'reservation_datetime' => Carbon::parse($request->reservation_date.' '.$request->reservation_time),
            'total_price' => $total_price,
            'status' => 'proses',
            'payment_status' => $paymentStatus,
            'payment_method' => $request->payment_method
        ]);

        foreach ($booking_details as $item) {
            BookingDetail::create([
                'booking_id' => $booking->id,
                'treatment_detail_id' => $item['treatment_detail_id'],
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
                    'email' => Auth::user()->email,
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
        $booking->status = 'confirmed';
        $booking->save();

        return redirect()->route('booking.history')->with('success','Pembayaran berhasil!');
    }

    // Riwayat booking
    public function history()
    {
        $user = Auth::user();
        $query = Booking::with(['treatment', 'stylist', 'cashier']);

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
        $inProcess = $allBookings->whereIn('status', ['proses', 'pending']);
        $history = $allBookings->whereIn('status', ['berhasil', 'dibatalkan']);

        return view('booking.history', compact('inProcess', 'history', 'allBookings'));
    }

    // Batalkan booking (Pelanggan)
    public function cancel(Request $request, $id)
    {
        $booking = Booking::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($booking->status !== 'proses' && $booking->status !== 'pending') {
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
        $status = $request->get('status', 'proses'); // Default to proses

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
            'proses' => Booking::where('status', 'proses')->count(),
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
            'status' => 'required|in:proses,berhasil,dibatalkan'
        ]);

        $booking->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status booking berhasil diperbarui.');
    }
}