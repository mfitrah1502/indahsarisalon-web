@extends('layout.dashboard')

@section('title', 'Edit Karyawan')
<!-- [Favicon] icon -->
<link rel="icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon" />
<!-- [Google Font] Family -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap"
    id="main-font-link" />
<link rel="stylesheet" href="{{ asset('assets/fonts/phosphor/duotone/style.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link" />
<link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}" />

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold text-dark">Edit Data Karyawan</h4>
                    <a href="{{ url()->previous() }}" class="btn btn-light rounded-pill px-4 btn-cancel">Kembali</a>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('karyawan.update', $karyawan->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label fw-bold text-muted">Nama Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="ti ti-user"></i></span>
                                    <input type="text" name="name" class="form-control bg-light border-0" value="{{ old('name', $karyawan->name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="ti ti-id"></i></span>
                                    <input type="text" name="username" class="form-control bg-light border-0" value="{{ old('username', $karyawan->username) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label fw-bold text-muted">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="ti ti-mail"></i></span>
                                    <input type="email" name="email" class="form-control bg-light border-0" value="{{ old('email', $karyawan->email) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted">Nomor Telepon</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="ti ti-phone"></i></span>
                                    <input type="text" name="phone" class="form-control bg-light border-0" value="{{ old('phone', $karyawan->phone) }}" required placeholder="Contoh: 08123456789">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label fw-bold text-muted">Role/Jabatan</label>
                                <select name="role" class="form-select bg-light border-0" required id="roleSelect">
                                    <option value="admin" {{ $karyawan->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="karyawan" {{ $karyawan->role == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="kategoriDiv" style="{{ $karyawan->role == 'karyawan' ? '' : 'display:none;' }}">
                                <label class="form-label fw-bold text-muted">Kategori Stylist</label>
                                <select name="kategori" class="form-select bg-light border-0" id="kategoriSelect">
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="senior" {{ old('kategori', $karyawan->kategori) == 'senior' ? 'selected' : '' }}>Senior Stylist</option>
                                    <option value="junior" {{ old('kategori', $karyawan->kategori) == 'junior' ? 'selected' : '' }}>Junior Stylist</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label fw-bold text-muted">Status Akun</label>
                                <select name="status" class="form-select bg-light border-0">
                                    <option value="aktif" {{ $karyawan->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="tidak" {{ $karyawan->status == 'tidak' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                            </div>
                        </div>

                        <hr class="my-4 border-light-subtle">
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('karyawan.index') }}" class="btn btn-light rounded-pill px-4 btn-cancel">Batal</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm"><i class="ti ti-device-floppy me-2"></i> Update Karyawan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts sama persis seperti produk -->
    <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/fonts/custom-font.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/js/theme.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
    <script>
        layout_change('light');
        font_change('Roboto');
        change_box_container('false');
        layout_caption_change('true');
        layout_rtl_change('false');
        preset_change('preset-1');

        function toggleKategoriField() {
            let role = document.getElementById('roleSelect').value;
            let kategoriDiv = document.getElementById('kategoriDiv');
            let kategoriSelect = document.getElementById('kategoriSelect');

            if (role === 'karyawan') {
                kategoriDiv.style.display = 'block';
                kategoriSelect.setAttribute('required', 'required');
            } else {
                kategoriDiv.style.display = 'none';
                kategoriSelect.removeAttribute('required');
                kategoriSelect.value = ''; // kosongkan value
            }
        }

        document.getElementById('roleSelect').addEventListener('change', toggleKategoriField);
        toggleKategoriField(); // jalankan saat halaman load

        // Cek perubahan data
        let isSubmitting = false;
        let initialData = $('form').serialize();

        $('form').on('submit', function() {
            isSubmitting = true;
        });

        function isFormModified() {
            let modified = $('form').serialize() !== initialData;
            $('input[type="file"]').each(function() {
                if (this.files.length > 0) modified = true;
            });
            return modified;
        }

        // Konfirmasi Batal
        $(document).on('click', '.btn-cancel', function(e) {
            e.preventDefault();
            let url = $(this).attr('href');
            
            if (isFormModified()) {
                Swal.fire({
                    title: 'Batalkan perubahan?',
                    text: "Setiap perubahan yang baru Anda buat tidak akan tersimpan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, batalkan!',
                    cancelButtonText: 'Kembali edit'
                }).then((result) => {
                    if (result.isConfirmed) {
                        isSubmitting = true;
                        window.location.href = url;
                    }
                });
            } else {
                isSubmitting = true;
                window.location.href = url;
            }
        });

        // Konfirmasi saat back di browser web
        window.addEventListener('beforeunload', function (e) {
            if (!isSubmitting && isFormModified()) {
                e.preventDefault();
                e.returnValue = ''; 
            }
        });
    </script>
@endsection