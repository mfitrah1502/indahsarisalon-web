@extends('layout.dashboard')

@section('title', 'Edit Karyawan')
<!-- [Favicon] icon -->
<link rel="icon" href="{{ asset('assets/images/indahsarisalonimg.jpg') }}" type="image/x-icon" />
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
            <div class="card">
                <div class="card-header">
                    <h4>Edit Karyawan</h4>
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
                        
                        <h5 class="mb-3 border-bottom pb-2">Informasi Akun</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $karyawan->name) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control" value="{{ old('username', $karyawan->username) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $karyawan->email) }}" required>
                            </div>
                            <!-- Note: Password on edit is usually optional, handled in controller -->
                            <div class="col-md-6 mb-3">
                                <label>Password <small class="text-muted">(Kosongkan jika tidak ingin mengubah)</small></label>
                                <input type="password" name="password" class="form-control">
                            </div>
                        </div>

                        <h5 class="mb-3 border-bottom pb-2 mt-4">Informasi Pribadi</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Nama Panggilan</label>
                                <input type="text" name="nickname" class="form-control" value="{{ old('nickname', $karyawan->nickname) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Jenis Kelamin</label>
                                <select name="gender" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="Laki-laki" {{ old('gender', $karyawan->gender) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ old('gender', $karyawan->gender) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Tempat Lahir</label>
                                <input type="text" name="birth_place" class="form-control" value="{{ old('birth_place', $karyawan->birth_place) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Tanggal Lahir</label>
                                <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date', $karyawan->birth_date) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Pendidikan Terakhir</label>
                                <input type="text" name="last_education" class="form-control" value="{{ old('last_education', $karyawan->last_education) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Nomor Telepon Pribadi</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $karyawan->phone) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Kontak Darurat (Emergency)</label>
                                <input type="text" name="emergency_contact" class="form-control" value="{{ old('emergency_contact', $karyawan->emergency_contact) }}">
                            </div>
                        </div>

                        <h5 class="mb-3 border-bottom pb-2 mt-4">Data Kepegawaian</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Role Akun</label>
                                <select name="role" class="form-select" required id="roleSelect">
                                    <option value="admin" {{ $karyawan->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="karyawan" {{ $karyawan->role == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3" id="kategoriDiv" style="display:none;">
                                <label>Kategori Karyawan</label>
                                <select name="kategori" class="form-select" id="kategoriSelect">
                                    <option value="">-- Pilih --</option>
                                    <option value="senior" {{ old('kategori', $karyawan->kategori) == 'senior' ? 'selected' : '' }}>Senior</option>
                                    <option value="junior" {{ old('kategori', $karyawan->kategori) == 'junior' ? 'selected' : '' }}>Junior</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Posisi / Jabatan</label>
                                <input type="text" name="position" class="form-control" value="{{ old('position', $karyawan->position) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Divisi</label>
                                <input type="text" name="division" class="form-control" value="{{ old('division', $karyawan->division) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Tanggal Bergabung</label>
                                <input type="date" name="join_date" class="form-control" value="{{ old('join_date', $karyawan->join_date) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Status Karyawan (Kontrak/Tetap)</label>
                                <select name="employment_status" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="Tetap" {{ old('employment_status', $karyawan->employment_status) == 'Tetap' ? 'selected' : '' }}>Karyawan Tetap</option>
                                    <option value="Kontrak" {{ old('employment_status', $karyawan->employment_status) == 'Kontrak' ? 'selected' : '' }}>Karyawan Kontrak</option>
                                    <option value="Magang" {{ old('employment_status', $karyawan->employment_status) == 'Magang' ? 'selected' : '' }}>Magang / Freelance</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Status Akun</label>
                                <select name="status" class="form-select">
                                    <option value="aktif" {{ $karyawan->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="tidak" {{ $karyawan->status == 'tidak' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                            </div>
                        </div>

                        <h5 class="mb-3 border-bottom pb-2 mt-4">Informasi Rekening Bank</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Nama Bank</label>
                                <input type="text" name="bank_account_name" class="form-control" value="{{ old('bank_account_name', $karyawan->bank_account_name) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Nomor Rekening</label>
                                <input type="text" name="bank_account_number" class="form-control" value="{{ old('bank_account_number', $karyawan->bank_account_number) }}">
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary px-4">Update Karyawan</button>
                            <a href="{{ route('karyawan.index') }}" class="btn btn-light border px-4 ms-2">Batal</a>
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

            if (role === 'admin') {
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
    </script>
@endsection