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
                        <div class="mb-3">
                            <label>Nama</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $pelanggan->name) }}"
                                required>
                        </div>
                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control"
                                value="{{ old('username', $pelanggan->username) }}" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control"
                                value="{{ old('email', $pelanggan->email) }}" required>
                        </div>
                        <div class="mb-3">
                            <label>Nomor Telepon</label>
                            <input type="text" name="phone" class="form-control"
                                value="{{ old('phone', $pelanggan->phone) }}" required placeholder="Contoh: 08123456789">
                        </div>
                        <div class="mb-3">
                            <label>Password <small>(kosongkan jika tidak ingin ganti)</small></label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <input type="hidden" name="role" value="pelanggan">
                        <div class="mb-3">
                            <label>Status</label>
                            <select name="status" class="form-select">
                                <option value="aktif" {{ $pelanggan->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="tidak" {{ $pelanggan->status == 'tidak' ? 'selected' : '' }}>Tidak Aktif
                                </option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('pelanggan.index') }}" class="btn btn-secondary">Batal</a>
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