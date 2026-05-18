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

                        <h5 class="mb-3 border-bottom pb-2 mt-4">Data Transaksi & Keanggotaan</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>Membership Tier</label>
                                <select name="membership_tier" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="Platinum" {{ old('membership_tier', $pelanggan->membership_tier) == 'Platinum' ? 'selected' : '' }}>Platinum</option>
                                    <option value="Gold" {{ old('membership_tier', $pelanggan->membership_tier) == 'Gold' ? 'selected' : '' }}>Gold</option>
                                    <option value="Silver" {{ old('membership_tier', $pelanggan->membership_tier) == 'Silver' ? 'selected' : '' }}>Silver</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Total Spend (Rp)</label>
                                <input type="number" name="total_spend" class="form-control" value="{{ old('total_spend', $pelanggan->total_spend) }}" min="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Tanggal Transaksi Terakhir</label>
                                @php
                                    $lastTrx = $pelanggan->last_transaction_at ? date('Y-m-d\TH:i', strtotime($pelanggan->last_transaction_at)) : '';
                                @endphp
                                <input type="datetime-local" name="last_transaction_at" class="form-control" value="{{ old('last_transaction_at', $lastTrx) }}">
                            </div>
                        </div>
                        
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