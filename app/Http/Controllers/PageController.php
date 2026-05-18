<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Booking;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PageController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $isStaff = in_array(strtolower($user->role), ['owner', 'admin']) || $user->type === 'karyawan';
        $today = now()->toDateString();
        $promoTreatments = \App\Models\Treatment::whereHas('category', function($q) {
            $q->where('name', 'Promo');
        })->where('is_active', true)
          ->where(function($q) use ($today) {
              $q->whereNull('promo_end_date')->orWhere('promo_end_date', '>=', $today);
          })
          ->with(['details.treatment.category'])->get();
        
        if ($user && $user->role === 'pelanggan') {
            $promoTreatments = $promoTreatments->filter(function($t) use ($user) {
                return $t->matchesUser($user);
            });
        }
        
        if (in_array(strtolower($user->role), ['admin', 'karyawan'])) {
            $today = now()->format('Y-m-d');
            $absensi = Absensi::where('user_id', $user->id)
                             ->where('tanggal', $today)
                             ->first();

            // Ambil ringkasan booking hari ini yang perlu diproses (Hanya untuk Admin)
            $todayBookings = collect();
            if (strtolower($user->role) === 'admin') {
                $todayBookings = Booking::whereDate('reservation_datetime', $today)
                                        ->where('status', 'pending')
                                        ->with(['treatment', 'user'])
                                        ->orderBy('reservation_datetime', 'asc')
                                        ->take(5)
                                        ->get();
            }

            return view('dashboard.homepage-karyawan', compact('absensi', 'todayBookings', 'promoTreatments'));
        }

        if (strtolower($user->role) === 'owner') {
            $grandTotalPemasukan = Booking::where('payment_status', 'paid')
                                            ->where('status', '!=', 'dibatalkan')
                                            ->sum('total_price');
            $grandTotalPengeluaran = Expense::sum('amount');

            // Hitung pelanggan terdaftar
            $registeredCount = \App\Models\User::where('role', 'pelanggan')->count();

            // Hitung pelanggan guest/offline unik dari bookings (sama seperti di PelangganController)
            $guestBookings = Booking::select('customer_name', 'customer_email', 'customer_phone')
                ->where(function($q) {
                    $q->whereNull('user_id')
                      ->orWhereHas('user', function($u) {
                          $u->where('role', '!=', 'pelanggan');
                      });
                })
                ->groupBy('customer_name', 'customer_email', 'customer_phone')
                ->get();

            $registeredEmails = \App\Models\User::where('role', 'pelanggan')->whereNotNull('email')->pluck('email')->toArray();
            $registeredPhones = \App\Models\User::where('role', 'pelanggan')->whereNotNull('phone')->pluck('phone')->toArray();
            $registeredNames = \App\Models\User::where('role', 'pelanggan')->pluck('name')->toArray();

            $guestCount = $guestBookings->filter(function($booking) use ($registeredEmails, $registeredPhones, $registeredNames) {
                if ($booking->customer_email && in_array($booking->customer_email, $registeredEmails)) return false;
                if ($booking->customer_phone && in_array($booking->customer_phone, $registeredPhones)) return false;
                if ($booking->customer_name && in_array($booking->customer_name, $registeredNames)) return false;
                return true;
            })->count();

            $totalPelangganCombined = $registeredCount + $guestCount;
            
            $stats = [
                'total_pelanggan' => $totalPelangganCombined,
                'total_pemasukan' => $grandTotalPemasukan,
                'total_pengeluaran' => $grandTotalPengeluaran,
                'profit' => $grandTotalPemasukan - $grandTotalPengeluaran,
                'today_bookings' => Booking::whereDate('reservation_datetime', now()->toDateString())->count(),
            ];

            // --- LOGIKA STATISTIK KEUANGAN DINAMIS ---
            $filter = request('chart_filter', 'monthly'); // default monthly
            $incomeData = [];
            $expenseData = [];
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
                    $expense = Expense::whereDate('expense_date', $date)->sum('amount');
                    
                    $incomeData[] = (int)$income;
                    $expenseData[] = (int)$expense;
                    $chartLabels[] = now()->subDays($i)->format('d M');
                }
                $chartTitle = "Statistik Keuangan 7 Hari Terakhir";
            } elseif ($filter === 'weekly') {
                // 4 Minggu Terakhir
                for ($i = 3; $i >= 0; $i--) {
                    $start = now()->subWeeks($i)->startOfWeek();
                    $end = now()->subWeeks($i)->endOfWeek();
                    $income = Booking::whereBetween('reservation_datetime', [$start, $end])
                                     ->where('payment_status', 'paid')
                                     ->where('status', '!=', 'dibatalkan')
                                     ->sum('total_price');
                    $expense = Expense::whereBetween('expense_date', [$start, $end])->sum('amount');

                    $incomeData[] = (int)$income;
                    $expenseData[] = (int)$expense;
                    $chartLabels[] = "Minggu " . ($i === 0 ? "Ini" : now()->subWeeks($i)->format('W'));
                }
                $chartTitle = "Statistik Keuangan 4 Minggu Terakhir";
            } else {
                // Bulanan (Tahun Berjalan)
                $currentYear = now()->year;
                for ($i = 1; $i <= 12; $i++) {
                    $income = Booking::whereYear('reservation_datetime', $currentYear)
                                     ->whereMonth('reservation_datetime', $i)
                                     ->where('payment_status', 'paid')
                                     ->where('status', '!=', 'dibatalkan')
                                     ->sum('total_price');
                    $expense = Expense::whereYear('expense_date', $currentYear)
                                     ->whereMonth('expense_date', $i)
                                     ->sum('amount');

                    $incomeData[] = (int)$income;
                    $expenseData[] = (int)$expense;
                }
                $chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
                $chartTitle = "Statistik Keuangan Tahun " . $currentYear;
            }

            if (request()->ajax()) {
                return response()->json([
                    'income' => $incomeData,
                    'expense' => $expenseData,
                    'labels' => $chartLabels,
                    'title' => $chartTitle
                ]);
            }

            return view('dashboard.homepage', compact('stats', 'incomeData', 'expenseData', 'chartLabels', 'chartTitle', 'filter', 'promoTreatments'));
        }

        // Dashboard untuk Pelanggan (User)
        $latestBooking = Booking::where('user_id', $user->id)
                                ->with(['treatment', 'stylist'])
                                ->latest()
                                ->first();

        $categories = \App\Models\Category::where('name', '!=', 'Promo')->with(['treatments' => function($q) {
            $q->where('is_active', true);
        }, 'treatments.details'])->get();

        if ($user && $user->role === 'pelanggan') {
            foreach ($categories as $category) {
                $category->setRelation('treatments', $category->treatments->filter(function($t) use ($user) {
                    return $t->matchesUser($user);
                }));
            }
        }

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
