<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masukkan OTP - Indah Sari Salon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            font-family: Arial, sans-serif;
            justify-content: center;
            align-items: center;
            background: #ffc0cb;
        }

        .auth-wrapper {
            width: 400px;
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 40px 30px rgba(0, 0, 0, 0.08);
        }

        .btn-custom {
            background-color: #ea8290;
            color: white;
            border-radius: 16px;
            width: 100%;
            border: none;
            padding: 12px;
            font-weight: bold;
            font-size: 16px;
        }

        .btn-custom:hover {
            background-color: #d9727f;
        }
    </style>
</head>

<body>

    <div class="auth-wrapper">
        <h2 class="text-center mb-3">Masukkan OTP</h2>
        <p class="text-center text-muted mb-4">Periksa email Anda dan masukkan kode OTP serta password baru.</p>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('reset.password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="email" value="{{ request('email') }}">

            <div class="mb-3">
                <label>Kode OTP</label>
                <input type="text" name="otp" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Password Baru</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn-custom">Reset Password</button>
        </form>

        <p class="text-center mt-3">
            Kembali ke <a href="{{ route('auth') }}">Login</a>
        </p>
    </div>

</body>

</html>