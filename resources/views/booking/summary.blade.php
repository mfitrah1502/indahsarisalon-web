@extends('layout.dashboard')

@section('title', 'Ringkasan Booking')

<!-- [Favicon] icon -->
<link rel="icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon" />
<!-- [Google Font] Family -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap"
    id="main-font-link" />
<!-- [phosphor Icons] -->
<link rel="stylesheet" href="{{ asset('assets/fonts/phosphor/duotone/style.css') }}" />
<!-- [Tabler Icons] -->
<link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}" />
<!-- [Feather Icons] -->
<link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}" />
<!-- [Font Awesome Icons] -->
<link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}" />
<!-- [Material Icons] -->
<link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}" />
<!-- [Template CSS Files] -->
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link" />
<link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}" />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Ringkasan Booking</h4>
                    <a href="{{ route('booking.index') }}" class="btn btn-secondary btn-sm">Kembali ke Daftar Treatment</a>
                </div>
                <div class="card-body">
                    <h5>Detail Layanan</h5>
                    <ul class="list-unstyled">
                        @foreach($booking->details as $item)
                        <li>
                            <strong>{{ $item->treatmentDetail->treatment->name }}</strong>: 
                            {{ $item->treatmentDetail->name }} - {{ $item->treatmentDetail->duration }} menit 
                            (Rp {{ number_format($item->price, 0) }})
                        </li>
                        @endforeach
                    </ul>

                    <h5>Stylist</h5>
                    <p>{{ $stylist->name ?? '-' }}</p>

                    <h5>Waktu Reservasi</h5>
                    <p>{{ \Carbon\Carbon::parse($reservation_datetime)->format('d M Y H:i') }}</p>

                    <h5>Total Biaya</h5>
                    <p>Rp {{ number_format($total_price, 0) }}</p>

                    <div class="mt-4">
                        @if ($booking->payment_status == 'unpaid' && $booking->payment_method == 'transfer' && $booking->snap_token)
                            <button class="btn btn-primary" id="pay-button">Bayar Sekarang (Midtrans)</button>
                        @elseif($booking->payment_status == 'unpaid' && $booking->payment_method == 'cash')
                            <div class="alert alert-info">
                                Silakan lakukan pembayaran tunai di kasir.
                            </div>
                            <a href="{{ route('booking.history') }}" class="btn btn-secondary">Lihat Riwayat Booking</a>
                        @else
                            <div class="alert alert-success">
                                Booking Berhasil Dikonfirmasi!
                            </div>
                            <a href="{{ route('booking.history') }}" class="btn btn-primary">Lihat Riwayat Booking</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Midtrans Snap JS -->
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    
    <script type="text/javascript">
        document.getElementById('pay-button')?.onclick = function() {
            snap.pay('{{ $booking->snap_token }}', {
                onSuccess: function(result) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Pembayaran Berhasil!',
                        text: 'Terima kasih, pembayaran Anda telah kami terima.',
                        showConfirmButton: false,
                        timer: 2500
                    }).then(() => {
                        window.location.href = "{{ route('booking.history') }}";
                    });
                },
                onPending: function(result) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Pembayaran Tertaut!',
                        text: 'Silakan selesaikan pembayaran sesuai instruksi di Midtrans.',
                        showConfirmButton: true
                    }).then(() => {
                        window.location.href = "{{ route('booking.history') }}";
                    });
                },
                onError: function(result) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Pembayaran Gagal',
                        text: 'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.'
                    });
                }
            });
        };
    </script>
                </div>
            </div>
        </div>
    </div>

    <!-- Required JS -->
    <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/fonts/custom-font.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/js/theme.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Inisialisasi layout & font
        layout_change('light');
        font_change('Roboto');
        change_box_container('false');
        layout_caption_change('true');
        layout_rtl_change('false');
        preset_change('preset-1');
    </script>

    <script>
        $(document).ready(function () {
            // Konfirmasi sebelum submit booking
            $('#bookingForm').on('submit', function (e) {
                e.preventDefault();
                if (confirm('Apakah Anda yakin ingin melakukan konfirmasi booking ini?')) {
                    this.submit();
                }
            });
        });
    </script>
@endsection