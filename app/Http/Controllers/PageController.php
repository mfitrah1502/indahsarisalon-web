<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $isStaff = in_array(strtolower($user->role), ['admin', 'karyawan']) || $user->type === 'karyawan';
        $promoTreatments = \App\Models\Treatment::where('is_promo', true)->with('details')->get();
        
        if (strtolower($user->role) === 'karyawan') {
            $today = now()->format('Y-m-d');
            $absensi = Absensi::where('user_id', $user->id)
                             ->where('tanggal', $today)
                             ->first();

            // Ambil ringkasan booking hari ini yang perlu diproses
            $todayBookings = Booking::whereDate('reservation_datetime', $today)
                                    ->where('status', 'pending')
                                    ->with(['treatment', 'user'])
                                    ->orderBy('reservation_datetime', 'asc')
                                    ->take(5)
                                    ->get();

            return view('dashboard.homepage-karyawan', compact('absensi', 'todayBookings', 'promoTreatments'));
        }

        if (strtolower($user->role) === 'admin') {
            // Stats untuk admin dashboard
            $stats = [
                'total_pelanggan' => \App\Models\User::where('role', 'pelanggan')->count(),
                'total_pemasukan' => Booking::where('payment_status', 'paid')
                                            ->where('status', 'berhasil')
                                            ->sum('total_price'),
                'today_bookings' => Booking::whereDate('reservation_datetime', now()->toDateString())->count(),
            ];

            // Data Pemasukan Bulanan untuk Chart
            $currentYear = now()->year;
            $monthlyIncome = [];
            for ($i = 1; $i <= 12; $i++) {
                $income = Booking::whereYear('reservation_datetime', $currentYear)
                                 ->whereMonth('reservation_datetime', $i)
                                 ->where('payment_status', 'paid')
                                 ->where('status', 'berhasil')
                                 ->sum('total_price');
                $monthlyIncome[] = $income;
            }

            return view('dashboard.homepage', compact('stats', 'monthlyIncome', 'currentYear', 'promoTreatments'));
        }

        // Dashboard untuk Pelanggan (User)
        $latestBooking = Booking::where('user_id', $user->id)
                                ->with(['treatment', 'stylist'])
                                ->latest()
                                ->first();

        $categories = \App\Models\Category::with(['treatments' => function($q) {
            $q->where('is_promo', false); // Optional: filter if needed
        }])->get();

        return view('dashboard.homepage-user', compact('latestBooking', 'categories', 'promoTreatments'));
    }
    public function landing()
    {
        // Jika user sudah login, langsung arahkan ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('landing'); // file: resources/views/landing.blade.php
    }

    public function about()
    {
        return view('about'); // file: resources/views/about.blade.php
    }
}
