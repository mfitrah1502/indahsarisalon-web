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
        $today = now()->toDateString();
        $promoTreatments = \App\Models\Treatment::whereHas('category', function($q) {
            $q->where('name', 'Promo');
        })->where('is_active', true)
          ->where(function($q) use ($today) {
              $q->whereNull('promo_end_date')->orWhere('promo_end_date', '>=', $today);
          })
          ->with('details')->get();
        
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
                                            ->where('status', '!=', 'dibatalkan')
                                            ->sum('total_price'),
                'today_bookings' => Booking::whereDate('reservation_datetime', now()->toDateString())->count(),
            ];

            // --- LOGIKA STATISTIK KEUANGAN DINAMIS ---
            $filter = request('chart_filter', 'monthly'); // default monthly
            $chartData = [];
            $chartLabels = [];
            $currentDate = now();

            if ($filter === 'daily') {
                // 7 Hari Terakhir
                for ($i = 6; $i >= 0; $i--) {
                    $date = now()->subDays($i)->toDateString();
                    $income = Booking::whereDate('reservation_datetime', $date)
                                     ->where('payment_status', 'paid')
                                     ->where('status', '!=', 'dibatalkan')
                                     ->sum('total_price');
                    $chartData[] = (int)$income;
                    $chartLabels[] = now()->subDays($i)->format('d M');
                }
                $chartTitle = "Statistik Pemasukan 7 Hari Terakhir";
            } elseif ($filter === 'weekly') {
                // 4 Minggu Terakhir
                for ($i = 3; $i >= 0; $i--) {
                    $start = now()->subWeeks($i)->startOfWeek();
                    $end = now()->subWeeks($i)->endOfWeek();
                    $income = Booking::whereBetween('reservation_datetime', [$start, $end])
                                     ->where('payment_status', 'paid')
                                     ->where('status', '!=', 'dibatalkan')
                                     ->sum('total_price');
                    $chartData[] = (int)$income;
                    $chartLabels[] = "Minggu " . ($i === 0 ? "Ini" : now()->subWeeks($i)->format('W'));
                }
                $chartTitle = "Statistik Pemasukan 4 Minggu Terakhir";
            } else {
                // Bulanan (Tahun Berjalan)
                $currentYear = now()->year;
                for ($i = 1; $i <= 12; $i++) {
                    $income = Booking::whereYear('reservation_datetime', $currentYear)
                                     ->whereMonth('reservation_datetime', $i)
                                     ->where('payment_status', 'paid')
                                     ->where('status', '!=', 'dibatalkan')
                                     ->sum('total_price');
                    $chartData[] = (int)$income;
                }
                $chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
                $chartTitle = "Statistik Pemasukan Tahun " . $currentYear;
            }

            if (request()->ajax()) {
                return response()->json([
                    'data' => $chartData,
                    'labels' => $chartLabels,
                    'title' => $chartTitle
                ]);
            }

            return view('dashboard.homepage', compact('stats', 'chartData', 'chartLabels', 'chartTitle', 'filter', 'promoTreatments'));
        }

        // Dashboard untuk Pelanggan (User)
        $latestBooking = Booking::where('user_id', $user->id)
                                ->with(['treatment', 'stylist'])
                                ->latest()
                                ->first();

        $categories = \App\Models\Category::where('name', '!=', 'Promo')->with(['treatments' => function($q) {
            $q->where('is_active', true);
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
