@extends('layout.dashboard')

@section('title', 'Admin Dashboard')

@push('styles')
    <style>
        .welcome-card {
            background: linear-gradient(45deg, #f7a7b3, #EA8290, #962536, #EA8290);
            background-size: 300% 300%;
            animation: gradientAnimation 8s ease infinite;
            border-radius: 20px;
            overflow: hidden;
            position: relative;
            color: white;
            border: none;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(234, 130, 144, 0.3);
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .welcome-content {
            padding: 40px;
            position: relative;
            z-index: 2;
        }

        .welcome-img {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 40%;
            background-image: url('{{ asset('storage/luxury_salon_dashboard_welcome.png') }}'); /* Replace with actual path or placeholder */
            background-size: cover;
            background-position: center;
            opacity: 0.3;
            mask-image: linear-gradient(to left, rgba(0, 0, 0, 1) 60%, rgba(0, 0, 0, 0));
        }

        .stat-card {
            border: none;
            border-radius: 20px;
            background: #ffffff;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        .icon-pelanggan { background: #E3F2FD; color: #2196F3; }
        .icon-pemasukan { background: #E8F5E9; color: #4CAF50; }
        .icon-bookings { background: #FFF3E0; color: #FF9800; }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
            color: #333;
        }

        .stat-label {
            color: #888;
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Greeting Section */
        @php
            $hour = now()->hour;
            if ($hour >= 5 && $hour < 11) {
                $greeting = 'Selamat Pagi';
            } elseif ($hour >= 11 && $hour < 15) {
                $greeting = 'Selamat Siang';
            } elseif ($hour >= 15 && $hour < 18) {
                $greeting = 'Selamat Sore';
            } else {
                $greeting = 'Selamat Malam';
            }
        @endphp
    </style>
@endpush

@section('content')
    <div class="row">
        <!-- Welcome Hero Section -->
        <div class="col-12">
            <div class="card welcome-card">
                <div class="welcome-img"></div>
                <div class="welcome-content">
                    <h1 class="text-white mb-2 fw-bold">{{ $greeting }}, {{ Auth::user()->name }}!</h1>
                    <p class="text-white opacity-75 mb-0" style="max-width: 500px;">
                        Senang melihat Anda kembali. Berikut adalah ringkasan performa Indah Sari Salon hari ini. Tetap berikan layanan terbaik untuk pelanggan kita!
                    </p>
                </div>
            </div>
        </div>

        <!-- Dashboard Stats -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="stat-icon icon-pelanggan">
                        <i class="ti ti-users"></i>
                    </div>
                    <div class="stat-value">{{ number_format($stats['total_pelanggan']) }}</div>
                    <div class="stat-label">Total Pelanggan</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card h-100 border-start border-4 border-success">
                <div class="card-body">
                    <div class="stat-icon icon-pemasukan">
                        <i class="ti ti-cash"></i>
                    </div>
                    <div class="stat-value text-success">Rp {{ number_format($stats['total_pemasukan'], 0, ',', '.') }}</div>
                    <div class="stat-label">Total Pemasukan</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card h-100 border-start border-4 border-danger">
                <div class="card-body">
                    <div class="stat-icon bg-light-danger text-danger" style="width: 50px; height: 50px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 20px;">
                        <i class="ti ti-receipt-off"></i>
                    </div>
                    <div class="stat-value text-danger">Rp {{ number_format($stats['total_pengeluaran'], 0, ',', '.') }}</div>
                    <div class="stat-label">Total Pengeluaran</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card h-100 border-start border-4 border-primary">
                <div class="card-body">
                    <div class="stat-icon bg-light-primary text-primary" style="width: 50px; height: 50px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 20px;">
                        <i class="ti ti-chart-pie"></i>
                    </div>
                    <div class="stat-value text-primary">Rp {{ number_format($stats['profit'], 0, ',', '.') }}</div>
                    <div class="stat-label">Profit Bersih</div>
                </div>
            </div>
        </div>

        <div class="col-12 mb-4">
            <div class="card stat-card py-2">
                <div class="card-body d-flex align-items-center gap-4">
                    <div class="stat-icon icon-bookings mb-0">
                        <i class="ti ti-calendar-check"></i>
                    </div>
                    <div>
                        <div class="stat-value mb-0">{{ number_format($stats['today_bookings']) }}</div>
                        <div class="stat-label">Booking Hari Ini</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Optional: Placeholder for Growth Chart if needed -->
        <div class="col-12 mt-4">
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold" id="chart-title-text">{{ $chartTitle }}</h5>
                    <div class="btn-group btn-group-sm rounded-pill p-1 bg-light">
                        <button onclick="updateChartFilter('daily', this)" 
                           class="btn filter-btn {{ $filter === 'daily' ? 'btn-primary shadow-sm' : 'btn-light border-0' }} rounded-pill px-3">Harian</button>
                        <button onclick="updateChartFilter('weekly', this)" 
                           class="btn filter-btn {{ $filter === 'weekly' ? 'btn-primary shadow-sm' : 'btn-light border-0' }} rounded-pill px-3">Mingguan</button>
                        <button onclick="updateChartFilter('monthly', this)" 
                           class="btn filter-btn {{ $filter === 'monthly' ? 'btn-primary shadow-sm' : 'btn-light border-0' }} rounded-pill px-3">Bulanan</button>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <div id="growth-chart" style="min-height: 350px;">
                        <!-- ApexCharts will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Apex Chart could be re-added here if needed -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        let chart;
        document.addEventListener('DOMContentLoaded', function() {
            var options = {
                series: [{
                    name: 'Pemasukan (Rp)',
                    data: {!! json_encode($incomeData) !!}
                }, {
                    name: 'Pengeluaran (Rp)',
                    data: {!! json_encode($expenseData) !!}
                }],
                chart: {
                    type: 'area',
                    height: 350,
                    toolbar: { show: false },
                    fontFamily: "'Inter', sans-serif"
                },
                colors: ['#EA8290', '#4680ff'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                xaxis: {
                    categories: {!! json_encode($chartLabels) !!},
                },
                yaxis: {
                    labels: {
                        formatter: function (val) {
                            return "Rp " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                },
                tooltip: {
                    theme: 'light',
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function (val) {
                            return "Rp " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
                    },
                    custom: function({series, seriesIndex, dataPointIndex, w}) {
                        let income = series[0][dataPointIndex];
                        let expense = series[1][dataPointIndex];
                        let profit = income - expense;
                        let profitColor = profit >= 0 ? '#4CAF50' : '#F44336';
                        
                        return '<div class="p-3">' +
                            '<div class="fw-bold mb-2">' + w.globals.categoryLabels[dataPointIndex] + '</div>' +
                            '<div class="d-flex justify-content-between mb-1"><span>Pemasukan:</span> <span class="fw-bold text-pink ms-3">Rp ' + income.toLocaleString('id-ID') + '</span></div>' +
                            '<div class="d-flex justify-content-between mb-1"><span>Pengeluaran:</span> <span class="fw-bold text-primary ms-3">Rp ' + expense.toLocaleString('id-ID') + '</span></div>' +
                            '<hr class="my-2">' +
                            '<div class="d-flex justify-content-between fw-bold"><span>Profit:</span> <span style="color:' + profitColor + '">Rp ' + profit.toLocaleString('id-ID') + '</span></div>' +
                            '</div>';
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.5,
                        opacityTo: 0.1,
                        stops: [0, 90, 100]
                    }
                }
            };

            chart = new ApexCharts(document.querySelector("#growth-chart"), options);
            chart.render();
        });

        window.updateChartFilter = function(mode, btn) {
            // Update button styles
            document.querySelectorAll('.filter-btn').forEach(b => {
                b.classList.remove('btn-primary', 'shadow-sm');
                b.classList.add('btn-light', 'border-0');
            });
            btn.classList.remove('btn-light', 'border-0');
            btn.classList.add('btn-primary', 'shadow-sm');

            // Fetch new data
            fetch(`{{ route('dashboard') }}?chart_filter=${mode}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update title
                document.getElementById('chart-title-text').innerText = data.title;
                
                // Update chart
                chart.updateOptions({
                    xaxis: {
                        categories: data.labels
                    },
                    series: [{
                        name: 'Pemasukan (Rp)',
                        data: data.income
                    }, {
                        name: 'Pengeluaran (Rp)',
                        data: data.expense
                    }]
                });
            })
            .catch(error => console.error('Error fetching chart data:', error));
        };
    </script>
@endpush