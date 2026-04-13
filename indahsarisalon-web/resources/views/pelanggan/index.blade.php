@extends('layout.dashboard')

@section('title', 'Manajemen Pelanggan')
<style>
    .popup-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .popup-box {
        background: #fff;
        padding: 25px;
        border-radius: 8px;
        width: 500px;
        max-height: 80vh;
        overflow: auto;
        position: relative;
    }

    .popup-close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 22px;
        cursor: pointer;
    }
</style>

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold text-dark">Daftar Pelanggan</h4>
                    <a href="{{ route('pelanggan.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="ti ti-plus me-1"></i> Tambah Pelanggan
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success m-3">{{ session('success') }}
                    </div>
                @endif

                <div class="card-body">
                    <ul class="nav nav-tabs mb-4" id="pelangganTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="aktif-tab" data-bs-toggle="tab" data-bs-target="#aktif" type="button" role="tab" aria-controls="aktif" aria-selected="true">
                                <i class="ti ti-users me-2"></i>Daftar Aktif
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="trash-tab" data-bs-toggle="tab" data-bs-target="#trash" type="button" role="tab" aria-controls="trash" aria-selected="false">
                                <i class="ti ti-trash me-2"></i>Keranjang Sampah 
                                @if($trashedPelanggans->count() > 0)
                                    <span class="badge bg-danger ms-2">{{ $trashedPelanggans->count() }}</span>
                                @endif
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="pelangganTabContent">
                        <!-- TAB AKTIF -->
                        <div class="tab-pane fade show active" id="aktif" role="tabpanel" aria-labelledby="aktif-tab">
                            <div class="d-flex mb-4">
                                <div class="input-group" style="max-width: 400px;">
                                    <span class="input-group-text bg-light border-0"><i class="ti ti-search text-muted"></i></span>
                                    <input type="text" id="searchInput" class="form-control bg-light border-0" placeholder="Cari pelanggan..." value="{{ request('search') }}">
                                </div>
                            </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th class="ps-3 border-0 rounded-start">No</th>
                                    <th class="border-0">Nama</th>
                                    <th class="border-0">Email</th>
                                    <th class="border-0">Telepon</th>
                                    <th class="border-0 text-center">Status</th>
                                    <th class="pe-3 border-0 rounded-end text-center" width="150">Aksi</th>
                                </tr>
                            </thead>
                        <tbody>
                            @forelse($pelanggans as $index => $pelanggan)
                                <tr class="pelanggan-row" data-name="{{ $pelanggan->name }}"
                                    data-username="{{ $pelanggan->username }}" data-email="{{ $pelanggan->email }}"
                                    data-phone="{{ $pelanggan->phone }}"
                                    data-status="{{ $pelanggan->status }}">
                                    <td class="ps-3">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light-info text-info rounded-circle me-2 d-flex align-items-center justify-content-center fw-bold" style="width: 35px; height: 35px;">
                                                {{ strtoupper(substr($pelanggan->name, 0, 1)) }}
                                            </div>
                                            <span class="fw-medium text-dark">{{ $pelanggan->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-muted">{{ $pelanggan->email }}</td>
                                    <td class="text-muted">{{ $pelanggan->phone ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ strtolower($pelanggan->status) === 'aktif' ? 'bg-light-success text-success' : 'bg-light-danger text-danger' }} rounded-pill px-3">
                                            {{ ucfirst($pelanggan->status) }}
                                        </span>
                                    </td>
                                    <td class="pe-3 text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-icon btn-light-info rounded-circle shadow-none view-detail" data-bs-toggle="tooltip" title="Lihat Profil">
                                                <i class="ti ti-eye"></i>
                                            </button>
                                            <a href="{{ route('pelanggan.edit', $pelanggan->id) }}" class="btn btn-icon btn-light-warning rounded-circle shadow-none" data-bs-toggle="tooltip" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <form action="{{ route('pelanggan.destroy', $pelanggan->id) }}" method="POST" class="delete-form" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-icon btn-light-danger rounded-circle shadow-none btn-delete" data-bs-toggle="tooltip" title="Hapus">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada pelanggan ditemukan</td>
                                </tr>
                            @endforelse
                        </tbody>
                        </table>
                            </div>
                        </div>

                        <!-- TAB TRASH -->
                        <div class="tab-pane fade" id="trash" role="tabpanel" aria-labelledby="trash-tab">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 text-center">
                                    <thead class="table-light text-muted">
                                        <tr>
                                            <th class="ps-3 border-0 rounded-start">Nama</th>
                                            <th class="border-0">Email</th>
                                            <th class="border-0">Tanggal Dihapus</th>
                                            <th class="border-0">Sisa Waktu</th>
                                            <th class="pe-3 border-0 rounded-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($trashedPelanggans as $item)
                                        <tr>
                                            <td class="ps-3">{{ $item->name }}</td>
                                            <td class="text-muted">{{ $item->email }}</td>
                                            <td>{{ $item->deleted_at->format('d/m/Y H:i') }}</td>
                                            <td><span class="badge bg-light-warning text-warning">{{ round(30 - $item->deleted_at->diffInDays(now())) }} hari lagi</span></td>
                                            <td class="pe-3">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button class="btn btn-sm btn-success rounded-pill px-3 btn-restore" data-id="{{ $item->id }}" data-type="pelanggan">
                                                        <i class="ti ti-reload me-1"></i> Pulihkan
                                                    </button>
                                                    <button class="btn btn-sm btn-danger rounded-pill px-3 btn-force-delete" data-id="{{ $item->id }}" data-type="pelanggan">
                                                        <i class="ti ti-trash-x me-1"></i> Hapus Permanen
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">Keranjang sampah kosong.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Popup detail pelanggan -->
    <div id="detailPopup" class="popup-overlay" style="display:none;">
        <div class="popup-box">
            <span class="popup-close">&times;</span>
            <h4 id="popupName"></h4>
            <p><b>Username:</b> <span id="popupUsername"></span></p>
            <p><b>Email:</b> <span id="popupEmail"></span></p>
            <p><b>Telepon:</b> <span id="popupPhone"></span></p>
            <p><b>Status:</b> <span id="popupStatus"></span></p>
        </div>
    </div>


@push('scripts')
<script>
    $(document).ready(function () {
        // Theme Config (Safe Check)
        if (typeof layout_change === 'function') {
            layout_change('light');
            font_change('Roboto');
            change_box_container('false');
            layout_caption_change('true');
            layout_rtl_change('false');
            preset_change('preset-1');
        }

        $('#searchInput').on('keyup', function () {
            let query = $(this).val();

            $.ajax({
                url: "{{ route('pelanggan.index') }}", 
                type: 'GET',
                data: { search: query },
                success: function (data) {
                    let tbody = $(data).find('tbody').html();
                    $('tbody').html(tbody);
                }
            });
        });

        // SweetAlert Delete Confirmation
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data pelanggan ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-danger mx-2',
                    cancelButton: 'btn btn-secondary mx-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Tooltip init
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        $(document).on('click', '.view-detail', function () {
            let row = $(this).closest('tr');
            $('#popupName').text(row.data('name')); 
            $('#popupUsername').text(row.data('username'));
            $('#popupEmail').text(row.data('email')); 
            $('#popupPhone').text(row.data('phone') || '-');
            $('#popupStatus').text(row.data('status')); 
            $('#detailPopup').fadeIn();
        });

        $('.popup-close').click(function () {
            $('#detailPopup').fadeOut();
        });

        $('#detailPopup').click(function (e) {
            if (e.target.id === 'detailPopup') {
                $(this).fadeOut();
            }
        });
        // ACTION: RESTORE
        $(document).on('click', '.btn-restore', function() {
            let id = $(this).data('id');
            let type = $(this).data('type');
            let tr = $(this).closest('tr');
            
            Swal.fire({
                title: 'Pulihkan Pelanggan?',
                text: "Data ini akan dikembalikan ke daftar aktif.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Pulihkan!',
                cancelButtonText: 'Batal',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-success mx-2',
                    cancelButton: 'btn btn-secondary mx-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/sampah/restore/${type}/${id}`,
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            if(res.success) {
                                Swal.fire('Dipulihkan!', res.message, 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Gagal!', res.message, 'error');
                            }
                        },
                        error: function(err) {
                            Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
                        }
                    });
                }
            });
        });

        // ACTION: FORCE DELETE
        $(document).on('click', '.btn-force-delete', function() {
            let id = $(this).data('id');
            let type = $(this).data('type');
            let tr = $(this).closest('tr');
            
            Swal.fire({
                title: 'Hapus Permanen?',
                text: "Peringatan: Data ini akan terhapus selamanya!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus Permanen!',
                cancelButtonText: 'Batal',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-danger mx-2',
                    cancelButton: 'btn btn-secondary mx-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/sampah/force-delete/${type}/${id}`,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            if(res.success) {
                                Swal.fire('Terhapus!', res.message, 'success');
                                tr.fadeOut(400, function(){ $(this).remove(); });
                            } else {
                                Swal.fire('Gagal!', res.message, 'error');
                            }
                        },
                        error: function(err) {
                            Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
@endsection