<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Baru - Indah Sari Salon</title>

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

        <h2 class="text-center mb-3">Buat Password Baru</h2>
        <p class="text-center text-muted mb-4">
            Silahkan buat password baru untuk akun Anda.
        </p>


        <form action="{{ route('reset.password.update') }}" method="POST">
            @csrf

            <input type="hidden" name="email" value="{{ request('email') }}">

            <div class="mb-3">
                <label>Password Baru</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn-custom">
                Simpan Password Baru
            </button>

        </form>

        <p class="text-center mt-3">
            Kembali ke <a href="{{ route('auth') }}">Login</a>
        </p>

    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form');
            const password = document.querySelector('input[name="password"]');
            const passwordConfirm = document.querySelector('input[name="password_confirmation"]');

            // Saat submit, cek password
            form.addEventListener('submit', function (e) {
                // Hapus alert sebelumnya
                const existingAlert = document.querySelector('.js-alert');
                if (existingAlert) existingAlert.remove();

                if (password.value !== passwordConfirm.value) {
                    e.preventDefault(); // cegah form submit
                    const alertDiv = document.createElement('div');
                    alertDiv.classList.add('alert', 'alert-danger', 'js-alert');
                    alertDiv.innerText = 'Password dan konfirmasi tidak sama!';
                    form.prepend(alertDiv); // tampilkan di atas form
                }
            });

            // Hilangkan alert saat user fokus di salah satu input
            [password, passwordConfirm].forEach(input => {
                input.addEventListener('focus', function () {
                    const existingAlert = document.querySelector('.js-alert');
                    if (existingAlert) existingAlert.remove();
                });
            });
        });
    </script>
</body>

</html>