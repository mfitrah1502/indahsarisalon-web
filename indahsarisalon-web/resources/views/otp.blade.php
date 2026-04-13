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
                    <input type="text" name="otp" class="form-control text-center fs-4" placeholder="123456" maxlength="6" required autofocus>
                </div>
                <button type="submit" class="btn-custom py-2" id="btnSubmitOTP">
                    <span class="btn-text">Verifikasi OTP</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>
            </form>
        @endif

        <p class="text-center mt-4 mb-0">
            <a href="{{ route('auth') }}" class="text-secondary text-decoration-none small">
                <i class="ti ti-arrow-left me-1"></i>Kembali ke Login
            </a>
        </p>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const forms = [document.getElementById('forgotForm'), document.getElementById('otpForm')];
            
            forms.forEach(form => {
                if (!form) return;
                form.addEventListener('submit', function () {
                    const btn = form.querySelector('button[type="submit"]');
                    const text = btn.querySelector('.btn-text');
                    const spinner = btn.querySelector('.spinner-border');
                    
                    btn.disabled = true;
                    if(text) text.innerText = 'Mohon Tunggu...';
                    if(spinner) spinner.classList.remove('d-none');
                });
            });
        });
    </script>
</body>

</html>