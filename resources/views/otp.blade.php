<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masukkan OTP - Indah Sari Salon</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #ffc0cb;
            font-family: Arial;
        }

        .auth-wrapper {
            width: 400px;
            background: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 40px 30px rgba(0, 0, 0, 0.08);
        }

        .btn-custom {
            background: #ea8290;
            color: white;
            border: none;
            width: 100%;
            padding: 12px;
            border-radius: 16px;
            font-weight: bold;
        }

        .btn-custom:hover {
            background: #d9727f;
        }
    </style>

</head>

<body>

    <div class="auth-wrapper">
        @php
            $email = request('email');
            $step = $email ? 2 : 1;
        @endphp

        <h2 class="text-center mb-3 text-dark fw-bold">
            {{ $step === 1 ? 'Lupa Kata Sandi' : 'Verifikasi OTP' }}
        </h2>
        <p class="text-center text-muted mb-4">
            {{ $step === 1 
                ? 'Masukkan email terdaftar Anda untuk menerima kode OTP.' 
                : 'Silahkan masukkan kode OTP yang telah dikirim ke email: ' . $email }}
        </p>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($step === 1)
            {{-- Step 1: Input Email --}}
            <form action="{{ route('reset.password.email') }}" method="POST" id="forgotForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-bold">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                </div>
                <button type="submit" class="btn-custom py-2" id="btnSubmit">
                    <span class="btn-text">Kirim Kode OTP</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>
            </form>
        @else
            {{-- Step 2: Input OTP --}}
            <form action="{{ route('otp.verify') }}" method="POST" id="otpForm">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Kode OTP</label>
                    <input type="text" name="otp" class="form-control text-center fs-4" placeholder="xxxxxx" maxlength="6" required autofocus>
                </div>
                <button type="submit" class="btn-custom py-2" id="btnSubmitOTP">
                    <span class="btn-text">Verifikasi OTP</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>
            </form>

            <div class="text-center mt-3">
                <span class="small text-muted">Tidak menerima kode? </span>
                <form action="{{ route('reset.password.resend') }}" method="POST" id="resendForm" class="d-inline">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <button type="submit" id="btnResend" class="btn btn-link text-decoration-none p-0 align-baseline small fw-bold" style="font-size: 0.875rem; color: #ea8290 !important;">
                        Kirim Ulang
                    </button>
                </form>
                <span id="cooldownText" class="small text-muted d-none">Kirim ulang dalam <strong id="countdown">60</strong>s</span>
            </div>
        @endif

        <p class="text-center mt-4 mb-0">
            <a href="{{ route('auth') }}" class="text-secondary text-decoration-none small">
                <i class="ti ti-arrow-left me-1"></i>Kembali ke Login
            </a>
        </p>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const forms = [
                document.getElementById('forgotForm'), 
                document.getElementById('otpForm'),
                document.getElementById('resendForm')
            ];
            
            forms.forEach(form => {
                if (!form) return;
                form.addEventListener('submit', function () {
                    const btn = form.querySelector('button[type="submit"]');
                    if (!btn) return;
                    const text = btn.querySelector('.btn-text');
                    const spinner = btn.querySelector('.spinner-border');
                    
                    btn.disabled = true;
                    if(text) text.innerText = 'Mohon Tunggu...';
                    if(spinner) spinner.classList.remove('d-none');
                });
            });

            // Cooldown for OTP Resend
            const btnResend = document.getElementById('btnResend');
            const cooldownText = document.getElementById('cooldownText');
            const countdownEl = document.getElementById('countdown');
            const resendForm = document.getElementById('resendForm');

            if (btnResend && cooldownText && countdownEl && resendForm) {
                const COOLDOWN_TIME = 60; // seconds
                const storageKey = 'otp_resend_cooldown_' + encodeURIComponent('{{ $email }}');
                
                function getRemainingTime() {
                    const expiry = localStorage.getItem(storageKey);
                    if (!expiry) return 0;
                    const remaining = Math.ceil((parseInt(expiry) - Date.now()) / 1000);
                    return remaining > 0 ? remaining : 0;
                }

                function startTimer(seconds) {
                    btnResend.classList.add('d-none');
                    cooldownText.classList.remove('d-none');
                    countdownEl.textContent = seconds;

                    const interval = setInterval(() => {
                        const remaining = getRemainingTime();
                        if (remaining <= 0) {
                            clearInterval(interval);
                            btnResend.classList.remove('d-none');
                            cooldownText.classList.add('d-none');
                            localStorage.removeItem(storageKey);
                        } else {
                            countdownEl.textContent = remaining;
                        }
                    }, 1000);
                }

                resendForm.addEventListener('submit', function () {
                    const expiryTime = Date.now() + (COOLDOWN_TIME * 1000);
                    localStorage.setItem(storageKey, expiryTime);
                });

                const remaining = getRemainingTime();
                if (remaining > 0) {
                    startTimer(remaining);
                } else {
                    @if(session('success'))
                        const expiryTime = Date.now() + (COOLDOWN_TIME * 1000);
                        localStorage.setItem(storageKey, expiryTime);
                        startTimer(COOLDOWN_TIME);
                    @endif
                }
            }
        });
    </script>
</body>

</html>