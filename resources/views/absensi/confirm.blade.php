@extends('layout.dashboard')

@section('title', 'Konfirmasi Presensi')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-lg border-0 mt-4 overflow-hidden">
            <div class="card-header bg-primary text-white text-center py-4">
                <i class="ti ti-clock-check mb-2" style="font-size: 3rem;"></i>
                <h4 class="text-white mb-0">Konfirmasi Presensi</h4>
            </div>
            
            <div class="card-body p-4 text-center">
                @if(isset($alreadyDone) && $alreadyDone)
                    <div class="py-4">
                        <div class="mb-3"><i class="ti ti-circle-check-filled text-success" style="font-size: 4rem;"></i></div>
                        <h4 class="fw-bold">{{ $message }}</h4>
                        <p class="text-muted">Anda dapat menutup halaman ini sekarang.</p>
                        <div class="d-grid mt-4">
                            <button onclick="handleFinish()" class="btn btn-primary btn-lg">OK</button>
                        </div>
                    </div>
                @else
                    <!-- INFO WAKTU & TANGGAL -->
                    <div class="mb-4">
                        <div class="d-inline-block px-3 py-1 rounded-pill bg-light-primary text-primary mb-2 small fw-bold">
                            <i class="ti ti-calendar-event me-1"></i> Detail Tanggal Scan
                        </div>
                        <h4 class="fw-bold mb-3">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</h4>
                        
                        <div class="display-1 fw-bold text-dark my-2" id="current-time" style="letter-spacing: -2px;">00:00:00</div>
                    </div>

                    <div class="card bg-light border-0 mb-4 text-start">
                        <div class="card-body py-3 px-4">
                            <p class="text-muted mb-1 small uppercase fw-bold" style="letter-spacing: 1px;">Petugas Presensi</p>
                            <h5 class="mb-0 text-dark fw-bold">{{ $user->name }}</h5>
                            <hr class="my-3 opacity-25">
                            <p class="text-muted mb-1 small uppercase fw-bold" style="letter-spacing: 1px;">Jenis Presensi Terdeteksi</p>
                            <h3 class="mb-0 text-primary fw-bold">CHECK {{ strtoupper($type) }}</h3>
                        </div>
                    </div>

                    <!-- FORM KONFIRMASI -->
                    <form id="attendance-form" action="{{ route('absensi.processQR') }}" method="POST">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        
                        <div id="action-buttons">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg py-3 shadow-lg fs-5 fw-bold" id="btn-submit">
                                    <i class="ti ti-check me-2"></i> Konfirmasi Selesai
                                </button>
                                <a href="{{ route('dashboard') }}" class="btn btn-link text-muted mt-2">Batal & Kembali</a>
                            </div>
                        </div>
                    </form>

                    <!-- SUCCESS STATE (Hidden Initially) -->
                    <div id="success-state" style="display: none;" class="py-4">
                        <div class="mb-3"><i class="ti ti-circle-check-filled text-success" style="font-size: 5rem;"></i></div>
                        <h3 class="fw-bold">Anda telah presensi!</h3>
                        <p class="text-muted" id="success-msg"></p>
                        <div class="d-grid mt-4">
                            <button onclick="handleFinish()" class="btn btn-primary btn-lg py-3">OK</button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Update Jam Real-time
    function updateClock() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('id-ID', { hour12: false });
        const timeElement = document.getElementById('current-time');
        if (timeElement) timeElement.innerText = timeStr;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Handle Form Submit
    const form = document.getElementById('attendance-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const btnSubmit = document.getElementById('btn-submit');
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Memproses...';

            const formData = new FormData(this);
            const token = formData.get('token');

            fetch(this.action, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: JSON.stringify({ token: token })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('attendance-form').style.display = 'none';
                    document.getElementById('current-time').parentElement.style.display = 'none';
                    document.querySelector('.card.bg-light').style.display = 'none';
                    
                    document.getElementById('success-msg').innerText = data.message;
                    document.getElementById('success-state').style.display = 'block';
                } else {
                    alert(data.message);
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="ti ti-check me-2"></i> Konfirmasi Selesai';
                }
            })
            .catch(error => {
                alert("Terjadi kesalahan sistem.");
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="ti ti-check me-2"></i> Konfirmasi Selesai';
            });
        });
    }

    function handleFinish() {
        window.close();
        setTimeout(() => {
            window.location.href = "{{ route('dashboard') }}";
        }, 500);
    }
</script>
@endpush

<style>
    .card-header {
        background: linear-gradient(45deg, #EA8290, #D96A79) !important;
    }
    .btn-primary {
        background-color: #EA8290 !important;
        border-color: #EA8290 !important;
    }
    .btn-primary:hover {
        background-color: #D96A79 !important;
    }
    .bg-light-primary {
        background-color: rgba(234, 130, 144, 0.1) !important;
    }
</style>
@endsection
