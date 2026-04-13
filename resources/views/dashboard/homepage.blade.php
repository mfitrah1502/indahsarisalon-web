@extends('layout.dashboard')

@section('title', 'Admin Dashboard')

@push('styles')
    <style>
        .welcome-card {
            background: linear-gradient(135deg, #EA8290 0%, #f7a7b3 100%);
            border-radius: 20px;
            overflow: hidden;
            position: relative;
            color: white;
            border: none;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(234, 130, 144, 0.2);
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
            $hour = date('H');
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
        <div class="col-xl-4 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-icon icon-pelanggan">
                        <i class="ti ti-users"></i>
                    </div>
                    <div class="stat-value">{{ number_format($stats['total_pelanggan']) }}</div>
                    <div class="stat-label">Total Pelanggan</div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-icon icon-pemasukan">
                        <i class="ti ti-cash"></i>
                    </div>
                    <div class="stat-value">Rp {{ number_format($stats['total_pemasukan'], 0, ',', '.') }}</div>
                    <div class="stat-label">Total Pemasukan</div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-icon icon-bookings">
                        <i class="ti ti-calendar-check"></i>
                    </div>
                    <div class="stat-value">{{ number_format($stats['today_bookings']) }}</div>
                    <div class="stat-label">Booking Hari Ini</div>
                </div>
            </div>
        </div>

        <!-- Optional: Placeholder for Growth Chart if needed -->
        <div class="col-12 mt-4">
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Statistik Pertumbuhan</h5>
                    <select class="form-select w-auto border-0 bg-light small">
                        <option>This Month</option>
                        <option>This Year</option>
                    </select>
                </div>
                <div class="card-body px-4 pb-4">
                    <div id="growth-chart" style="min-height: 350px;">
                        <!-- ApexCharts will be rendered here -->
                        <div class="text-center py-5">
                            <i class="ti ti-chart-line text-muted f-30"></i>
                            <p class="text-muted mt-2">Data pertumbuhan akan segera tersedia.</p>
                        </div>
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
        // Simple placeholder for now to avoid errors if scripts refer to it
        document.addEventListener('DOMContentLoaded', function() {
            // Future chart initialization here
        });
    </script>
@endpush