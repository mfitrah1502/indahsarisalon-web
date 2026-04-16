@extends('layout.dashboard')

@section('title', 'QR Presensi Harian')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 text-center">
        <div class="card shadow-lg border-0 py-5">
            <div class="card-body">
                <h3 class="mb-2">📅 QR Presensi Salon</h3>
                <p class="text-muted mb-4">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                
                <!-- QR Container -->
                <div id="qrcode" class="d-flex justify-content-center mb-4 p-3 bg-white rounded shadow-sm mx-auto" style="width: fit-content;"></div>

                <div class="alert alert-light-primary border-primary border-opacity-25 py-2">
                    <i class="ti ti-info-circle me-1"></i> Minta karyawan scan QR di atas untuk absen masuk/keluar.
                </div>
                
                <div class="mt-2 text-muted small">
                    <strong>URL Terdeteksi:</strong> <code id="url-display">...</code><br>
                    <span class="text-danger" id="ip-warning" style="display:none;">
                        <i class="ti ti-alert-triangle"></i> Peringatan: Anda menggunakan 'localhost'. Scan tidak akan bekerja di HP!
                    </span>
                </div>

                <div class="mt-4">
                    <button onclick="window.location.reload()" class="btn btn-light-primary btn-sm">
                        <i class="ti ti-refresh me-1"></i> Refresh QR
                    </button>
                    <button onclick="window.print()" class="btn btn-light-secondary btn-sm ms-2">
                        <i class="ti ti-printer me-1"></i> Cetak QR
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Paksa menggunakan APP_URL dari .env agar selalu mengarah ke IP, bukan localhost
        const baseUrl = "{{ config('app.url') }}";
        const relativeUrl = "{{ route('absensi.confirmation', ['token' => $token], false) }}";
        const confirmationUrl = baseUrl + relativeUrl;
        
        // Update UI info
        const urlDisplay = document.getElementById('url-display');
        const ipWarning = document.getElementById('ip-warning');
        
        if (urlDisplay) urlDisplay.innerText = confirmationUrl;
        
        if (ipWarning && (confirmationUrl.includes('localhost') || confirmationUrl.includes('127.0.0.1'))) {
            ipWarning.style.display = 'block';
        }

        // Generate QR
        new QRCode(document.getElementById("qrcode"), {
            text: confirmationUrl,
            width: 256,
            height: 256,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    });
</script>
@endpush

<style>
    @media print {
        .pc-sidebar, .pc-header, .btn, .alert {
            display: none !important;
        }
        .pc-container {
            padding: 0 !important;
            margin: 0 !important;
        }
        .card {
            box-shadow: none !important;
            border: none !important;
        }
    }
</style>
@endsection