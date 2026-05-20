@extends('layout.dashboard')

@section('title', 'Edit Pelanggan')
<!-- semua link & css sama seperti create -->

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Pelanggan</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <form action="{{ route('pelanggan.update', $pelanggan->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        @if($pelanggan->status === 'guest')
                            <h5 class="mb-3 border-bottom pb-2">Informasi Kontak Guest</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $pelanggan->name) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $pelanggan->email) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Nomor Telepon</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $pelanggan->phone) }}" required placeholder="Contoh: 08123456789">
                                </div>
                            </div>
                        @else
                            <h5 class="mb-3 border-bottom pb-2">Informasi Akun & Kontak</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $pelanggan->name) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Username</label>
                                    <input type="text" name="username" class="form-control" value="{{ old('username', $pelanggan->username) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $pelanggan->email) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Nomor Telepon</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $pelanggan->phone) }}" required placeholder="Contoh: 08123456789">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Password <small class="text-muted">(kosongkan jika tidak ingin ganti)</small></label>
                                    <input type="password" name="password" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Status Akun</label>
                                    <select name="status" class="form-select">
                                        <option value="aktif" {{ $pelanggan->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="tidak" {{ $pelanggan->status == 'tidak' ? 'selected' : '' }}>Tidak Aktif</option>
                                    </select>
                                </div>
                            </div>

                        @endif
                        
                        <input type="hidden" name="role" value="pelanggan">
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary px-4">Update Pelanggan</button>
                            <a href="{{ route('pelanggan.index') }}" class="btn btn-light border px-4 ms-2">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts sama persis -->
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
    </script>
@endsection