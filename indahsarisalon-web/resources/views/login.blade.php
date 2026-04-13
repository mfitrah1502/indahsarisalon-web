<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            font-family: Arial, sans-serif;
        }

        .left-panel {
            flex: 1;
            background: #ffffff;
            padding-left: 0px;
        }

        .right-panel {
            flex: 1;
            background: #e6a7b0;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding-right: 120px;
            padding-left: 120px;
        }

        .auth-card {
            width: 100%;
            max-width: 600px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 40px 30px rgba(0, 0, 0, 0.08);
            padding: 40px;
        }

        .btn-primary {
            background: linear-gradient(90deg, #3b82f6, #2563eb);
            border: none;
        }

        .btn-primary:hover {
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .left-panel {
                display: none;
            }

            .right-panel {
                justify-content: center;
                padding: 20px;
            }
        }
    </style>
</head>

<body>

    <div class="left-panel"></div>

    <div class="right-panel">

        <div class="auth-card">

            <h2 class="text-center mb-2">Login</h2>
            <p class="text-center text-muted mb-4">Silahkan login terlebih dahulu.</p>

            {{-- Alert error --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('login.process') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="fw-bold">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}"
                        class="form-control @error('username') is-invalid @enderror">
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2">
                    Login
                </button>

                <p class="text-center mt-3">
                    Belum memiliki akun? <a href="{{ route('register') }}">Daftar</a>
                </p>

            </form>
        </div>

    </div>
    <!-- Tambahkan tepat sebelum </body> -->
    <script>
        // Ambil semua input di form
        const inputs = document.querySelectorAll('input[name="username"], input[name="password"]');

        // Ambil elemen alert
        const alertBox = document.querySelector('.alert-danger');

        if (alertBox) {
            inputs.forEach(input => {
                input.addEventListener('focus', () => {
                    alertBox.remove(); // Hapus alert saat input difokus
                });
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>