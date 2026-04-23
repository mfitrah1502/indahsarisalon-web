@extends('layout.dashboard')

@section('title', 'Manajemen Hari Libur')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">📅 Manajemen Hari Libur</h3>
        <p class="text-muted mb-0">Atur tanggal libur operasional agar pelanggan tidak dapat memesan pada hari tersebut.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-4" role="alert">
        <i class="ti ti-check me-2"></i><strong>Berhasil!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <!-- Kolom Kiri: Form Tambah Libur -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white border-bottom pt-4 pb-3">
                <h5 class="fw-bold text-dark mb-0"><i class="ti ti-calendar-plus me-2 text-primary"></i>Tambah Hari Libur</h5>
            </div>
            <div class="card-body p-4 bg-light bg-opacity-50">
                <form action="{{ route('holidays.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal Libur <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Keterangan (Opsional)</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Misal: Libur Nasional Lebaran / Tutup Sementara"></textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2"><i class="ti ti-device-floppy me-2"></i>Simpan Hari Libur</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Daftar Libur -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-bottom pt-4 pb-3">
                <h5 class="fw-bold text-dark mb-0"><i class="ti ti-calendar-event me-2 text-danger"></i>Daftar Hari Libur</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-muted small fw-bold px-4 py-3">TANGGAL</th>
                                <th class="text-muted small fw-bold py-3">KETERANGAN</th>
                                <th class="text-muted small fw-bold text-end px-4 py-3">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($holidays as $holiday)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light-danger rounded d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <i class="ti ti-calendar text-danger fs-4"></i>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($holiday->date)->translatedFormat('l, d F Y') }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span class="text-muted">{{ $holiday->description ?? '-' }}</span>
                                    </td>
                                    <td class="text-end px-4 py-3">
                                        <form action="{{ route('holidays.destroy', $holiday->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus hari libur ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-light-danger btn-sm rounded-pill px-3 border-0 shadow-sm">
                                                <i class="ti ti-trash me-1"></i>Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <div class="text-muted py-4">
                                            <i class="ti ti-calendar-off fs-1 opacity-25"></i>
                                            <p class="mt-3 mb-0">Belum ada hari libur yang ditambahkan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
