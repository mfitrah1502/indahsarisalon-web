@extends('layout.dashboard')

@section('title', 'Manajemen Karyawan')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold text-dark">Daftar Karyawan</h4>
                    <a href="{{ route('karyawan.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="ti ti-plus me-1"></i> Tambah Karyawan
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success m-3">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="card-body">
                    <ul class="nav nav-tabs mb-4" id="karyawanTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="aktif-tab" data-bs-toggle="tab" data-bs-target="#aktif" type="button" role="tab" aria-controls="aktif" aria-selected="true">
                                <i class="ti ti-users me-2"></i>Daftar Aktif
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="trash-tab" data-bs-toggle="tab" data-bs-target="#trash" type="button" role="tab" aria-controls="trash" aria-selected="false">
                                <i class="ti ti-trash me-2"></i>Keranjang Sampah 
                                @if($trashedKaryawans->count() > 0)
                                    <span class="badge bg-danger ms-2">{{ $trashedKaryawans->count() }}</span>
                                @endif
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="karyawanTabContent">
                        <!-- TAB AKTIF -->
                        <div class="tab-pane fade show active" id="aktif" role="tabpanel" aria-labelledby="aktif-tab">
                            <div class="d-flex mb-4">
                                <div class="input-group" style="max-width: 400px;">
                                    <span class="input-group-text bg-light border-0"><i class="ti ti-search text-muted"></i></span>
                                    <input type="text" id="searchInput" class="form-control bg-light border-0" placeholder="Cari karyawan..." value="{{ request('search') }}">
                                </div>
                            </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th class="ps-3 border-0 rounded-start">No</th>
                                    <th class="border-0">Nama</th>
                                    <th class="border-0">Username</th>
                                    <th class="border-0">Email</th>
                                    <th class="border-0">Telepon</th>
                                    <th class="border-0 text-center">Role</th>
                                    <th class="border-0 text-center">Status</th>
                                    <th class="pe-3 border-0 rounded-end text-center" width="150">Aksi</th>
                                </tr>
                            </thead>
                        <tbody>
                            @foreach($karyawans as $karyawan)
                                {{-- hanya tampilkan admin --}}
                                <tr>
                                    <td class="ps-3">{{ $loop->iteration }}</td>
                                    <td>
                                        <a href="#" class="lihat-absensi text-dark fw-medium text-decoration-none" data-id="{{ $karyawan->id }}">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-light-primary text-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                    {{ strtoupper(substr($karyawan->name, 0, 1)) }}
                                                </div>
                                                {{ $karyawan->name }}
                                            </div>
                                        </a>
                                    </td>
                                    <td class="text-muted">{{ $karyawan->username }}</td>
                                    <td class="text-muted">{{ $karyawan->email }}</td>
                                    <td class="text-muted">{{ $karyawan->phone ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-light-info text-info rounded-pill px-3">{{ ucfirst($karyawan->role) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $karyawan->status === 'aktif' ? 'bg-light-success text-success' : 'bg-light-danger text-danger' }} rounded-pill px-3">
                                            {{ ucfirst($karyawan->status) }}
                                        </span>
                                    </td>
                                    <td class="pe-3 text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('karyawan.edit', $karyawan->id) }}" class="btn btn-icon btn-light-warning rounded-circle shadow-none" data-bs-toggle="tooltip" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <form action="{{ route('karyawan.destroy', $karyawan->id) }}" method="POST" class="delete-form" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-icon btn-light-danger rounded-circle shadow-none btn-delete" data-bs-toggle="tooltip" title="Hapus">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                            @endforeach
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
                                            <th class="border-0">Role</th>
                                            <th class="border-0">Tanggal Dihapus</th>
                                            <th class="border-0">Sisa Waktu</th>
                                            <th class="pe-3 border-0 rounded-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($trashedKaryawans as $item)
                                        <tr>
                                            <td class="ps-3">{{ $item->name }}</td>
                                            <td><span class="badge bg-light-primary text-primary">{{ ucfirst($item->role) }}</span></td>
                                            <td>{{ $item->deleted_at->format('d/m/Y H:i') }}</td>
                                            <td><span class="badge bg-light-warning text-warning">{{ round(30 - $item->deleted_at->diffInDays(now())) }} hari lagi</span></td>
                                            <td class="pe-3">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button class="btn btn-sm btn-success rounded-pill px-3 btn-restore" data-id="{{ $item->id }}" data-type="karyawan">
                                                        <i class="ti ti-reload me-1"></i> Pulihkan
                                                    </button>
                                                    <button class="btn btn-sm btn-danger rounded-pill px-3 btn-force-delete" data-id="{{ $item->id }}" data-type="karyawan">
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

    <!-- Modal Absensi -->
    <div class="modal fade" id="absensiModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Riwayat Presensi: <span id="employeeName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Filter Section -->
                    <div class="row g-2 mb-4 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Jenis Filter</label>
                            <select id="filterType" class="form-select">
                                <option value="all">Semua Data</option>
                                <option value="harian">Harian</option>
                                <option value="bulanan">Bulanan</option>
                                <option value="tahunan">Tahunan</option>
                            </select>
                        </div>
                        <div class="col-md-5" id="filterInputContainer" style="display:none;">
                            <label class="form-label small fw-bold" id="filterInputLabel">Pilih Tanggal</label>
                            <input type="date" id="filterValue" class="form-control">
                        </div>
                        <div class="col-md-3" id="filterActionContainer" style="display:none;">
                            <button id="btnResetFilter" class="btn btn-light-secondary w-100">Reset</button>
                        </div>
                    </div>

                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light sticky-top">
                            <tr>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Jam Keluar</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="absensiTable"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


@push('scripts')
    <script>
        $(document).ready(function () {
            $('#searchInput').on('keyup', function () {
                let query = $(this).val();

                $.ajax({
                    url: "{{ route('karyawan.index') }}", // route yang sama
                    type: 'GET',
                    data: { search: query },
                    success: function (data) {
                        let tbody = $(data).find('tbody').html();
                        $('tbody').html(tbody);
                    }
                });
            });
        });
        // Data presensi global untuk filter
        let currentAbsensiData = [];

        // SweetAlert Delete Confirmation
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data karyawan ini akan dihapus secara permanen!",
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

        function renderAbsensi(data) {
            let rows = '';
            if (data.length === 0) {
                rows = `<tr><td colspan="4" class="text-center text-muted py-4">Data tidak ditemukan</td></tr>`;
            } else {
                data.forEach(function (item) {
                    let badgeClass = 'bg-light-success text-success';
                    if (item.status === 'Terlambat') badgeClass = 'bg-light-warning text-warning';
                    if (item.status === 'Tidak Hadir' || item.status === 'Alpha') badgeClass = 'bg-light-danger text-danger';
                    
                    rows += `<tr>
                        <td>${item.tanggal}</td>
                        <td class="text-center">${item.jam_masuk ? item.jam_masuk.split(' ')[1] || item.jam_masuk : '-'}</td>
                        <td class="text-center">${item.jam_keluar ? item.jam_keluar.split(' ')[1] || item.jam_keluar : '-'}</td>
                        <td class="text-center"><span class="badge ${badgeClass}">${item.status ?? 'Hadir'}</span></td>
                    </tr>`;
                });
            }
            $('#absensiTable').html(rows);
        }

        function applyFilter() {
            const type = $('#filterType').val();
            const val = $('#filterValue').val();
            
            if (type === 'all' || !val) {
                renderAbsensi(currentAbsensiData);
                return;
            }

            let filtered = currentAbsensiData.filter(item => {
                if (type === 'harian') return item.tanggal === val;
                if (type === 'bulanan') return item.tanggal.startsWith(val);
                if (type === 'tahunan') return item.tanggal.startsWith(val);
                return true;
            });

            renderAbsensi(filtered);
        }

        // Event change filter
        $('#filterType').on('change', function() {
            const type = $(this).val();
            const $container = $('#filterInputContainer');
            const $action = $('#filterActionContainer');
            const $input = $('#filterValue');
            const $label = $('#filterInputLabel');

            if (type === 'all') {
                $container.hide();
                $action.hide();
                $input.val('');
                renderAbsensi(currentAbsensiData);
            } else {
                $container.show();
                $action.show();
                if (type === 'harian') {
                    $input.attr('type', 'date');
                    $label.text('Pilih Tanggal');
                } else if (type === 'bulanan') {
                    $input.attr('type', 'month');
                    $label.text('Pilih Bulan');
                } else if (type === 'tahunan') {
                    $input.attr('type', 'number').attr('min', '2020').attr('max', '2030');
                    $input.val(new Date().getFullYear());
                    $label.text('Ketik Tahun');
                }
            }
        });

        $('#filterValue').on('change keyup', applyFilter);
        $('#btnResetFilter').on('click', function() {
            $('#filterType').val('all').trigger('change');
        });

        // Popup presensi
        $(document).on('click', '.lihat-absensi', function (e) {
            e.preventDefault();
            let userId = $(this).data('id');
            let name = $(this).text().trim();
            $('#employeeName').text(name);

            $.ajax({
                url: "/karyawan/" + userId + "/absensi",
                type: "GET",
                success: function (data) {
                    currentAbsensiData = data;
                    $('#filterType').val('all').trigger('change');
                    renderAbsensi(data);
                    var modal = new bootstrap.Modal(document.getElementById('absensiModal'));
                    modal.show();
                },
                error: function (err) {
                    console.log('AJAX Error:', err);
                }
            });
        });

        // ACTION: RESTORE (dari template sampah)
        $(document).on('click', '.btn-restore', function() {
            let id = $(this).data('id');
            let type = $(this).data('type');
            let tr = $(this).closest('tr');
            
            Swal.fire({
                title: 'Pulihkan Data?',
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
                                    location.reload(); // Reload untuk update kedua tabel 
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