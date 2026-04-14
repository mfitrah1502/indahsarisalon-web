@extends('layout.dashboard')

@section('title', 'My Profile')

@section('content')

    <style>
        .profile-card {
            background: linear-gradient(135deg, #EA8290 0%, #f7a7b3 100%);
            border: none;
            border-radius: 15px;
            color: white;
        }

        .profile-img-container {
            width: 120px;
            height: 120px;
            margin: 0 auto;
            border: 5px solid rgba(255, 255, 255, 0.2);
            padding: 5px;
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .profile-img-container:hover {
            border-color: rgba(255, 255, 255, 0.5);
            transform: scale(1.02);
        }

        .camera-overlay {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: #fff;
            color: #EA8290;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 10;
        }

        .profile-img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .info-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .info-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            color: #6c757d;
            letter-spacing: 1px;
        }

        .btn-pink {
            background-color: #EA8290;
            border-color: #EA8290;
            color: white;
        }

        .btn-pink:hover {
            background-color: #d6717e;
            border-color: #d6717e;
            color: white;
        }

        .btn-light-pink {
            background-color: #fcebed;
            color: #EA8290;
            border: none;
        }

        .btn-light-pink:hover {
            background-color: #f7d7db;
            color: #EA8290;
        }

        .text-pink {
            color: #EA8290 !important;
        }
    </style>

    <div class="row">
        <!-- LEFT COLUMN: PHOTO & BASIC INFO -->
        <div class="col-xl-4">
            <div class="card profile-card shadow-lg mb-4">
                <div class="card-body text-center py-5">
                    <form id="formAvatar" enctype="multipart/form-data">
                        @csrf
                        <label for="avatarInput" class="profile-img-container rounded-circle mb-3 d-block" id="btnChangeAvatar">
                            <img src="{{ Auth::user()->avatar_url }}" 
                                 class="rounded-circle shadow w-100 h-100" 
                                 id="profileImgPreview"
                                 style="object-fit: cover;"
                                 alt="User Profile">
                            <div class="camera-overlay">
                                <i class="ti ti-camera fs-5"></i>
                            </div>
                        </label>
                        <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/*">
                    </form>
                    <h3 class="text-white mb-1">{{ Auth::user()->name }}</h3>
                    <p class="text-white-50 mb-3">{{ ucfirst(Auth::user()->role) }}</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">Quick Links</h5>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('booking.history') }}"
                            class="list-group-item list-group-item-action border-0 px-0 d-flex align-items-center">
                            <i class="ti ti-history fs-4 me-3 text-pink"></i> History Booking
                        </a>
                        <a href="{{ route('dashboard') }}"
                            class="list-group-item list-group-item-action border-0 px-0 d-flex align-items-center">
                            <i class="ti ti-smart-home fs-4 me-3 text-pink"></i> Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: ACCOUNT DETAILS -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">🔍 Detail Akun Saya</h4>
                    <button class="btn btn-sm btn-light-pink" data-bs-toggle="modal" data-bs-target="#modalEditProfile">
                        <i class="ti ti-edit me-1"></i> Edit Info
                    </button>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="info-box border">
                                <span class="info-label">Nama Lengkap</span>
                                <div class="fw-bold mt-1 fs-5 text-dark" id="display-name">{{ Auth::user()->name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="info-box border">
                                <span class="info-label">Email Address</span>
                                <div class="fw-bold mt-1 fs-5 text-dark">{{ Auth::user()->email }}</div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="info-box border">
                                <span class="info-label">Nomor Telepon</span>
                                <div class="fw-bold mt-1 fs-5 text-dark" id="display-phone">{{ Auth::user()->phone ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="info-box border">
                                <span class="info-label">Username</span>
                                <div class="fw-bold mt-1 fs-5 text-dark" id="display-username">@
                                    {{ Auth::user()->username ?? strtolower(str_replace(' ', '', Auth::user()->name)) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="info-box border">
                                <span class="info-label">Role Akun</span>
                                <div>
                                    <span
                                        class="badge bg-light-success text-success fs-6 mt-1">{{ ucfirst(Auth::user()->role) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <h5 class="mb-3">Tindakan Keamanan</h5>
                        <button class="btn btn-pink" data-bs-toggle="modal" data-bs-target="#modalPassword">
                            <i class="ti ti-lock me-1"></i> Ubah Password
                        </button>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    </div>

    <!-- MODAL EDIT PROFILE INFO -->
    <div class="modal fade" id="modalEditProfile" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title">📝 Edit Informasi Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="formEditProfile">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="{{ Auth::user()->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text">@</span>
                                <input type="text" name="username" class="form-control" value="{{ Auth::user()->username }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text" name="phone" class="form-control" value="{{ Auth::user()->phone }}" required placeholder="08123456789">
                        </div>
                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-pink px-4">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL UBAH PASSWORD -->
    <div class="modal fade" id="modalPassword" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title" id="modalTitle">🔐 Pengaturan Keamanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <!-- VIEW 1: CHANGE PASSWORD -->
                <div id="viewChangePassword" class="modal-body p-4">
                    <p class="text-muted small mb-4">Pastikan Anda menggunakan kata sandi yang kuat untuk menjaga keamanan akun.</p>
                    <form id="formChangePassword">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Kata Sandi Saat Ini</label>
                            <input type="password" name="current_password" class="form-control" required placeholder="••••••••">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kata Sandi Baru</label>
                            <input type="password" name="new_password" class="form-control" required placeholder="Minimal 8 karakter">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="new_password_confirmation" class="form-control" required placeholder="Ulangi kata sandi baru">
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="javascript:void(0)" id="btnToOtp" class="text-pink small fw-bold text-decoration-underline">Lupa kata sandi?</a>
                            <button type="submit" class="btn btn-pink px-4">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>

                <!-- VIEW 2: OTP FORGOT PASSWORD -->
                <div id="viewOtp" class="modal-body p-4 d-none">
                    <div class="text-center mb-4">
                        <div class="avtar avtar-lg bg-light-warning rounded-circle mx-auto mb-3">
                            <i class="ti ti-mail-forward fs-2 text-warning"></i>
                        </div>
                        <h5 class="mb-1">Verifikasi Kode OTP</h5>
                        <p class="text-muted small">Kami telah mengirimkan kode OTP ke email Anda ({{ Auth::user()->email }}). Silakan cek kotak masuk atau spam.</p>
                    </div>
                    
                    <form id="formVerifyOtp">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label text-center d-block">Masukkan 6 Digit Kode OTP</label>
                            <input type="text" name="otp" class="form-control text-center fs-4 fw-bold letter-spacing-5" maxlength="6" required placeholder="000000">
                        </div>
                        <div class="mb-3 mt-4">
                            <label class="form-label">Set Kata Sandi Baru</label>
                            <input type="password" name="new_password" class="form-control" required placeholder="Minimal 8 karakter">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="new_password_confirmation" class="form-control" required placeholder="Ulangi kata sandi">
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning fw-bold">Verifikasi & Reset Password</button>
                            <button type="button" id="btnBackToLogin" class="btn btn-link link-secondary btn-sm mt-1">Kembali</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initial Config (Safe Check)
            if (typeof layout_change === 'function') {
                layout_change('light');
                font_change('Roboto');
                change_box_container('false');
                layout_caption_change('true');
                layout_rtl_change('false');
                preset_change('preset-1');
            }

            // --- Switch View Logic ---
            $('#btnToOtp').on('click', function() {
                // Sembunyikan modal utama dulu
                $('#modalPassword').modal('hide');

                // Trigger OTP Send AJAX
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang mengirimkan kode OTP ke email Anda.',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                $.ajax({
                    url: "{{ route('profile.otp.send') }}",
                    method: "POST",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(res) {
                        Swal.close();
                        // Ganti view ke OTP
                        $('#viewChangePassword').addClass('d-none');
                        $('#viewOtp').removeClass('d-none');
                        $('#modalTitle').text('🔑 Verifikasi OTP');
                        
                        // Tampilkan modal lagi dengan view OTP
                        $('#modalPassword').modal('show');
                    },
                    error: function(err) {
                        Swal.fire('Gagal', 'Gagal mengirim OTP. Silakan coba lagi.', 'error');
                        // Tampilkan modal kembali ke awal jika gagal
                        $('#modalPassword').modal('show');
                    }
                });
            });

            $('#btnBackToLogin').on('click', function() {
                $('#viewOtp').addClass('d-none');
                $('#viewChangePassword').removeClass('d-none');
                $('#modalTitle').text('🔐 Pengaturan Keamanan');
            });

            // --- AJAX: Change Password (Normal) ---
            $('#formChangePassword').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('profile.change-password') }}",
                    method: "POST",
                    data: formData,
                    success: function(res) {
                        $('#modalPassword').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Kata sandi Anda telah diperbarui.',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        $('#formChangePassword')[0].reset();
                    },
                    error: function(err) {
                        let msg = err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan.';
                        Swal.fire('Oops...', msg, 'error');
                    }
                });
            });

            // --- AJAX: Verify OTP & Reset ---
            $('#formVerifyOtp').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('profile.otp.verify') }}",
                    method: "POST",
                    data: formData,
                    success: function(res) {
                        $('#modalPassword').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Verified!',
                            text: 'Password berhasil diubah menggunakan OTP.',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(err) {
                        let msg = err.responseJSON ? err.responseJSON.message : 'Kode OTP salah atau tidak valid.';
                        Swal.fire('Gagal', msg, 'error');
                    }
                });
            });

            // --- AJAX: Edit Profile Info ---
            $('#formEditProfile').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('profile.update-info') }}",
                    method: "POST",
                    data: formData,
                    success: function(res) {
                        $('#modalEditProfile').modal('hide');
                        
                        // Update UI display
                        $('#display-name').text(res.name);
                        $('#display-username').text('@ ' + res.username);
                        $('#display-phone').text(res.phone || '-');
                        $('.profile-card h3').text(res.name);
                        $('.pc-header .small.text-muted').text(res.name);

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function(err) {
                        let msg = err.responseJSON ? err.responseJSON.message : 'Gagal memperbarui profil.';
                        Swal.fire('Oops...', msg, 'error');
                    }
                });
            });

            // --- AJAX: Update Avatar ---
            $('#avatarInput').on('change', function() {
                let file = this.files[0];
                if (!file) return;

                let formData = new FormData();
                formData.append('avatar', file);
                formData.append('_token', "{{ csrf_token() }}");

                Swal.fire({
                    title: 'Mengunggah...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                $.ajax({
                    url: "{{ route('profile.avatar.update') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        // Update preview
                        $('#profileImgPreview').attr('src', res.avatar_url);
                        // Update header avatar too if possible (by selector)
                        $('.user-avtar').attr('src', res.avatar_url);

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function(err) {
                        let msg = err.responseJSON ? err.responseJSON.message : 'Gagal mengunggah foto.';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });
        });
    </script>
@endpush
@endsection