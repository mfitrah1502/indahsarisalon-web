@extends('layout.dashboard')

@section('title', 'Status Pemesanan | Staf')
<style>
    .table > :not(caption) > * > * {
        padding: 1.25rem 1rem;
    }
    .badge {
        font-size: 0.85rem;
        padding: 0.5em 1em;
    }
    .dropdown-menu {
        border: none;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        border-radius: 12px;
        margin-top: 10px !important;
    }
    .fw-bold {
        font-size: 1.05rem;
    }
</style>

@section('content')
<div class="row mb-4">
    <!-- STATS CARDS -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 avatar-sm bg-white bg-opacity-25 rounded">
                        <i class="ti ti-calendar-event fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-white mb-0 opacity-75">Semua Pesanan</h6>
                        <h4 class="text-white mb-0">{{ $stats['total'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 avatar-sm bg-white bg-opacity-25 rounded">
                        <i class="ti ti-loader fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-dark mb-0 opacity-75">Perlu Diproses</h6>
                        <h4 class="text-dark mb-0">{{ $stats['proses'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 avatar-sm bg-white bg-opacity-25 rounded">
                        <i class="ti ti-check fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-white mb-0 opacity-75">Sudah Selesai</h6>
                        <h4 class="text-white mb-0">{{ $stats['berhasil'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-danger text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 avatar-sm bg-white bg-opacity-25 rounded">
                        <i class="ti ti-x fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-white mb-0 opacity-75">Dibatalkan</h6>
                        <h4 class="text-white mb-0">{{ $stats['dibatalkan'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom pt-3 pb-0">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">📋 Panel Operasional Booking</h4>
                    
                    <!-- DATE FILTER SECTION -->
                    <form action="{{ route('admin.bookings.index') }}" method="GET" id="filterForm" class="row g-2 align-items-center">
                        <input type="hidden" name="status" value="{{ request('status', 'proses') }}">
                        <div class="col-auto">
                            <select name="filter_mode" id="filterMode" class="form-select form-select-sm shadow-none">
                                <option value="all" {{ request('filter_mode') == 'all' ? 'selected' : '' }}>Semua Riwayat</option>
                                <option value="daily" {{ request('filter_mode') == 'daily' ? 'selected' : '' }}>Harian</option>
                                <option value="monthly" {{ request('filter_mode') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                                <option value="yearly" {{ request('filter_mode') == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                            </select>
                        </div>
                        <div class="col-auto" id="filterInputCol" style="{{ in_array(request('filter_mode'), ['daily','monthly','yearly']) ? '' : 'display:none;' }}">
                            @if(request('filter_mode') == 'daily')
                                <input type="date" name="filter_value" class="form-control form-control-sm" value="{{ request('filter_value') }}">
                            @elseif(request('filter_mode') == 'monthly')
                                <input type="month" name="filter_value" class="form-control form-control-sm" value="{{ request('filter_value') }}">
                            @elseif(request('filter_mode') == 'yearly')
                                <input type="number" name="filter_value" class="form-control form-control-sm" placeholder="Tahun" min="2020" max="2030" value="{{ request('filter_value') }}">
                            @endif
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary btn-sm px-3"><i class="ti ti-search me-1"></i>Filter</button>
                            <a href="{{ route('admin.bookings.index', ['status' => request('status', 'proses')]) }}" class="btn btn-light-secondary btn-sm px-3"><i class="ti ti-refresh me-1"></i>Reset</a>
                        </div>
                    </form>
                </div>
                
                <!-- TABS -->
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ $status == 'proses' ? 'active font-weight-bold' : '' }}" 
                           href="{{ route('admin.bookings.index', ['status' => 'proses']) }}">
                            ⏳ Pending / Proses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $status == 'berhasil' ? 'active font-weight-bold' : '' }}" 
                           href="{{ route('admin.bookings.index', ['status' => 'berhasil']) }}">
                            ✅ Selesai (Berhasil)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $status == 'dibatalkan' ? 'active font-weight-bold' : '' }}" 
                           href="{{ route('admin.bookings.index', ['status' => 'dibatalkan']) }}">
                            ❌ Dibatalkan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $status == 'all' ? 'active font-weight-bold' : '' }}" 
                           href="{{ route('admin.bookings.index', ['status' => 'all']) }}">
                            Semua
                        </a>
                    </li>
                </ul>
            </div>

            @if(session('success'))
                <div class="alert alert-success m-3">{{ session('success') }}</div>
            @endif

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Informasi Pelanggan</th>
                                <th>Layanan</th>
                                <th>Waktu & Petugas</th>
                                <th>Biaya & Bayar</th>
                                <th>Status Bayar</th>
                                <th class="text-center">Aksi Staf</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $index => $booking)
                                <tr>
                                    <td>{{ $bookings->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-dark">{{ $booking->customer_name }}</span>
                                            @if($booking->user)
                                                <small class="text-muted"><i class="ti ti-mail me-1"></i>{{ $booking->user->email }}</small>
                                            @else
                                                <small class="text-muted text-uppercase" style="font-size: 0.7rem;"><i class="ti ti-user me-1"></i>OFFLINE CUSTOMER</small>
                                            @endif
                                            @if($booking->cashier)
                                                <small class="text-success"><i class="ti ti-id me-1"></i>Oleh: {{ $booking->cashier->name }}</small>
                                            @endif
                                            <small class="text-info">#BOOK-{{ $booking->id }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-primary fw-bold">{{ $booking->treatment->name }}</span>
                                            <small class="text-muted">{{ $booking->details->count() }} Detail</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span><i class="ti ti-clock me-1 text-warning"></i>{{ \Carbon\Carbon::parse($booking->reservation_datetime)->format('d M Y, H:i') }}</span>
                                            <small class="text-muted"><i class="ti ti-user me-1 text-secondary"></i>Stylist: {{ $booking->stylist->name ?? 'None' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold">Rp {{ number_format($booking->total_price, 0) }}</span>
                                            <span class="badge {{ $booking->payment_method == 'transfer' ? 'bg-light-info' : 'bg-light-secondary' }} text-dark mt-1">
                                                <i class="ti ti-{{ $booking->payment_method == 'transfer' ? 'credit-card' : 'wallet' }} me-1"></i>
                                                {{ ucfirst($booking->payment_method) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $payClass = [
                                                'paid' => 'bg-success',
                                                'pending' => 'bg-warning text-dark',
                                                'failed' => 'bg-danger'
                                            ][$booking->payment_status] ?? 'bg-secondary';
                                        @endphp
                                        <span class="badge {{ $payClass }} rounded-pill px-3">
                                            {{ ucfirst($booking->payment_status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-primary border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Update Status
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                <li class="dropdown-header">Ubah Status Pengerjaan</li>
                                                <li>
                                                    <form action="{{ route('admin.bookings.updateStatus', $booking->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="proses">
                                                        <button type="submit" class="dropdown-item">⏳ Sedang Proses</button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.bookings.updateStatus', $booking->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="berhasil">
                                                        <button type="submit" class="dropdown-item text-success">✅ Selesai</button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.bookings.updateStatus', $booking->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="dibatalkan">
                                                        <button type="submit" class="dropdown-item text-danger">❌ Batalkan</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="ti ti-clipboard-x fs-1 opacity-25"></i>
                                            <p class="mt-2">Belum ada pemesanan dalam kategori ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $bookings->appends(['status' => $status])->links() }}
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script>
    // Initial Config (Safe Check)
    if (typeof layout_change === 'function') {
        layout_change('light');
        font_change('Roboto');
        change_box_container('false');
        layout_caption_change('true');
        layout_rtl_change('false');
        preset_change('preset-1');
    }

    $(document).ready(function() {
        $('#filterMode').on('change', function() {
            const mode = $(this).val();
            const $container = $('#filterInputCol');
            
            if (mode === 'all') {
                $container.hide().empty();
                $('#filterForm').submit();
            } else {
                $container.show();
                let inputHtml = '';
                if (mode === 'daily') {
                    inputHtml = '<input type="date" name="filter_value" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">';
                } else if (mode === 'monthly') {
                    inputHtml = '<input type="month" name="filter_value" class="form-control form-control-sm" value="{{ date('Y-m') }}">';
                } else if (mode === 'yearly') {
                    inputHtml = '<input type="number" name="filter_value" class="form-control form-control-sm" placeholder="Tahun" min="2020" max="2030" value="{{ date('Y') }}">';
                }
                $container.html(inputHtml);
            }
        });
    });
</script>
@endpush

@endsection
