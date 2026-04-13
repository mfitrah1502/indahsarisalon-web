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
                                             onclick="showBookingDetail({{ json_encode($booking->load('treatment', 'stylist')) }})" 
                                             style="cursor: pointer; transition: transform 0.2s;">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <span class="badge bg-light-warning text-warning rounded-pill px-3">Pending</span>
                                                    <small class="text-muted">#BOOK-{{ $booking->id }}</small>
                                                </div>
                                                <h6 class="fw-bold mb-1 text-dark">{{ $booking->treatment->name }}</h6>
                                                <div class="text-muted small mb-3">
                                                    <i class="ti ti-calendar me-1"></i>{{ \Carbon\Carbon::parse($booking->reservation_datetime)->format('d M Y, H:i') }}
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
                                        <img src="{{ asset('assets/images/widget/empty-cart.svg') }}" alt="Empty" style="width: 120px; opacity: 0.5;">
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
                                                    <div class="fw-bold text-dark">{{ $booking->treatment->name }}</div>
                                                    <small class="text-muted">Stylist: {{ $booking->stylist->name ?? '-' }}</small>
                                                </td>
                                                <td>
                                                    <div class="small">{{ \Carbon\Carbon::parse($booking->reservation_datetime)->format('d M Y') }}</div>
                                                    <div class="extra-small text-muted">{{ \Carbon\Carbon::parse($booking->reservation_datetime)->format('H:i') }}</div>
                                                </td>
                                                <td class="fw-bold text-dark">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                                                <td>
                                                    @if($booking->status === 'berhasil')
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
                                                <div class="fw-bold text-dark">{{ $booking->treatment->name }}</div>
                                                <div class="badge bg-light-primary text-primary border border-primary border-opacity-25 small mt-1">
                                                    <i class="ti ti-user me-1"></i>{{ $booking->customer_name }} {{ $booking->user_id ? '' : '(Offline)' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div>{{ $resDateTime->format('d M Y') }}</div>
                                                <small class="text-muted text-uppercase">{{ $resDateTime->format('H:i') }}</small>
                                            </td>
                                            <td class="fw-bold">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                                            <td>
                                                @php
                                                    $statusClass = 'bg-light-warning text-warning';
                                                    if($booking->status == 'berhasil') $statusClass = 'bg-light-success text-success';
                                                    if($booking->status == 'dibatalkan') $statusClass = 'bg-light-danger text-danger';
                                                @endphp
                                                <span class="badge {{ $statusClass }} rounded-pill px-3">{{ ucfirst($booking->status) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $booking->payment_status == 'paid' ? 'bg-light-success text-success' : 'bg-light-danger text-danger' }} rounded-pill px-3">
                                                    {{ ucfirst($booking->payment_status) }}
                                                </span>
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
                <h5 class="fw-bold mb-0">${booking.treatment.name}</h5>
                <span class="text-muted">#BOOK-${booking.id}</span>
            </div>
            <div class="list-group list-group-flush border-top border-bottom mb-3">
                <div class="list-group-item d-flex justify-content-between px-0">
                    <span class="text-muted">Jadwal</span>
                    <span class="fw-bold text-dark">${formattedDate}</span>
                </div>
                <div class="list-group-item d-flex justify-content-between px-0">
                    <span class="text-muted">Waktu</span>
                    <span class="fw-bold text-dark">${formattedTime} WIB</span>
                </div>
                <div class="list-group-item d-flex justify-content-between px-0">
                    <span class="text-muted">Stylist</span>
                    <span class="fw-bold text-dark">${booking.stylist ? booking.stylist.name : 'Belum Ditentukan'}</span>
                </div>
                <div class="list-group-item d-flex justify-content-between px-0">
                    <span class="text-muted">Total Tagihan</span>
                    <span class="fw-bold text-primary">Rp ${new Intl.NumberFormat('id-ID').format(booking.total_price)}</span>
                </div>
            </div>
            <div class="alert alert-light-info border-0 d-flex align-items-center mb-0">
                <i class="ti ti-info-circle me-2 h4 mb-0"></i>
                <small>Mohon datang 10 menit sebelum jadwal untuk verifikasi.</small>
            </div>
        `;
        
        $('#detailContent').html(html);
        $('#modalDetail').modal('show');
    }

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