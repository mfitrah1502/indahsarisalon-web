<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Indah Sari Salon</title>

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
        <h2 class="text-center mb-3">Reset Password</h2>
        <p class="text-center text-muted mb-4">Masukkan email Anda untuk reset password</p>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('reset.password.email') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-custom">Kirim OTP</button>
        </form>

        <p class="text-center mt-3">
            Kembali ke <a href="{{ route('auth') }}">Login</a>
        </p>
    </div>

</body>

</html>