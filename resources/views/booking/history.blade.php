@extends('layout.dashboard')

@section('title', 'Riwayat Booking')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold text-dark">
                    <i class="ti ti-history me-2 text-primary"></i>Riwayat Pemesanan
                </h4>
                @if(Auth::user()->role === 'pelanggan')
                    <a href="{{ route('booking.index') }}" class="btn btn-primary btn-sm rounded-pill px-3">
                        + Booking Baru
                    </a>
                @endif
            </div>
            <div class="card-body p-0">
                @if(Auth::user()->role === 'pelanggan')
                    <!-- ========================================== -->
                    <!-- TAMPILAN KHUSUS PELANGGAN (TABBED & INTERAKTIF) -->
                    <!-- ========================================== -->
                    <ul class="nav nav-tabs nav-tabs-basic px-4 pt-3" id="bookingTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold" id="process-tab" data-bs-toggle="tab" data-bs-target="#process" type="button" role="tab">
                                <i class="ti ti-loader me-2"></i>Dalam Proses 
                                <span class="badge bg-warning ms-1">{{ $inProcess->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
                                <i class="ti ti-checkup-list me-2"></i>Riwayat Selesai
                                <span class="badge bg-secondary ms-1">{{ $history->count() }}</span>
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content p-4" id="bookingTabsContent">
                        <!-- TAB 1: DALAM PROSES -->
                        <div class="tab-pane fade show active" id="process" role="tabpanel">
                            <div class="row g-3">
                                @forelse($inProcess as $booking)
                                    <div class="col-md-6 col-xl-4">
                                        <div class="card border border-light-subtle shadow-none h-100 booking-card-user" 
                                             onclick="showBookingDetail({{ json_encode($booking->load('treatment', 'stylist', 'details.treatmentDetail.treatment', 'details.stylist')) }})" 
                                             style="cursor: pointer; transition: transform 0.2s;">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <span class="badge bg-light-warning text-warning rounded-pill px-3">Pending</span>
                                                    <small class="text-muted">#BOOK-{{ $booking->id }}</small>
                                                </div>
                                                <h6 class="fw-bold mb-1 text-dark">{{ $booking->treatment->name }}</h6>
                                                <div class="small mb-1">
                                                    <span class="badge bg-light-primary text-primary px-2 py-0.5 extra-small" style="font-size: 0.6rem;"><i class="ti ti-calendar-event me-1"></i>Reservasi:</span>
                                                    <div class="fw-bold text-dark small mt-0.5">{{ \Carbon\Carbon::parse($booking->reservation_datetime)->format('d M Y, H:i') }} WIB</div>
                                                </div>
                                                <div class="small mb-3">
                                                    <span class="badge bg-light-secondary text-secondary px-2 py-0.5 extra-small" style="font-size: 0.6rem;"><i class="ti ti-receipt me-1"></i>Transaksi:</span>
                                                    <div class="text-muted small mt-0.5">{{ \Carbon\Carbon::parse($booking->created_at)->format('d M Y, H:i') }} WIB</div>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between mt-auto">
                                                    <span class="fw-bold text-primary">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                                                    <span class="text-muted small"><i class="ti ti-click me-1"></i>Klik detail</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center py-5">
                                        <div class="mb-3">
                                            <i class="ti ti-calendar-off text-muted" style="font-size: 4rem; opacity: 0.6;"></i>
                                        </div>
                                        <p class="text-muted mt-3">Tidak ada pemesanan yang sedang diproses.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- TAB 2: RIWAYAT SELESAI -->
                        <div class="tab-pane fade" id="history" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Layanan</th>
                                            <th>Tanggal</th>
                                            <th>Biaya</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($history as $booking)
                                            <tr>
                                                <td>
                                                    <div class="fw-bold text-dark">
                                                        @php
                                                            $treatments = $booking->details->map(fn($d) => $d->treatmentDetail->treatment->name)->unique();
                                                        @endphp
                                                        {{ $treatments->implode(', ') }}
                                                    </div>
                                                    <small class="text-muted d-block mt-1">
                                                        <i class="ti ti-users me-1"></i>
                                                        @php
                                                            $stylistNames = $booking->details->map(fn($d) => $d->stylist->name ?? '-')->unique();
                                                        @endphp
                                                        {{ $stylistNames->implode(', ') }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="small" title="Tanggal Reservasi">
                                                        <span class="badge bg-light-primary text-primary px-2 py-0.5 extra-small mb-1" style="font-size: 0.6rem;"><i class="ti ti-calendar-event me-1"></i>Reservasi:</span>
                                                        <div class="fw-bold text-dark small">{{ \Carbon\Carbon::parse($booking->reservation_datetime)->format('d M Y') }} - {{ \Carbon\Carbon::parse($booking->reservation_datetime)->format('H:i') }} WIB</div>
                                                    </div>
                                                    <div class="small mt-2" title="Tanggal Transaksi">
                                                        <span class="badge bg-light-secondary text-secondary px-2 py-0.5 extra-small mb-1" style="font-size: 0.6rem;"><i class="ti ti-receipt me-1"></i>Transaksi:</span>
                                                        <div class="text-muted small">{{ \Carbon\Carbon::parse($booking->created_at)->format('d M Y') }} - {{ \Carbon\Carbon::parse($booking->created_at)->format('H:i') }} WIB</div>
                                                    </div>
                                                </td>
                                                <td class="fw-bold text-dark">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                                                <td>
                                                    @if($booking->status === 'success')
                                                        <span class="badge bg-light-success text-success rounded-pill px-3">Selesai</span>
                                                    @else
                                                        <span class="badge bg-light-danger text-danger rounded-pill px-3" data-bs-toggle="tooltip" title="Alasan: {{ $booking->cancel_reason }}">Dibatalkan</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-5 text-muted">Belum ada riwayat pemesanan selesai.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                @else
                    <!-- ========================================== -->
                    <!-- TAMPILAN ADMIN / KARYAWAN (TABEL LENGKAP) -->
                    <!-- ========================================== -->
                    <div class="p-4">
                        <!-- Existing Filter Section for Staff -->
                        <div class="row g-3 mb-4 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Mode Filter</label>
                                <select id="filterMode" class="form-select">
                                    <option value="all">Semua Riwayat</option>
                                    <option value="daily">Harian</option>
                                    <option value="monthly">Bulanan</option>
                                    <option value="yearly">Tahunan</option>
                                </select>
                            </div>
                            <div id="filterInputCol" class="col-md-4" style="display:none;">
                                <label id="filterLabel" class="form-label fw-bold small">Pilih Tanggal</label>
                                <input type="date" id="filterValue" class="form-control">
                            </div>
                            <div id="resetCol" class="col-md-2" style="display:none;">
                                <button id="btnReset" class="btn btn-light-secondary w-100">Reset</button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="historyTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Treatment & Pelanggan</th>
                                        <th>Tanggal & Waktu</th>
                                        <th>Total Biaya</th>
                                        <th>Status</th>
                                        <th>Pembayaran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($allBookings as $booking)
                                        @php
                                            $createdAt = \Carbon\Carbon::parse($booking->created_at);
                                            $resDateTime = \Carbon\Carbon::parse($booking->reservation_datetime);
                                        @endphp
                                        <tr class="booking-row" 
                                            data-date="{{ $resDateTime->format('Y-m-d') }}" 
                                            data-month="{{ $resDateTime->format('Y-m') }}" 
                                            data-year="{{ $resDateTime->format('Y') }}">
                                            <td>
                                                <div class="fw-bold text-dark">
                                                    @php
                                                        $treatments = $booking->details->map(fn($d) => $d->treatmentDetail->treatment->name)->unique();
                                                    @endphp
                                                    {{ $treatments->implode(', ') }}
                                                </div>
                                                <div class="badge bg-light-primary text-primary border border-primary border-opacity-25 small mt-1">
                                                    <i class="ti ti-user me-1"></i>{{ $booking->customer_name }} {{ $booking->user_id ? '' : '(Offline)' }}
                                                </div>
                                                <div class="extra-small text-muted mt-1">
                                                    <i class="ti ti-users me-1"></i>{{ $booking->details->map(fn($d) => $d->stylist->name ?? '-')->unique()->implode(', ') }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="small" title="Tanggal Reservasi">
                                                    <span class="badge bg-light-primary text-primary px-2 py-1 extra-small mb-1" style="font-size: 0.65rem;"><i class="ti ti-calendar-event me-1"></i>Reservasi:</span>
                                                    <div class="fw-bold text-dark">{{ $resDateTime->format('d M Y') }} - {{ $resDateTime->format('H:i') }} WIB</div>
                                                </div>
                                                <div class="small mt-2" title="Tanggal Transaksi">
                                                    <span class="badge bg-light-secondary text-secondary px-2 py-1 extra-small mb-1" style="font-size: 0.65rem;"><i class="ti ti-receipt me-1"></i>Transaksi:</span>
                                                    <div class="text-muted">{{ $createdAt->format('d M Y') }} - {{ $createdAt->format('H:i') }} WIB</div>
                                                </div>
                                            </td>
                                            <td class="fw-bold">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                                            <td>
                                                @php
                                                    $statusClass = 'bg-light-warning text-warning';
                                                    if($booking->status == 'success') $statusClass = 'bg-light-success text-success';
                                                    if($booking->status == 'dibatalkan') $statusClass = 'bg-light-danger text-danger';
                                                @endphp
                                                <span class="badge {{ $statusClass }} rounded-pill px-3">{{ ucfirst($booking->status) }}</span>
                                            </td>
                                            <td>
                                                <!-- <span class="badge {{ $booking->payment_status == 'paid' ? 'bg-light-success text-success' : 'bg-light-danger text-danger' }} rounded-pill px-3">
                                                    {{ ucfirst($booking->payment_status) }}
                                                </span> -->
                                                <small class="text-muted" style="font-size: 0.65rem;">
                                                <i class="ti ti-{{ strtolower($booking->payment_method) == 'transfer' ? 'credit-card' : (strtolower($booking->payment_method) == 'qris' ? 'qrcode' : 'wallet') }} me-1"></i>{{ ucfirst($booking->payment_method) }}
                                            </small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center py-5">Kosong.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL DETAIL BOOKING (PELANGGAN) -->
<!-- ========================================== -->
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold">Detail Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="detailContent">
                <!-- Content injected via JS -->
            </div>
            <div class="modal-footer border-top p-3 d-flex justify-content-between" id="detailFooter">
                <button type="button" class="btn btn-light-danger" id="btnCancelShow">Batalkan Pemesanan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL ALASAN PEMBATALAN -->
<!-- ========================================== -->
<div class="modal fade" id="modalCancel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white fw-bold">Batalkan Pemesanan?</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted">Apakah Anda yakin ingin membatalkan pesanan ini? Jika ya, silakan berikan alasan pembatalan (opsional).</p>
                <div class="mb-3">
                    <label class="form-label fw-bold">Alasan Pembatalan</label>
                    <textarea id="cancelReason" class="form-control" rows="3" placeholder="Contoh: Ada keperluan mendadak..."></textarea>
                </div>
                <input type="hidden" id="cancelBookingId">
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger" id="btnSubmitCancel">Konfirmasi Batalkan</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Midtrans Snap JS -->
<script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ config('services.midtrans.client_key') }}"></script>

<script>
    // Theme Config (Safe Check)
    if (typeof layout_change === 'function') {
        layout_change('light');
        font_change('Roboto');
        change_box_container('false');
        layout_caption_change('true');
        layout_rtl_change('false');
        preset_change('preset-1');
    }

    let activeBooking = null;

    function showBookingDetail(booking) {
        activeBooking = booking;
        const resDate = new Date(booking.reservation_datetime);
        const formattedDate = resDate.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
        const formattedTime = resDate.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

        let html = `
            <div class="text-center mb-4">
                                <div class="avtar avtar-xl bg-light-primary text-primary mx-auto mb-3">
                                    <i class="ti ti-calendar-check" style="font-size: 2rem;"></i>
                                </div>
                                <h5 class="fw-bold mb-0">Detail Pesanan</h5>
                                <span class="text-muted">#BOOK-${booking.id}</span>
                            </div>
                            <div class="mb-3">
                                <h6 class="fw-bold small text-muted text-uppercase mb-2">Layanan yang dipilih:</h6>
                                <div class="list-group list-group-flush border rounded">
                                    ${booking.details.map(d => `
                                        <div class="list-group-item py-2">
                                            <div class="d-flex justify-content-between">
                                                <span class="small fw-bold">${d.treatment_detail.treatment.name}</span>
                                                <span class="small text-primary">Rp ${new Intl.NumberFormat('id-ID').format(d.price)}</span>
                                            </div>
                                            <div class="extra-small text-muted d-flex justify-content-between">
                                                <span>${d.treatment_detail.name}</span>
                                                <span class="fw-bold"><i class="ti ti-user-check me-1"></i>${d.stylist ? d.stylist.name : 'N/A'}</span>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
            <div class="list-group list-group-flush border-top border-bottom mb-3">
                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <span class="text-muted small">Nama Pelanggan</span>
                    <span class="fw-bold text-dark text-end">${booking.customer_name}</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <span class="text-muted small">No. HP</span>
                    <span class="fw-bold text-dark text-end">${booking.customer_phone || '-'}</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-start px-0">
                    <span class="text-muted small">Email</span>
                    <span class="fw-bold text-dark text-end text-break" style="max-width: 70%;">${booking.customer_email || '-'}</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <span class="text-muted small">Jadwal Reservasi</span>
                    <span class="fw-bold text-dark text-end">${formattedDate} - ${formattedTime} WIB</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <span class="text-muted small">Tanggal Transaksi</span>
                    <span class="fw-bold text-muted text-end">${new Date(booking.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })} - ${new Date(booking.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false }).replace(/\./g, ':')} WIB</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <span class="text-muted small">Stylist</span>
                    <span class="fw-bold text-dark text-end">${booking.stylist ? booking.stylist.name : 'Belum Ditentukan'}</span>
                </div>
                <div class="list-group-item d-flex justify-content-between px-0">
                    <span class="text-muted">Total Tagihan</span>
                    <span class="fw-bold text-primary">Rp ${new Intl.NumberFormat('id-ID').format(booking.total_price)}</span>
                </div>
                <div class="list-group-item d-flex justify-content-between px-0">
                    <span class="text-muted">Status Pembayaran</span>
                    <span class="fw-bold text-dark">${booking.payment_status === 'paid' ? '<span class="badge bg-success">Lunas (Berhasil)</span>' : '<span class="badge bg-warning text-dark">Belum Dibayar</span>'}</span>
                </div>
<div class="list-group-item d-flex justify-content-between px-0">
    <span class="text-muted">Status Pemesanan</span>
    <span class="fw-bold text-dark">${booking.status === 'pending' ? '<span class="badge bg-warning text-dark">Pending</span>' : (booking.status === 'confirmed' ? '<span class="badge bg-success">Confirmed</span>' : (booking.status === 'cancelled' ? '<span class="badge bg-danger">Dibatalkan</span>' : ''))}</span>
</div>
            </div>
            
            ${(booking.payment_status === 'unpaid' && (booking.status === 'pending' || booking.status === 'confirmed')) ? `
                <div class="p-3 bg-light-warning rounded-3 border border-warning-subtle mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="ti ti-wallet text-warning h4 mb-0 me-2"></i>
                        <span class="fw-bold text-dark small">Pilih Metode Pembayaran & Selesaikan</span>
                    </div>
                    <div class="mb-3">
                        <select id="change_payment_method_${booking.id}" class="form-select form-select-sm">
                            <option value="Transfer" ${booking.payment_method === 'Transfer' ? 'selected' : ''}>Transfer Bank (Midtrans)</option>
                            <option value="QRIS" ${booking.payment_method === 'QRIS' ? 'selected' : ''}>QRIS / E-Wallet (Midtrans)</option>
                        </select>
                    </div>
                    <button class="btn btn-sm btn-success w-100 py-2 fw-bold" id="btnPayNow_${booking.id}" onclick="payUnpaidBooking(${booking.id})">
                        <i class="ti ti-credit-card me-1"></i>Bayar Sekarang
                    </button>
                </div>
            ` : ''}

            <div class="alert alert-light-info border-0 d-flex align-items-center mb-0">
                <i class="ti ti-info-circle me-2 h4 mb-0"></i>
                <small>Mohon datang 10 menit sebelum jadwal untuk verifikasi.</small>
            </div>
        `;
        
        $('#detailContent').html(html);
        $('#modalDetail').modal('show');
    }

    window.payUnpaidBooking = function (id) {
        const btn = $(`#btnPayNow_${id}`);
        const selectEl = $(`#change_payment_method_${id}`);
        const method = selectEl.val();

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Memproses...');

        $.ajax({
            url: `/booking/${id}/update-payment-method`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                payment_method: method
            },
            success: function (response) {
                if (response.snap_token) {
                    $('#modalDetail').modal('hide');
                    
                    snap.pay(response.snap_token, {
                        onSuccess: function (result) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `/booking/pay/${id}`;
                            const csrf = document.createElement('input');
                            csrf.type = 'hidden';
                            csrf.name = '_token';
                            csrf.value = '{{ csrf_token() }}';
                            form.appendChild(csrf);
                            document.body.appendChild(form);
                            form.submit();
                        },
                        onPending: function (result) {
                            Swal.fire('Info', 'Pembayaran sedang menunggu penyelesaian.', 'info').then(() => {
                                window.location.reload();
                            });
                        },
                        onError: function (result) {
                            Swal.fire('Error', 'Mohon maaf, transaksi gagal diproses.', 'error').then(() => {
                                window.location.reload();
                            });
                        },
                        onClose: function () {
                            Swal.fire({
                                title: 'Pembayaran Belum Selesai',
                                text: 'Anda dapat melanjutkan pembayaran kapan saja dari halaman riwayat ini.',
                                icon: 'warning',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#EA8290'
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    });
                } else {
                    Swal.fire('Error', 'Gagal memproses pembayaran.', 'error').then(() => {
                        window.location.reload();
                    });
                }
            },
            error: function (xhr) {
                btn.prop('disabled', false).html('<i class="ti ti-credit-card me-1"></i>Bayar Sekarang');
                Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
            }
        });
    };

    $(document).ready(function() {
        // Handle Tombol Batal di Modal Detail
        $('#btnCancelShow').on('click', function() {
            $('#modalDetail').modal('hide');
            $('#cancelBookingId').val(activeBooking.id);
            $('#modalCancel').modal('show');
        });

        // Submit Pembatalan
        $('#btnSubmitCancel').on('click', function() {
            const id = $('#cancelBookingId').val();
            const reason = $('#cancelReason').val();

            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Memproses...');

            $.ajax({
                url: `/booking/${id}/cancel`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    reason: reason
                },
                success: function(response) {
                    $('#modalCancel').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr) {
                    $('#btnSubmitCancel').prop('disabled', false).text('Konfirmasi Batalkan');
                    Swal.fire('Error', xhr.responseJSON.message || 'Terjadi kesalahan.', 'error');
                }
            });
        });

        // STAFF FILTER LOGIC
        const $mode = $('#filterMode');
        const $input = $('#filterValue');
        const $container = $('#filterInputCol');
        const $reset = $('#resetCol');
        const $label = $('#filterLabel');

        function applyFilter() {
            const mode = $mode.val();
            const val = $input.val();
            if (mode === 'all') {
                $('.booking-row').show();
            } else {
                $('.booking-row').hide();
                $('.booking-row').each(function() {
                    const rowDate = $(this).data('date');
                    const rowMonth = $(this).data('month');
                    const rowYear = $(this).data('year').toString();
                    let match = false;
                    if (mode === 'daily' && rowDate === val) match = true;
                    if (mode === 'monthly' && rowMonth === val) match = true;
                    if (mode === 'yearly' && rowYear === val) match = true;
                    if (match) $(this).show();
                });
            }
        }

        $mode.on('change', function() {
            const mode = $(this).val();
            $input.val('');
            if (mode === 'all') { $container.hide(); $reset.hide(); applyFilter(); }
            else {
                $container.show(); $reset.show();
                if (mode === 'daily') { $input.attr('type', 'date'); $label.text('Pilih Tanggal'); }
                else if (mode === 'monthly') { $input.attr('type', 'month'); $label.text('Pilih Bulan'); }
                else if (mode === 'yearly') { $input.attr('type', 'number').attr('min', '2020').attr('max', '2030'); $label.text('Tahun'); }
                applyFilter();
            }
        });
        $input.on('change keyup', applyFilter);
        $('#btnReset').on('click', () => { $mode.val('all').trigger('change'); });
    });
</script>
@endpush

<style>
    .booking-card-user:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05) !important;
        border-color: var(--bs-primary) !important;
    }
    .nav-tabs-basic .nav-link {
        border: none;
        border-bottom: 2px solid transparent;
        color: #6c757d;
        padding: 0.8rem 1.2rem;
    }
    .nav-tabs-basic .nav-link.active {
        color: var(--bs-primary);
        border-bottom-color: var(--bs-primary);
        background: transparent;
    }
    .extra-small { font-size: 0.7rem; }
</style>
@endsection