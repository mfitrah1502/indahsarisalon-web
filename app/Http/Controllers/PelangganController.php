<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'pelanggan');

        // fitur search
        if($request->has('search') && $request->search != ''){
            $query->where(function($q) use ($request){
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('username', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $registeredPelanggans = $query->get();

        // Get guest customers from bookings (including those mistakenly assigned to staff IDs)
        $guestBookingsQuery = \App\Models\Booking::selectRaw('MAX(id) as id, customer_name, customer_email, customer_phone, MAX(created_at) as last_transaction_at, SUM(total_price) as total_spending')
            ->where(function($q) {
                $q->whereNull('user_id')
                  ->orWhereHas('user', function($u) {
                      $u->where('role', '!=', 'pelanggan');
                  });
            })
            ->where('status', '!=', 'dibatalkan')
            ->groupBy('customer_name', 'customer_email', 'customer_phone');

        if($request->has('search') && $request->search != ''){
            $search = $request->search;
            $guestBookingsQuery->where(function($q) use ($search){
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $guestBookings = $guestBookingsQuery->get();

        // Filter out guests that actually belong to registered users (matched by email/phone/name)
        $registeredEmails = User::where('role', 'pelanggan')->whereNotNull('email')->pluck('email')->toArray();
        $registeredPhones = User::where('role', 'pelanggan')->whereNotNull('phone')->pluck('phone')->toArray();
        $registeredNames = User::where('role', 'pelanggan')->pluck('name')->toArray();

        $guestPelanggans = $guestBookings->filter(function($booking) use ($registeredEmails, $registeredPhones, $registeredNames) {
            if ($booking->customer_email && in_array($booking->customer_email, $registeredEmails)) return false;
            if ($booking->customer_phone && in_array($booking->customer_phone, $registeredPhones)) return false;
            if ($booking->customer_name && in_array($booking->customer_name, $registeredNames)) return false;
            return true;
        })->map(function($booking) {
            $user = new User();
            $user->incrementing = false;
            $user->keyType = 'string';
            $user->id = 'guest-' . $booking->id;
            $user->name = $booking->customer_name;
            $user->username = 'Guest';
            $user->email = $booking->customer_email ?? '-';
            $user->phone = $booking->customer_phone ?? '-';
            $user->role = 'pelanggan';
            $user->status = 'guest';
            $user->setAttribute('cached_total_spending', $booking->total_spending);
            $user->setAttribute('last_transaction_at', $booking->last_transaction_at);
            return $user;
        });

        $pelanggans = $registeredPelanggans->concat($guestPelanggans)->sortByDesc('created_at')->values();

        return view('pelanggan.index', compact('pelanggans'));
    }
    public function create()
    {
        return view('pelanggan.create'); // form tambah pelanggan
    }
    public function edit($id)
    {
        if (strpos($id, 'guest-') === 0) {
            $bookingId = str_replace('guest-', '', $id);
            $booking = \App\Models\Booking::findOrFail($bookingId);
            
            $pelanggan = new User();
            $pelanggan->incrementing = false;
            $pelanggan->keyType = 'string';
            $pelanggan->id = $id;
            $pelanggan->name = $booking->customer_name;
            $pelanggan->username = 'Guest';
            $pelanggan->email = $booking->customer_email ?? '-';
            $pelanggan->phone = $booking->customer_phone ?? '-';
            $pelanggan->role = 'pelanggan';
            $pelanggan->status = 'guest';
            
            return view('pelanggan.edit', compact('pelanggan'));
        }

        $pelanggan = User::findOrFail($id);
        return view('pelanggan.edit', compact('pelanggan'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|string|max:15',
            'password' => 'required|string|min:6|confirmed',
            'status'   => 'required|in:aktif,tidak',
        ]);

        User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => 'pelanggan', // selalu pelanggan
            'type'     => 'pelanggan',
            'status'   => $request->status,
        ]);

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan');
    }
    public function update(Request $request, $id)
    {
        if (strpos($id, 'guest-') === 0) {
            $request->validate([
                'name'  => 'required|string|max:255',
                'email' => 'nullable|email',
                'phone' => 'required|string|max:15',
            ]);

            $bookingId = str_replace('guest-', '', $id);
            $originBooking = \App\Models\Booking::findOrFail($bookingId);

            $bookingsQuery = \App\Models\Booking::where(function($q) use ($originBooking) {
                if (!empty($originBooking->customer_email) && $originBooking->customer_email !== '-') {
                    $q->orWhere('customer_email', $originBooking->customer_email);
                }
                if (!empty($originBooking->customer_phone) && $originBooking->customer_phone !== '-') {
                    $q->orWhere('customer_phone', $originBooking->customer_phone);
                }
                if (!empty($originBooking->customer_name) && $originBooking->customer_name !== '-') {
                    $q->orWhere('customer_name', $originBooking->customer_name);
                }
            });

            $bookingsQuery->update([
                'customer_name' => $request->name,
                'customer_email' => $request->email ?? '-',
                'customer_phone' => $request->phone,
            ]);

            return redirect()->route('pelanggan.index')->with('success', 'Pelanggan Guest berhasil diupdate');
        }

        $pelanggan = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $pelanggan->id,
            'email'    => 'required|email|unique:users,email,' . $pelanggan->id,
            'phone'    => 'required|string|max:15',
            'password' => 'nullable|string|min:6|confirmed',
            'status'   => 'required|in:aktif,tidak',
        ]);

        $pelanggan->name     = $request->name;
        $pelanggan->username = $request->username;
        $pelanggan->email    = $request->email;
        $pelanggan->phone    = $request->phone;
        $pelanggan->status   = $request->status;
        $pelanggan->type     = 'pelanggan';

        if ($request->password) {
            $pelanggan->password = Hash::make($request->password);
        }

        $pelanggan->save();

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil diupdate');
    }

    // public function show($id)
    // {
    //     $pelanggan = User::where('role','pelanggan')->findOrFail($id);

    //     return view('pelanggan.show', compact('pelanggan'));
    // }

    public function destroy($id)
    {
        if (strpos($id, 'guest-') === 0) {
            $bookingId = str_replace('guest-', '', $id);
            $originBooking = \App\Models\Booking::findOrFail($bookingId);

            $bookingsQuery = \App\Models\Booking::where(function($q) use ($originBooking) {
                if (!empty($originBooking->customer_email) && $originBooking->customer_email !== '-') {
                    $q->orWhere('customer_email', $originBooking->customer_email);
                }
                if (!empty($originBooking->customer_phone) && $originBooking->customer_phone !== '-') {
                    $q->orWhere('customer_phone', $originBooking->customer_phone);
                }
                if (!empty($originBooking->customer_name) && $originBooking->customer_name !== '-') {
                    $q->orWhere('customer_name', $originBooking->customer_name);
                }
            });

            $bookingsQuery->delete();

            return redirect()->route('pelanggan.index')
                ->with('success', 'Pelanggan Guest dan seluruh riwayat pemesanannya berhasil dihapus');
        }

        $pelanggan = User::findOrFail($id);

        // Unlink bookings to prevent foreign key violation and keep transaction history
        \App\Models\Booking::where('user_id', $pelanggan->id)->update(['user_id' => null]);

        $pelanggan->delete();

        return redirect()->route('pelanggan.index')
            ->with('success','Pelanggan berhasil dihapus');
    }

    public function filter(Request $request)
    {
        $query = User::where('role', 'pelanggan');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('username', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $pelanggans = $query->get();

        return view('pelanggan.table', compact('pelanggans'));
    }

    public function history($id)
    {
        if (strpos($id, 'guest-') === 0) {
            $bookingId = str_replace('guest-', '', $id);
            $originBooking = \App\Models\Booking::find($bookingId);
            
            if (!$originBooking) {
                $bookings = collect();
            } else {
                $bookings = \App\Models\Booking::where(function($q) use ($originBooking) {
                    if (!empty($originBooking->customer_email) && $originBooking->customer_email !== '-') {
                        $q->orWhere('customer_email', $originBooking->customer_email);
                    }
                    if (!empty($originBooking->customer_phone) && $originBooking->customer_phone !== '-') {
                        $q->orWhere('customer_phone', $originBooking->customer_phone);
                    }
                    if (!empty($originBooking->customer_name) && $originBooking->customer_name !== '-') {
                        $q->orWhere('customer_name', $originBooking->customer_name);
                    }
                })
                ->where('status', '!=', 'dibatalkan')
                ->orderBy('reservation_datetime', 'desc')
                ->get();
            }
        } else {
            $pelanggan = User::findOrFail($id);
            $bookings = $pelanggan->getAllBookingsQuery()
                ->where('status', 'success')
                ->orderBy('reservation_datetime', 'desc')
                ->get();
        }
            
        return view('pelanggan.history_table', compact('bookings'));
    }
}