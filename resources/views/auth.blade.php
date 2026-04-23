<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register | Indah Sari Salon</title>
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/indahsarisalonimg.jpg') }}" type="image/x-icon" />

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons: Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <script>
        window.onpageshow = function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        };
    </script>

    <style>
        :root {
            --primary-color: #EA8290;
            --primary-light: #FFF0F2;
            --secondary-color: #333333;
            --bg-color: #FDFDFD;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--secondary-color);
        }

        /* ===== LAYOUT ===== */
        .auth-master {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        .left-panel {
            flex: 1.2;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 80px;
            position: relative;
            overflow: hidden;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            bottom: -100px;
            right: -100px;
            width: 400px;
            height: 400px;
            background: var(--primary-light);
            border-radius: 50%;
            z-index: 0;
            opacity: 0.5;
        }

        .brand-section {
            position: relative;
            z-index: 1;
        }

        .brand-logo {
            width: 60px;
            height: 60px;
            background: var(--primary-color);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            margin-bottom: 24px;
            box-shadow: 0 8px 16px rgba(234, 130, 144, 0.2);
        }

        .brand-title {
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 16px;
            color: var(--secondary-color);
        }

        .brand-subtitle {
            font-size: 1.1rem;
            color: #666;
            line-height: 1.6;
            margin-bottom: 40px;
            max-width: 450px;
        }

        .value-list {
            display: grid;
            gap: 24px;
        }

        .value-item {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .value-icon {
            width: 44px;
            height: 44px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: 1.2rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
        }

        .value-text h6 {
            margin: 0;
            font-weight: 600;
        }

        .value-text p {
            margin: 0;
            font-size: 0.9rem;
            color: #888;
        }

        .right-panel {
            flex: 1;
            background: linear-gradient(45deg, #FFF0F2, #EA8290, #962536, #EA8290);
            background-size: 300% 300%;
            animation: gradientAnimation 10s ease infinite;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* ===== AUTH CARD & FORMS ===== */
        .auth-wrapper {
            width: 100%;
            max-width: 480px;
            background: #fff;
            border-radius: 24px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.02);
            position: relative;
        }

        .auth-heading {
            text-align: center;
            margin-bottom: 40px;
        }

        .auth-heading h2 {
            font-weight: 700;
            margin-bottom: 8px;
        }

        .auth-heading p {
            color: #999;
            font-size: 0.95rem;
        }

        .form-group-custom {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group-custom label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
            display: block;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #ADB5BD;
            font-size: 1.1rem;
            transition: var(--transition);
        }

        .form-input {
            width: 100%;
            padding: 12px 16px 12px 48px;
            background: #F8F9FA;
            border: 1px solid #F1F3F5;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: var(--transition);
        }

        .form-input:focus {
            outline: none;
            background: #fff;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(234, 130, 144, 0.08);
        }

        .form-input:focus+i {
            color: var(--primary-color);
        }

        .btn-elegant {
            width: 100%;
            padding: 14px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            transition: var(--transition);
            margin-top: 10px;
        }

        .btn-elegant:hover {
            background: #D96A79;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(234, 130, 144, 0.2);
        }

        .auth-footer {
            text-align: center;
            margin-top: 32px;
            font-size: 0.95rem;
            color: #888;
        }

        .auth-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        /* Slider toggle logic */
        .auth-container {
            display: flex;
            width: 200%;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: transform;
        }

        .auth-view {
            width: 50%;
            flex: 0 0 50%;
            padding: 48px;
            box-sizing: border-box;
        }

        .slide-register {
            transform: translateX(-50%);
        }

        @media (max-width: 992px) {
            .left-panel {
                display: none;
            }

            body {
                background: #FFF6F7;
                justify-content: center;
                align-items: center;
            }

            .right-panel {
                padding: 20px;
                width: 100%;
            }

            .auth-wrapper {
                border-radius: 20px;
                padding: 32px;
            }
        }
    </style>
</head>

<body>

    <div class="auth-master">
        <!-- Left Panel: Brand & Values -->
        <div class="left-panel">
            <div class="brand-section">
                <div class="brand-logo">
                    <i class="ti ti-scissors"></i>
                </div>
                <h1 class="brand-title">Indah Sari Salon</h1>
                <p class="brand-subtitle">Rasakan sentuhan kecantikan yang elegan dan profesional untuk menunjang
                    penampilan terbaik Anda setiap hari.</p>

                <div class="value-list">
                    <div class="value-item">
                        <div class="value-icon"><i class="ti ti-sparkles"></i></div>
                        <div class="value-text">
                            <h6>Premium Treatments</h6>
                            <p>Perawatan berkualitas dengan produk terbaik.</p>
                        </div>
                    </div>
                    <div class="value-item">
                        <div class="value-icon"><i class="ti ti-users"></i></div>
                        <div class="value-text">
                            <h6>Expert Stylists</h6>
                            <p>Ditangani oleh tenaga ahli yang berpengalaman.</p>
                        </div>
                    </div>
                    <div class="value-item">
                        <div class="value-icon"><i class="ti ti-calendar-event"></i></div>
                        <div class="value-text">
                            <h6>Online Booking</h6>
                            <p>Pesan jadwal Anda kapan saja dan di mana saja.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: Forms -->
        <div class="right-panel">
            <div class="auth-wrapper">
                <div class="auth-container {{ session('showRegister') || $errors->any() ? 'slide-register' : '' }}"
                    id="authContainer">

                    <!-- LOGIN FORM -->
                    <div class="auth-view" id="loginView">
                        <div class="auth-heading">
                            <h2>Selamat Datang</h2>
                            <p>Silahkan login untuk mengakses layanan kami.</p>
                        </div>

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show border-0 small py-2">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('info'))
                            <div class="alert alert-info alert-dismissible fade show border-0 small py-2 d-flex align-items-center">
                                <i class="ti ti-info-circle me-2 fs-5"></i>
                                <span>{{ session('info') }}</span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form id="loginForm" action="{{ route('login.process') }}" method="POST">
                            @csrf
                            <div id="loginAlert"></div>

                            <div class="form-group-custom">
                                <label>Username</label>
                                <div class="input-wrapper">
                                    <input type="text" name="username" class="form-input" value="{{ old('username') }}"
                                        placeholder="username">
                                    <i class="ti ti-user"></i>
                                </div>
                            </div>

                            <div class="form-group-custom">
                                <label>Password</label>
                                <div class="input-wrapper">
                                    <input type="password" name="password" class="form-input" placeholder="password">
                                    <i class="ti ti-lock"></i>
                                </div>
                            </div>

                            <div class="text-end mb-3">
                                <a href="{{ route('reset.password') }}"
                                    class="small text-muted text-decoration-none">Lupa password?</a>
                            </div>

                            <button type="submit" class="btn-elegant">Login</button>
                        </form>

                        <div class="auth-footer">
                            Belum memiliki akun? <a href="#" id="toRegister">Daftar Sekarang</a>
                        </div>
                    </div>

                    <!-- REGISTER FORM -->
                    <div class="auth-view" id="registerView">
                        <div class="auth-heading">
                            <h2>Join Us</h2>
                            <p>Buat akun Anda dan nikmati layanan terbaik.</p>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show border-0 small py-2">
                                <ul class="mb-0 list-unstyled">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form id="registerForm" action="{{ route('register.process') }}" method="POST">
                            @csrf
                            <div id="registerAlert"></div>

                            <div class="form-group-custom">
                                <label>Nama Lengkap</label>
                                <div class="input-wrapper">
                                    <input type="text" name="name" class="form-input" value="{{ old('name') }}"
                                        placeholder="Your name">
                                    <i class="ti ti-id"></i>
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="form-group-custom">
                                        <label>Email</label>
                                        <div class="input-wrapper">
                                            <input type="email" name="email" class="form-input"
                                                value="{{ old('email') }}" placeholder="email@example.com">
                                            <i class="ti ti-mail"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group-custom">
                                        <label>Telepon</label>
                                        <div class="input-wrapper">
                                            <input type="text" name="phone" class="form-input"
                                                value="{{ old('phone') }}" placeholder="08xxxxxxx">
                                            <i class="ti ti-phone"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group-custom">
                                <label>Username</label>
                                <div class="input-wrapper">
                                    <input type="text" name="username" class="form-input" value="{{ old('username') }}"
                                        placeholder="username">
                                    <i class="ti ti-user-circle"></i>
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="form-group-custom">
                                        <label>Password</label>
                                        <div class="input-wrapper">
                                            <input type="password" name="password" class="form-input"
                                                placeholder="Min. 6 character">
                                            <i class="ti ti-lock"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group-custom">
                                        <label>Konfirmasi</label>
                                        <div class="input-wrapper">
                                            <input type="password" name="password_confirmation" class="form-input"
                                                placeholder="Confirm password">
                                            <i class="ti ti-lock-check"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn-elegant">Buat Akun</button>
                        </form>

                        <div class="auth-footer">
                            Sudah punya akun? <a href="#" id="toLogin">Kembali Login</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script>
        const container = document.getElementById('authContainer');
        const toRegister = document.getElementById('toRegister');
        const toLogin = document.getElementById('toLogin');
        const loginForm = document.getElementById('loginForm');
        const loginAlert = document.getElementById('loginAlert');
        const registerForm = document.getElementById('registerForm');
        const registerAlert = document.getElementById('registerAlert');

        // Toggle Views
        toRegister.addEventListener('click', (e) => {
            e.preventDefault();
            container.classList.add('slide-register');
            clearAlerts();
        });

        toLogin.addEventListener('click', (e) => {
            e.preventDefault();
            container.classList.remove('slide-register');
            clearAlerts();
        });

        function clearAlerts() {
            loginAlert.innerHTML = '';
            registerAlert.innerHTML = '';
        }

        // Login Validation
        loginForm.addEventListener('submit', (e) => {
            const user = loginForm.querySelector('input[name="username"]').value.trim();
            const pass = loginForm.querySelector('input[name="password"]').value.trim();
            if (!user || !pass) {
                e.preventDefault();
                loginAlert.innerHTML = `<div class="alert alert-warning border-0 small py-2 mb-3">Harap isi username dan password.</div>`;
            }
        });

        // Register Validation
        registerForm.addEventListener('submit', (e) => {
            const fields = ['name', 'email', 'phone', 'username', 'password', 'password_confirmation'];
            let empty = false;
            fields.forEach(f => {
                if (!registerForm.querySelector(`input[name="${f}"]`).value.trim()) empty = true;
            });

            if (empty) {
                e.preventDefault();
                registerAlert.innerHTML = `<div class="alert alert-warning border-0 small py-2 mb-3">Harap lengkapi semua data diri Anda.</div>`;
                return;
            }

            const p1 = registerForm.querySelector('input[name="password"]').value;
            const p2 = registerForm.querySelector('input[name="password_confirmation"]').value;
            if (p1 !== p2) {
                e.preventDefault();
                registerAlert.innerHTML = `<div class="alert alert-danger border-0 small py-2 mb-3">Password konfirmasi tidak sesuai.</div>`;
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>