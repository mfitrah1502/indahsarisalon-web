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
                        <div class="mb-3">
                            <label>Nama</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $karyawan->name) }}"
                                required>
                        </div>
                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control"
                                value="{{ old('username', $karyawan->username) }}" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control"
                                value="{{ old('email', $karyawan->email) }}" required>
                        </div>
                        <div class="mb-3">
                            <label>Nomor Telepon</label>
                            <input type="text" name="phone" class="form-control"
                                value="{{ old('phone', $karyawan->phone) }}" required placeholder="Contoh: 08123456789">
                        </div>
                        <div class="mb-3">
                            <label>Role</label>
                            <select name="role" class="form-select" required id="roleSelect">
                                <option value="admin" {{ $karyawan->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="karyawan" {{ $karyawan->role == 'karyawan' ? 'selected' : '' }}>Karyawan
                                </option>
                            </select>
                        </div>

                        <!-- Field kategori (hanya untuk karyawan) -->
                        <div class="mb-3" id="kategoriDiv" style="display:none;">
                            <label>Kategori</label>
                            <select name="kategori" class="form-select" id="kategoriSelect">
                                <option value="">-- Pilih Kategori --</option>
                                <option value="senior" {{ old('kategori', $karyawan->kategori) == 'senior' ? 'selected' : '' }}>Senior</option>
                                <option value="junior" {{ old('kategori', $karyawan->kategori) == 'junior' ? 'selected' : '' }}>Junior</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Status</label>
                            <select name="status" class="form-select">
                                <option value="aktif" {{ $karyawan->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="tidak" {{ $karyawan->status == 'tidak' ? 'selected' : '' }}>Tidak Aktif
                                </option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('karyawan.index') }}" class="btn btn-secondary">Batal</a>
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
    </script>
@endsection