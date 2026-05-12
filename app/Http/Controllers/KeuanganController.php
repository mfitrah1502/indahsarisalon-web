<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KeuanganController extends Controller
{
    public function pemasukan(Request $request)
    {
        $query = Booking::where('payment_status', 'paid')
                        ->where('status', '!=', 'dibatalkan');
                        
        if ($request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('reservation_datetime', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $pemasukan = $query->with('treatment')->orderBy('reservation_datetime', 'desc')->get();
        $totalPemasukan = $pemasukan->sum('total_price');

        // --- DYNAMIC CHART DATA ---
        $chartLabels = [];
        $chartData = [];
        $chartTitle = "Statistik Akumulasi Pemasukan (6 Bulan Terakhir)";

        if ($request->filled(['start_date', 'end_date'])) {
            $start = Carbon::parse($request->start_date);
            $end = Carbon::parse($request->end_date);
            $diffInDays = $start->diffInDays($end);
            $chartTitle = "Statistik Terfilter (" . $start->format('d M Y') . " - " . $end->format('d M Y') . ")";

            if ($diffInDays <= 31) {
                // Daily for short ranges
                $cumulative = 0;
                for ($d = 0; $d <= $diffInDays; $d++) {
                    $date = $start->copy()->addDays($d)->toDateString();
                    $chartLabels[] = $start->copy()->addDays($d)->format('d M');
                    $daily = Booking::whereDate('reservation_datetime', $date)
                                    ->where('payment_status', 'paid')
                                    ->where('status', '!=', 'dibatalkan')
                                    ->sum('total_price');
                    $cumulative += $daily;
                    $chartData[] = $cumulative;
                }
            } else {
                // Monthly for long ranges
                $cumulative = 0;
                $current = $start->copy()->startOfMonth();
                while ($current <= $end) {
                    $chartLabels[] = $current->format('M Y');
                    $monthly = Booking::whereYear('reservation_datetime', $current->year)
                                     ->whereMonth('reservation_datetime', $current->month)
                                     ->where('payment_status', 'paid')
                                     ->where('status', '!=', 'dibatalkan')
                                     ->sum('total_price');
                    $cumulative += $monthly;
                    $chartData[] = $cumulative;
                    $current->addMonth();
                }
            }
        } else {
            // Default 6 Months Cumulative
            $cumulativeTotal = Booking::where('reservation_datetime', '<', now()->subMonths(5)->startOfMonth())
                                     ->where('payment_status', 'paid')
                                     ->where('status', '!=', 'dibatalkan')
                                     ->sum('total_price');

            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $chartLabels[] = $month->format('M');
                $monthlyIncome = Booking::whereYear('reservation_datetime', $month->year)
                                     ->whereMonth('reservation_datetime', $month->month)
                                     ->where('payment_status', 'paid')
                                     ->where('status', '!=', 'dibatalkan')
                                     ->sum('total_price');
                $cumulativeTotal += $monthlyIncome;
                $chartData[] = $cumulativeTotal;
            }
        }

        $grandTotalPemasukan = Booking::where('payment_status', 'paid')->where('status', '!=', 'dibatalkan')->sum('total_price');
        $grandTotalPengeluaran = Expense::sum('amount');
        $grandProfit = $grandTotalPemasukan - $grandTotalPengeluaran;

        return view('keuangan.pemasukan', compact('pemasukan', 'totalPemasukan', 'chartLabels', 'chartData', 'chartTitle', 'grandTotalPemasukan', 'grandTotalPengeluaran', 'grandProfit'));
    }

    public function pengeluaran(Request $request)
    {
        $query = Expense::query();

        if ($request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('expense_date', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $pengeluaran = $query->orderBy('expense_date', 'desc')->get();
        $totalPengeluaran = $pengeluaran->sum('amount');

        $chartLabels = [];
        $chartData = [];
        $chartTitle = "Statistik Akumulasi Pengeluaran (6 Bulan Terakhir)";

        if ($request->filled(['start_date', 'end_date'])) {
            $start = Carbon::parse($request->start_date);
            $end = Carbon::parse($request->end_date);
            $diffInDays = $start->diffInDays($end);
            $chartTitle = "Statistik Terfilter (" . $start->format('d M Y') . " - " . $end->format('d M Y') . ")";

            if ($diffInDays <= 31) {
                // Daily for short ranges
                $cumulative = 0;
                for ($d = 0; $d <= $diffInDays; $d++) {
                    $date = $start->copy()->addDays($d)->toDateString();
                    $chartLabels[] = $start->copy()->addDays($d)->format('d M');
                    $daily = Expense::whereDate('expense_date', $date)->sum('amount');
                    $cumulative += $daily;
                    $chartData[] = $cumulative;
                }
            } else {
                // Monthly for long ranges
                $cumulative = 0;
                $current = $start->copy()->startOfMonth();
                while ($current <= $end) {
                    $chartLabels[] = $current->format('M Y');
                    $monthly = Expense::whereYear('expense_date', $current->year)
                                     ->whereMonth('expense_date', $current->month)
                                     ->sum('amount');
                    $cumulative += $monthly;
                    $chartData[] = $cumulative;
                    $current->addMonth();
                }
            }
        } else {
            // Default 6 Months Cumulative
            $cumulativeTotal = Expense::where('expense_date', '<', now()->subMonths(5)->startOfMonth())
                                     ->sum('amount');

            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $chartLabels[] = $month->format('M');
                $monthlyExpense = Expense::whereYear('expense_date', $month->year)
                                         ->whereMonth('expense_date', $month->month)
                                         ->sum('amount');
                $cumulativeTotal += $monthlyExpense;
                $chartData[] = $cumulativeTotal;
            }
        }

        $grandTotalPemasukan = Booking::where('payment_status', 'paid')->where('status', '!=', 'dibatalkan')->sum('total_price');
        $grandTotalPengeluaran = Expense::sum('amount');
        $grandProfit = $grandTotalPemasukan - $grandTotalPengeluaran;

        return view('keuangan.pengeluaran', compact('pengeluaran', 'totalPengeluaran', 'chartLabels', 'chartData', 'chartTitle', 'grandTotalPemasukan', 'grandTotalPengeluaran', 'grandProfit'));
    }

    public function exportProfitPdf()
    {
        $bookings = Booking::with('treatment')
                           ->where('payment_status', 'paid')
                           ->where('status', '!=', 'dibatalkan')
                           ->orderBy('reservation_datetime', 'asc')
                           ->get();

        $expenses = Expense::orderBy('expense_date', 'asc')->get();

        // Combine into a single history
        $history = [];
        foreach ($bookings as $b) {
            $history[] = [
                'date' => Carbon::parse($b->reservation_datetime)->format('d/m/Y'),
                'raw_date' => $b->reservation_datetime,
                'type' => 'Pemasukan',
                'description' => 'Layanan Salon (' . ($b->treatment->name ?? 'Treatment') . ')',
                'amount' => $b->total_price,
                'class' => 'text-success'
            ];
        }
        foreach ($expenses as $e) {
            $history[] = [
                'date' => Carbon::parse($e->expense_date)->format('d/m/Y'),
                'raw_date' => $e->expense_date,
                'type' => 'Pengeluaran',
                'description' => $e->category . ($e->description ? ' - ' . $e->description : ''),
                'amount' => $e->amount,
                'class' => 'text-danger'
            ];
        }

        // Sort history by raw_date ascending
        usort($history, function($a, $b) {
            return strtotime($a['raw_date']) - strtotime($b['raw_date']);
        });

        $grandTotalPemasukan = $bookings->sum('total_price');
        $grandTotalPengeluaran = $expenses->sum('amount');
        $grandProfit = $grandTotalPemasukan - $grandTotalPengeluaran;
        
        $data = [
            'history' => $history,
            'pemasukan' => $grandTotalPemasukan,
            'pengeluaran' => $grandTotalPengeluaran,
            'profit' => $grandProfit,
            'date' => now()->format('d M Y H:i')
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('keuangan.profit_pdf', $data);
        return $pdf->download('laporan-profit-salon.pdf');
    }

    public function storePengeluaran(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'expense_date' => 'required|date',
        ]);

        Expense::create([
            'category' => $request->category,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'description' => $request->description ?? null,
        ]);

        return redirect()->back()->with('success', 'Data pengeluaran berhasil ditambahkan.');
    }
}
