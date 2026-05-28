@extends('layout.dashboard')

@section('title', 'Manajemen Booking')

<style>
    .booking-card-table {
        border-collapse: separate;
        border-spacing: 0 12px;
    }
    .booking-card-table tr {
        background: #fff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        border-radius: 12px;
        transition: all 0.2s ease;
    }
    .booking-card-table tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }
    .booking-card-table td {
        padding: 1.2rem 1rem !important;
        vertical-align: middle;
        border: none !important;
    }
    .booking-card-table td:first-child { border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
    .booking-card-table td:last-child { border-top-right-radius: 12px; border-bottom-right-radius: 12px; }

    .action-btn {
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        transition: all 0.2s;
    }
    .badge-status {
        padding: 6px 14px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.75rem;
    }
    /* Pink Branding */
    .text-pink { color: #EA8290 !important; }
    .bg-pink { background-color: #EA8290 !important; }
    .btn-pink { background: #EA8290 !important; color: #fff !important; border: none; }
    .btn-light-pink { background: #fcebed !important; color: #EA8290 !important; border: none; }
    
    .modal-detail-item {
        padding: 12px;
        border-bottom: 1px solid #f1f1f1;
    }
    .modal-detail-item:last-child { border-bottom: none; }

    /* Fix SweetAlert2 appearing behind modal */
    .swal2-container {
        z-index: 9999 !important;
    }
</style>

@section('content')
<div class="row mb-4">
    <!-- STATS CARDS -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-pink text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 avatar-sm bg-white bg-opacity-25 rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="ti ti-calendar-event fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-white mb-0 opacity-75 small">Total Booking</h6>
                        <h4 class="text-white mb-0 fw-bold">{{ $stats['total'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 avatar-sm bg-white bg-opacity-25 rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="ti ti-loader fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-dark mb-0 opacity-75 small">Pending</h6>
                        <h4 class="text-dark mb-0 fw-bold">{{ $stats['pending'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 avatar-sm bg-white bg-opacity-25 rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="ti ti-check fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-white mb-0 opacity-75 small">Selesai / Berhasil</h6>
                        <h4 class="text-white mb-0 fw-bold">{{ $stats['success'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-danger text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 avatar-sm bg-white bg-opacity-25 rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="ti ti-x fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-white mb-0 opacity-75 small">Dibatalkan</h6>
                        <h4 class="text-white mb-0 fw-bold">{{ $stats['dibatalkan'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-4 overlay-hidden">
            <div class="card-header bg-white border-bottom pt-4 pb-0">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 px-2">
                    <div>
                        <h4 class="fw-bold text-dark mb-1"><i class="ti ti-clipboard-list me-2 text-pink"></i>Manajemen Status Pemesanan</h4>
                        <p class="text-muted small mb-0">Pantau dan update status layanan pelanggan secara real-time.</p>
                    </div>
                    
                    <!-- DATE FILTER SECTION -->
                    <form action="{{ route('admin.bookings.index') }}" method="GET" id="filterForm" class="row g-2 align-items-center">
                        <input type="hidden" name="status" value="{{ request('status', 'pending') }}">
                        <div class="col-auto">
                            <select name="filter_mode" id="filterMode" class="form-select form-select-sm border-light shadow-none rounded-pill px-3">
                                <option value="all" {{ request('filter_mode') == 'all' ? 'selected' : '' }}>Semua Riwayat</option>
                                <option value="daily" {{ request('filter_mode') == 'daily' ? 'selected' : '' }}>Harian</option>
                                <option value="monthly" {{ request('filter_mode') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                                <option value="yearly" {{ request('filter_mode') == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                            </select>
                        </div>
                        <div class="col-auto" id="filterInputCol" style="{{ in_array(request('filter_mode'), ['daily','monthly','yearly']) ? '' : 'display:none;' }}">
                            @if(request('filter_mode') == 'daily')
                                <input type="date" name="filter_value" class="form-control form-control-sm rounded-pill" value="{{ request('filter_value') }}">
                            @elseif(request('filter_mode') == 'monthly')
                                <input type="month" name="filter_value" class="form-control form-control-sm rounded-pill" value="{{ request('filter_value') }}">
                            @elseif(request('filter_mode') == 'yearly')
                                <input type="number" name="filter_value" class="form-control form-control-sm rounded-pill" placeholder="Tahun" min="2020" max="2030" value="{{ request('filter_value') }}">
                            @endif
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-pink btn-sm rounded-pill px-3"><i class="ti ti-search me-1"></i>Filter</button>
                            <a href="{{ route('admin.bookings.index', ['status' => request('status', 'pending')]) }}" class="btn btn-light btn-sm rounded-pill px-3 border"><i class="ti ti-refresh me-1"></i>Reset</a>
                        </div>
                    </form>
                </div>
                
                <!-- MODERN PILL TABS -->
                <div class="px-3 pb-3 mt-2">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}" 
                           class="btn rounded-pill px-4 py-2 {{ $status == 'pending' ? 'btn-warning text-dark fw-bold shadow-sm' : 'btn-light text-muted border' }}">
                            <i class="ti ti-clock me-1"></i> Pending
                        </a>
                        <a href="{{ route('admin.bookings.index', ['status' => 'success']) }}" 
                           class="btn rounded-pill px-4 py-2 {{ $status == 'success' ? 'btn-success text-white fw-bold shadow-sm' : 'btn-light text-muted border' }}">
                            <i class="ti ti-circle-check me-1"></i> Selesai (Berhasil)
                        </a>
                        <a href="{{ route('admin.bookings.index', ['status' => 'dibatalkan']) }}" 
                           class="btn rounded-pill px-4 py-2 {{ $status == 'dibatalkan' ? 'btn-danger text-white fw-bold shadow-sm' : 'btn-light text-muted border' }}">
                            <i class="ti ti-circle-x me-1"></i> Dibatalkan
                        </a>
                        <a href="{{ route('admin.bookings.index', ['status' => 'all']) }}" 
                           class="btn rounded-pill px-4 py-2 {{ $status == 'all' ? 'btn-dark text-white fw-bold shadow-sm' : 'btn-light text-muted border' }}">
                            <i class="ti ti-layout-list me-1"></i> Semua
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body p-4 bg-light bg-opacity-50">
                <div class="table-responsive">
                    <table class="table booking-card-table align-middle">
                        <thead>
                            <tr class="bg-transparent shadow-none">
                                <th class="text-muted small fw-bold px-3 py-2">LOG BOOKING</th>
                                <th class="text-muted small fw-bold py-2 text-center">LAYANAN UTAMA</th>
                                <th class="text-muted small fw-bold py-2 text-center">WAKTU</th>
                                <th class="text-muted small fw-bold py-2 text-center">TOTAL</th>
                                <th class="text-muted small fw-bold py-2 text-center">PEMBAYARAN</th>
                                <th class="text-muted small fw-bold py-2 text-end px-3">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                                <tr class="booking-row" data-id="{{ $booking->id }}">
                                    <td class="px-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light-pink rounded-pill d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <i class="ti ti-user text-pink fs-4"></i>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold text-dark">{{ $booking->customer_name }}</span>
                                                <div class="d-flex flex-column extra-small">
                                                    <span class="text-muted"><i class="ti ti-mail me-1"></i>{{ $booking->customer_email ?? '-' }}</span>
                                                    <span class="text-muted"><i class="ti ti-brand-whatsapp me-1"></i>{{ $booking->customer_phone ?? '-' }}</span>
                                                </div>
                                                <small class="text-info mt-1" style="font-size: 0.75rem;">#BOOK-{{ $booking->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex flex-column">
                                            <span class="text-dark fw-bold">{{ $booking->treatment->name ?? 'Layanan Tidak Diketahui' }}</span>
                                            <small class="text-muted">{{ $booking->details->count() }} Sub-Layanan</small>
                                        </div>
                                    </td>
                                    <td class="text-center" style="min-width: 160px;">
                                        <div class="d-flex flex-column align-items-center text-center gap-2">
                                            <div class="small" title="Tanggal Reservasi">
                                                <span class="badge bg-light-primary text-primary px-2 py-0.5" style="font-size: 0.6rem;"><i class="ti ti-calendar-event me-1"></i>Reservasi:</span>
                                                <div class="fw-bold text-dark small mt-0.5">{{ \Carbon\Carbon::parse($booking->reservation_datetime)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($booking->reservation_datetime)->format('H:i') }}</div>
                                            </div>
                                            <div class="small" title="Tanggal Transaksi">
                                                <span class="badge bg-light-secondary text-secondary px-2 py-0.5" style="font-size: 0.6rem;"><i class="ti ti-receipt me-1"></i>Transaksi:</span>
                                                <div class="text-muted small mt-0.5">{{ \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($booking->created_at)->format('H:i') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-dark">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $payBadge = [
                                                'paid' => 'bg-success',
                                                'unpaid' => 'bg-danger text-white',
                                                'pending' => 'bg-warning text-dark',
                                                'failed' => 'bg-danger'
                                            ][$booking->payment_status] ?? 'bg-secondary';
                                        @endphp
                                        <div class="d-flex flex-column align-items-center">
                                            <!-- <span class="badge {{ $payBadge }} rounded-pill px-3 mb-1" style="font-size: 0.7rem;">
                                                {{ $booking->payment_status === 'unpaid' ? 'BELUM BAYAR' : strtoupper($booking->payment_status) }}
                                            </span> -->
                                            <small class="text-muted" style="font-size: 0.65rem;">
                                                <i class="ti ti-{{ strtolower($booking->payment_method) == 'transfer' ? 'credit-card' : (strtolower($booking->payment_method) == 'qris' ? 'qrcode' : 'wallet') }} me-1"></i>{{ ucfirst($booking->payment_method) }}
                                            </small>
                                        </div>
                                    </td>
                                    <td class="text-end px-3">
                                        <button class="btn btn-light-pink action-btn btn-view-detail" data-id="{{ $booking->id }}" title="Lihat Detail & Update">
                                            <i class="ti ti-eye fs-5"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted py-4">
                                            <i class="ti ti-calendar-off fs-1 opacity-25 d-block mb-2"></i>
                                            <p class="mt-3">Belum ada pemesanan dalam kategori <b>{{ ucfirst($status) }}</b>.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $bookings->appends(['status' => $status, 'filter_mode' => request('filter_mode'), 'filter_value' => request('filter_value')])->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DETAIL BOOKING -->
<div class="modal fade" id="bookingDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-pink text-white border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="ti ti-clipboard-list me-2"></i>Detail Pemesanan <span id="mdl_id"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <!-- Sisi Kiri: Info Pelanggan & Status -->
                    <div class="col-md-5 bg-light p-4 border-end">
                        <div class="text-center mb-4">
                            <div class="avatar-lg bg-white rounded-circle shadow-sm mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="ti ti-user text-pink" style="font-size: 3rem;"></i>
                            </div>
                            <h4 id="mdl_customer" class="fw-bold text-dark mb-1"></h4>
                            <div class="small text-muted mb-2">
                                <div id="mdl_customer_email"></div>
                                <div id="mdl_customer_phone"></div>
                            </div>
                            <span id="mdl_role_badge" class="badge bg-light-pink text-pink rounded-pill px-3"></span>

                            <div class="d-flex flex-column gap-2 mt-4">
                                <button class="btn btn-success w-100 rounded-3 shadow-sm" id="btn_wa_reminder">
                                    <i class="ti ti-brand-whatsapp me-2"></i>Reminder WA
                                </button>
                                <button class="btn btn-outline-success w-100 rounded-3" id="btn_wa_broadcast">
                                    <i class="ti ti-users me-2"></i>Broadcast ke Grup
                                </button>
                                <button class="btn btn-pink w-100 rounded-3 shadow-sm" id="btn_print_receipt">
                                    <i class="ti ti-printer me-2"></i>Cetak Struk
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h6 class="fw-bold small text-muted text-uppercase mb-3">Informasi Status</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Status Booking:</span>
                                <span id="mdl_status" class="badge-status"></span>
                            </div>
                            <!-- <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Status Pembayaran:</span>
                                <span id="mdl_payment_status" class="badge-status"></span>
                            </div> -->
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Metode:</span>
                                <span id="mdl_payment_method" class="fw-bold"></span>
                            </div>
                            <hr>
                            <div class="d-flex flex-column gap-1 mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Jadwal Reservasi:</span>
                                    <span id="mdl_time" class="fw-bold small text-primary"></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Tanggal Transaksi:</span>
                                    <span id="mdl_transaction_time" class="fw-bold small text-muted"></span>
                                </div>
                            </div>
                            
                            <!-- CASH PAYMENT SECTION -->
                            <div id="cash_payment_section" style="display:none;" class="mt-3 p-3 bg-white rounded border border-success shadow-sm">
                                <label class="small fw-bold text-success mb-1"><i class="ti ti-cash me-1"></i> Pembayaran Tunai:</label>
                                <div class="input-group input-group-sm mb-2">
                                    <span class="input-group-text bg-light text-success fw-bold">Rp</span>
                                    <input type="text" id="cash_nominal" class="form-control fw-bold text-success" placeholder="Masukkan nominal...">
                                </div>
                                <div id="cash_change_container" class="small text-muted mb-2 d-none d-flex justify-content-between">
                                    <span>Kembalian:</span>
                                    <span id="cash_change" class="fw-bold text-success">Rp 0</span>
                                </div>
                                <button class="btn btn-sm btn-success w-100 fw-bold" id="btn_process_cash_payment">
                                    <i class="ti ti-wallet me-1"></i> Bayar
                                </button>
                            </div>

                            <div id="reschedule_section" style="display:none;" class="mt-2 p-2 bg-white rounded border border-pink shadow-sm">
                                <label class="small fw-bold text-pink mb-1">Ganti Jadwal:</label>
                                <input type="datetime-local" id="reschedule_datetime" class="form-control form-control-sm mb-2 border-pink">
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-pink flex-grow-1" id="btn_save_reschedule">Simpan</button>
                                    <button class="btn btn-sm btn-light flex-grow-1" id="btn_cancel_reschedule">Batal</button>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-pink w-100 mt-2" id="btn_show_reschedule">
                                <i class="ti ti-calendar-event me-1"></i>Reschedule
                            </button>
                            <div class="d-flex justify-content-between mt-3 mb-2">
                                <span class="text-muted">Petugas/Kasir:</span>
                                <span id="mdl_cashier" class="fw-bold"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sisi Kanan: Detail Layanan -->
                    <div class="col-md-7 p-4 bg-white">
                        <h6 class="fw-bold text-dark mb-3">Daftar Layanan:</h6>
                        <div id="mdl_services_list" class="list-group list-group-flush rounded-3 border overflow-hidden mb-4">
                            <!-- Populated by JS -->
                        </div>
                        
                        <div class="bg-light p-3 rounded-3 mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark fs-5">Total Pembayaran</span>
                                <span id="mdl_total" class="fw-bold text-pink fs-4"></span>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div id="mdl_actions" class="d-flex gap-2">
                            <button class="btn btn-success flex-grow-1 rounded-pill py-2 shadow-sm" id="btnMarkFinished">
                                <i class="ti ti-check me-1"></i> Tandai Selesai
                            </button>
                            <button class="btn btn-outline-danger flex-grow-1 rounded-pill py-2" id="btnCancelBooking">
                                <i class="ti ti-x me-1"></i> Batalkan Pesanan
                            </button>
                        </div>
                        <div id="mdl_status_info" class="text-center p-3 bg-light rounded-pill d-none">
                            <span class="text-muted fw-bold small">Pemesanan ini sudah bersifat final.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Modal Instance
        const detailModal = new bootstrap.Modal(document.getElementById('bookingDetailModal'));
        let currentBookingId = null;

        // View Detail Handler
        $(document).on('click', '.btn-view-detail', function() {
            const id = $(this).data('id');
            const btn = $(this);
            currentBookingId = id;
            
            // Show loading or just open? Let's fetch data.
            $.ajax({
                url: "{{ url('admin/bookings') }}/" + id,
                type: 'GET',
                beforeSend: function() {
                    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
                },
                success: function(data) {
                    $('#mdl_id').text('#' + data.id);
                    $('#mdl_customer').text(data.customer_name);
                    $('#mdl_customer_email').html('<i class="ti ti-mail me-1"></i>' + (data.customer_email || '-'));
                    $('#mdl_customer_phone').html('<i class="ti ti-brand-whatsapp me-1"></i>' + (data.customer_phone || '-'));
                    $('#mdl_role_badge').text(data.user_id ? 'Pelanggan Terdaftar' : 'Pelanggan Guest/Offline');
                    
                    // Status Badge Mapping
                    const statusMap = {
                        'pending':    { label: '<i class="ti ti-clock me-1"></i> Pending',  class: 'bg-warning text-dark' },
                        'success':    { label: '<i class="ti ti-circle-check me-1"></i> Selesai', class: 'bg-success text-white' },
                        'dibatalkan': { label: '<i class="ti ti-circle-x me-1"></i> Batal',  class: 'bg-danger text-white' }
                    };
                    const payMap = {
                        // 'paid': { label: 'LUNAS', class: 'bg-success text-white' },
                        // 'unpaid': { label: 'BELUM BAYAR', class: 'bg-danger text-white' },
                        'pending': { label: 'PENDING', class: 'bg-warning text-dark' },
                        'failed': { label: 'GAGAL', class: 'bg-danger text-white' }
                    };

                    const s = statusMap[data.status] || { label: data.status, class: 'bg-secondary' };
                    $('#mdl_status').html(s.label).removeClass().addClass('badge-status ' + s.class);
                    
                    const ps = payMap[data.payment_status] || { label: data.payment_status, class: 'bg-secondary' };
                    $('#mdl_payment_status').text(ps.label).removeClass().addClass('badge-status ' + ps.class);

                    // Store total price for receipt printing and cash payment
                    $('#bookingDetailModal').data('total-price', data.total_price);

                    // Cash Payment section visibility and initial state setup
                    if (data.payment_method && data.payment_method.toLowerCase() === 'tunai' && data.payment_status === 'unpaid') {
                        $('#cash_payment_section').show();
                        $('#cash_nominal').val('');
                        $('#cash_change_container').addClass('d-none');
                        $('#btn_process_cash_payment').prop('disabled', true);
                    } else {
                        $('#cash_payment_section').hide();
                    }
                    
                    $('#mdl_payment_method').text((data.payment_method || '-').toUpperCase());
                    const formatDatetime = (dtStr) => {
                        if (!dtStr) return '-';
                        try {
                            const date = new Date(dtStr);
                            const day = String(date.getDate()).padStart(2, '0');
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const year = date.getFullYear();
                            const hours = String(date.getHours()).padStart(2, '0');
                            const minutes = String(date.getMinutes()).padStart(2, '0');
                            return `${day}/${month}/${year} - ${hours}:${minutes} WIB`;
                        } catch(e) {
                            return dtStr;
                        }
                    };
                    $('#mdl_time').text(formatDatetime(data.reservation_datetime));
                    $('#mdl_transaction_time').text(formatDatetime(data.created_at));
                    $('#mdl_cashier').text(data.cashier ? data.cashier.name : '-');
                    $('#mdl_total').text('Rp ' + new Intl.NumberFormat('id-ID').format(data.total_price));

                    // Services List
                    let servicesHtml = '';
                    data.details.forEach(detail => {
                        const detailName = detail.treatment_detail ? detail.treatment_detail.name : 'Layanan Tidak Diketahui';
                        const duration   = detail.treatment_detail ? detail.treatment_detail.duration : null;
                        const stylistName = detail.stylist ? detail.stylist.name : (data.stylist ? data.stylist.name : 'Tanpa Stylist');
                        servicesHtml += `
                            <div class="list-group-item p-3 border-0 border-bottom">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-bold text-dark">${detailName}</span>
                                    <span class="fw-bold">Rp ${new Intl.NumberFormat('id-ID').format(detail.price)}</span>
                                </div>
                                <div class="d-flex gap-3 flex-wrap">
                                    <small class="text-muted"><i class="ti ti-user me-1"></i>Stylist: ${stylistName}</small>
                                    ${duration ? `<small class="text-muted"><i class="ti ti-clock me-1"></i>Durasi: ${duration} menit</small>` : ''}
                                </div>
                            </div>
                        `;
                    });
                    $('#mdl_services_list').html(servicesHtml);

                    // Store Phone for WA
                    const customerPhone = data.customer_phone || (data.user ? data.user.phone : '');
                    $('#bookingDetailModal').data('phone', customerPhone);
                    $('#bookingDetailModal').data('id', data.id);
                    
                    // Reset reschedule section
                    $('#reschedule_section').hide();
                    $('#reschedule_datetime').val('');

                    // Action buttons & Cetak Struk visibility based on status
                    if(data.status === 'pending') {
                        $('#mdl_actions').removeClass('d-none').addClass('d-flex');
                        $('#mdl_status_info').addClass('d-none');
                        $('#btn_print_receipt').hide();
                    } else if(data.status === 'success') {
                        $('#mdl_actions').removeClass('d-flex').addClass('d-none');
                        $('#mdl_status_info').removeClass('d-none');
                        $('#btn_print_receipt').show();
                    } else { // dibatalkan
                        $('#mdl_actions').removeClass('d-flex').addClass('d-none');
                        $('#mdl_status_info').removeClass('d-none');
                        $('#btn_print_receipt').hide();
                    }

                    detailModal.show();
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Gagal mengambil data: ' + (xhr.responseJSON ? xhr.responseJSON.message : xhr.statusText), 'error');
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="ti ti-eye fs-5"></i>');
                }
            });
        });

        // AJAX Status Update Helper
        function updateBookingStatus(status) {
            Swal.fire({
                title: status === 'success' ? 'Selesaikan Pesanan?' : 'Batalkan Pesanan?',
                text: "Status akan diperbarui secara permanen.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: status === 'success' ? '#2ecc71' : '#e74c3c',
                cancelButtonColor: '#95a5a6',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Kembali'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/bookings') }}/" + currentBookingId + "/status",
                        type: 'PATCH',
                        data: {
                            _token: '{{ csrf_token() }}',
                            status: status
                        },
                        success: function(res) {
                            Swal.fire('Berhasil!', res.message, 'success').then(() => {
                                location.reload(); // Refresh to update counts/tabs
                            });
                        },
                        error: function() {
                            Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
                        }
                    });
                }
            });
        }

        $('#btnMarkFinished').click(() => updateBookingStatus('success'));
        $('#btnCancelBooking').click(() => updateBookingStatus('dibatalkan'));

        // WhatsApp & Reschedule Handlers
        $('#btn_wa_reminder').on('click', function() {
            const id = $('#bookingDetailModal').data('id');
            const phone = $('#bookingDetailModal').data('phone');
            if (!phone) return Swal.fire('Error', 'Nomor telepon tidak tersedia', 'error');
            
            let cleanPhone = phone.replace(/\D/g, '');
            if (cleanPhone.startsWith('0')) {
                cleanPhone = '62' + cleanPhone.slice(1);
            }
            const message = encodeURIComponent(`Halo Kak, kami dari Indah Sari Salon ingin mengingatkan jadwal booking Kakak (#BOOK-${id}) pada jam ${$('#mdl_time').text()}. Apakah ada perubahan jadwal? Sampaikan kepada kami.`);
            window.open(`https://wa.me/${cleanPhone}?text=${message}`, '_blank');
        });

        $('#btn_wa_broadcast').on('click', function() {
            const customer = $('#mdl_customer').text();
            const date = $('#mdl_date').text();
            const time = $('#mdl_time').text();
            
            // Get Stylist from the first service item (as per typical needs)
            const stylist = $('#mdl_services_list small').first().text().replace('Stylist: ', '').trim() || 'Tidak Ditentukan';
            
            // Collect services text
            let services = [];
            $('#mdl_services_list .fw-bold.text-dark').each(function() {
                services.push($(this).text());
            });
            
            // Debug: Check the console (F12) to see if the message is generated correctly
            var pin = String.fromCodePoint(0x1F4CC);
            var user = String.fromCodePoint(0x1F464);
            var cal = String.fromCodePoint(0x1F4C5);
            var hair = String.fromCodePoint(0x1F487);

            var msg = "*BOOKING BARU - INDAH SARI SALON*\n\n";
            msg += pin + " *Stylist:* " + stylist + "\n";
            msg += user + " *Customer:* " + customer + "\n";
            msg += cal + " *Jadwal:* " + date + " | " + time + " WIB\n";
            msg += hair + " *Treatment:* " + services.join(', ') + "\n\n";
            msg += "_Mohon bersiap sebelum jam booking. Terima kasih!_";
            
            console.log("Final Message:", msg);
            
            var url = "https://api.whatsapp.com/send?text=" + encodeURIComponent(msg);
            
            // Create a temporary link to trigger the open
            var link = document.createElement('a');
            link.href = url;
            link.target = '_blank';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });

        $('#btn_print_receipt').on('click', function(e) {
            e.preventDefault();
            const id = $('#bookingDetailModal').data('id');
            const totalPrice = $('#bookingDetailModal').data('total-price') || 0;
            
            // Prevent Bootstrap modal from hijacking focus when SweetAlert2 is open
            const stopFocusTrap = (e) => {
                if (e.target && e.target.closest && e.target.closest('.swal2-container')) {
                    e.stopImmediatePropagation();
                }
            };
            window.addEventListener('focusin', stopFocusTrap, true);
            
            Swal.fire({
                title: 'Input Nominal Pembayaran',
                text: `Total Pembayaran: Rp ${new Intl.NumberFormat('id-ID').format(totalPrice)}`,
                input: 'text',
                inputPlaceholder: 'Masukkan nominal pembayaran...',
                inputAttributes: {
                    autocapitalize: 'off',
                    autocorrect: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Cetak Struk',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#EA8290',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Nominal tidak boleh kosong!';
                    }
                    const nominal = parseInt(value.replace(/\D/g, ''));
                    if (isNaN(nominal) || nominal < 0) {
                        return 'Masukkan nominal yang valid!';
                    }
                    if (nominal < totalPrice) {
                        return `Nominal kurang! Minimal Rp ${new Intl.NumberFormat('id-ID').format(totalPrice)}`;
                    }
                },
                didOpen: () => {
                    const input = Swal.getInput();
                    
                    // Format input on keyup
                    $(input).on('input', function() {
                        let val = $(this).val().replace(/\D/g, '');
                        if (val !== '') {
                            $(this).val(new Intl.NumberFormat('id-ID').format(val));
                        }
                    });
                }
            }).then((result) => {
                window.removeEventListener('focusin', stopFocusTrap, true);
                if (result.isConfirmed) {
                    const nominal = result.value.replace(/\D/g, '');
                    window.open(`/admin/bookings/${id}/print?nominal=${nominal}`, '_blank');
                }
            });
        });

        $('#btn_show_reschedule').on('click', function() {
            $('#reschedule_section').slideToggle();
        });

        $('#btn_cancel_reschedule').on('click', function() {
            $('#reschedule_section').slideUp();
        });

        $('#btn_save_reschedule').on('click', function() {
            const newDateTime = $('#reschedule_datetime').val();
            if (!newDateTime) return Swal.fire('Peringatan', 'Pilih tanggal dan waktu baru terlebih dahulu.', 'warning');

            const id = $('#bookingDetailModal').data('id');
            const btn = $(this);
            btn.prop('disabled', true).text('Menyimpan...');

            $.ajax({
                url: `/admin/bookings/${id}/reschedule`,
                type: 'PATCH',
                data: {
                    _token: '{{ csrf_token() }}',
                    reservation_datetime: newDateTime
                },
                success: function(res) {
                    Swal.fire('Berhasil!', res.message, 'success').then(() => {
                        location.reload();
                    });
                },
                error: function() {
                    Swal.fire('Error!', 'Gagal mengubah jadwal.', 'error');
                    btn.prop('disabled', false).text('Simpan');
                }
            });
        });

        // Format cash nominal input with thousands separator and calculate change dynamically
        $(document).on('input', '#cash_nominal', function() {
            let val = $(this).val().replace(/\D/g, '');
            if (val === '') {
                $('#cash_change_container').addClass('d-none');
                $('#btn_process_cash_payment').prop('disabled', true);
                return;
            }
            
            // Format input value beautifully with dot separators (e.g. 1.000.000)
            let formatted = new Intl.NumberFormat('id-ID').format(val);
            $(this).val(formatted);
            
            const totalPrice = $('#bookingDetailModal').data('total-price') || 0;
            const nominal = parseInt(val);
            
            $('#cash_change_container').removeClass('d-none');
            if (nominal >= totalPrice) {
                const change = nominal - totalPrice;
                $('#cash_change').text('Rp ' + new Intl.NumberFormat('id-ID').format(change)).removeClass('text-danger').addClass('text-success');
                $('#btn_process_cash_payment').prop('disabled', false);
            } else {
                const lack = totalPrice - nominal;
                $('#cash_change').text('Kurang Rp ' + new Intl.NumberFormat('id-ID').format(lack)).removeClass('text-success').addClass('text-danger');
                $('#btn_process_cash_payment').prop('disabled', true);
            }
        });

        // Click Handler to process Cash payment via AJAX
        $('#btn_process_cash_payment').on('click', function() {
            const id = $('#bookingDetailModal').data('id');
            const nominalStr = $('#cash_nominal').val().replace(/\D/g, '');
            const nominal = parseInt(nominalStr);
            const btn = $(this);
            
            if (!nominal || isNaN(nominal)) {
                return Swal.fire('Peringatan', 'Masukkan nominal yang valid.', 'warning');
            }
            
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Memproses...');
            
            $.ajax({
                url: `/admin/bookings/${id}/pay-cash`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    cash_nominal: nominal
                },
                success: function(res) {
                    Swal.fire({
                        title: 'Pembayaran Berhasil!',
                        html: `Kembalian: <b class="text-success">${res.formatted_change}</b>`,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire('Error!', (xhr.responseJSON ? xhr.responseJSON.message : 'Gagal memproses pembayaran.'), 'error');
                    btn.prop('disabled', false).html('<i class="ti ti-wallet me-1"></i> Bayar');
                }
            });
        });

        // Tab Filter Logic (Already covered by direct links, but keep standard)
        if (typeof layout_change === 'function') {
            layout_change('light');
            font_change('Roboto');
            preset_change('preset-1');
        }
    });

    // Date Filter Persistence
    $('#filterMode').on('change', function() {
        const mode = $(this).val();
        if (mode === 'all') {
            $('#filterForm').submit();
        } else {
            $('#filterInputCol').show();
            // Optional: reset input based on type
        }
    });
</script>
@endpush
@endsection
