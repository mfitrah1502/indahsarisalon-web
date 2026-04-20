@extends('layout.dashboard')

@section('title', 'Manajemen Karyawan')

@push('styles')
<style>
    .employee-card-table {
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    .employee-card-table tr {
        background: #fff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border-radius: 12px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .employee-card-table tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .employee-card-table td {
        padding: 1.2rem 1rem !important;
        vertical-align: middle;
        border: none !important;
    }

    .employee-card-table td:first-child { border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
    .employee-card-table td:last-child { border-top-right-radius: 12px; border-bottom-right-radius: 12px; }

    .action-btn {
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        transition: all 0.2s;
    }

    .action-btn:hover {
        background: #fdf2f8 !important;
        transform: scale(1.1);
    }
</style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Manajemen Karyawan</h3>
                    <p class="text-muted mb-0">Kelola data staf salon and monitoring presensi.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('karyawan.create') }}" class="btn btn-primary rounded-pill px-4 shadow">
                        <i class="ti ti-plus me-1"></i> Tambah Karyawan
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="ti ti-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4 overlay-hidden">
                <div class="card-body p-4">
                    <!-- Modern Filter Bar -->
                    <div class="row g-3 mb-4 align-items-center bg-light p-3 rounded-4">
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted mb-2">Cari Nama / Email / Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="ti ti-search text-muted"></i></span>
                                <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Ketik kata kunci..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="small fw-bold text-muted mb-2">Filter Role</label>
                            <select id="filterRole" class="form-select border-0 shadow-none">
                                <option value="">Semua Role</option>
                                <option value="admin">Admin</option>
                                <option value="karyawan">Karyawan</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="small fw-bold text-muted mb-2">Filter Status</label>
                            <select id="filterStatus" class="form-select border-0 shadow-none">
                                <option value="">Semua Status</option>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table employee-card-table">
                            <thead>
                                <tr class="bg-transparent shadow-none">
                                    <th class="text-muted small fw-bold px-3 py-2">KARYAWAN</th>
                                    <th class="text-muted small fw-bold py-2">KONTAK</th>
                                    <th class="text-muted small fw-bold py-2">ROLE</th>
                                    <th class="text-muted small fw-bold py-2">STATUS</th>
                                    <th class="text-muted small fw-bold py-2 text-end px-3">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @include('karyawan.table')
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Absensi -->
    <div class="modal fade" id="absensiModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold"><i class="ti ti-calendar-event me-2 text-primary"></i>Riwayat Presensi: <span id="employeeName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Filter Section -->
                    <div class="row g-2 mb-4 align-items-end bg-light p-3 rounded-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Jenis Filter</label>
                            <select id="filterType" class="form-select border-0 shadow-sm">
                                <option value="all">Semua Data</option>
                                <option value="harian">Harian</option>
                                <option value="bulanan">Bulanan</option>
                                <option value="tahunan">Tahunan</option>
                            </select>
                        </div>
                        <div class="col-md-5" id="filterInputContainer" style="display:none;">
                            <label class="form-label small fw-bold text-muted" id="filterInputLabel">Pilih Tanggal</label>
                            <input type="date" id="filterValue" class="form-control border-0 shadow-sm">
                        </div>
                        <div class="col-md-3" id="filterActionContainer" style="display:none;">
                            <button id="btnResetFilter" class="btn btn-secondary w-100 rounded-pill">Reset</button>
                        </div>
                    </div>

                    <div class="table-responsive rounded-3 border" style="max-height: 400px;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th class="small fw-bold py-3">TANGGAL</th>
                                    <th class="small fw-bold text-center py-3">JAM MASUK</th>
                                    <th class="small fw-bold text-center py-3">JAM KELUAR</th>
                                    <th class="small fw-bold text-center py-3">STATUS</th>
                                </tr>
                            </thead>
                            <tbody id="absensiTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
    <script>
        // AJAX filter/search
        function applyFilters() {
            let search = $('#searchInput').val();
            let role = $('#filterRole').val();
            let status = $('#filterStatus').val();

            $.ajax({
                url: "{{ route('karyawan.filter') }}",
                type: "GET",
                data: { search: search, role: role, status: status },
                success: function (response) {
                    $('.employee-card-table tbody').html(response);
                }
            });
        }

        $('#filterRole, #filterStatus').change(applyFilters);
        $('#searchInput').on('keyup', function (e) {
            if (e.keyCode === 13) applyFilters();
        });

        // Data presensi global untuk filter
        let currentAbsensiData = [];

        function renderAbsensi(data) {
            let rows = '';
            if (data.length === 0) {
                rows = `<tr><td colspan="4" class="text-center text-muted py-5">
                            <i class="ti ti-info-circle fs-2 d-block mb-2"></i>
                            Data tidak ditemukan
                        </td></tr>`;
            } else {
                data.forEach(function (item) {
                    let badgeClass = 'bg-light-success text-success';
                    if (item.status === 'Terlambat' || item.status === 'Tidak Absensi Pulang') badgeClass = 'bg-light-warning text-warning';
                    if (item.status === 'Tidak Hadir' || item.status === 'Alpha') badgeClass = 'bg-light-danger text-danger';
                    
                    rows += `<tr>
                        <td class="fw-medium">${item.tanggal}</td>
                        <td class="text-center">${item.jam_masuk ? (item.jam_masuk.includes(' ') ? item.jam_masuk.split(' ')[1] : item.jam_masuk) : '-'}</td>
                        <td class="text-center">${item.jam_keluar ? (item.jam_keluar.includes(' ') ? item.jam_keluar.split(' ')[1] : item.jam_keluar) : '-'}</td>
                        <td class="text-center"><span class="badge ${badgeClass} rounded-pill px-3">${item.status ?? 'Hadir'}</span></td>
                    </tr>`;
                });
            }
            $('#absensiTable').html(rows);
        }

        function applyAbsensiFilter() {
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

        $('#filterValue').on('change keyup', applyAbsensiFilter);
        $('#btnResetFilter').on('click', function() {
            $('#filterType').val('all').trigger('change');
        });

        // Popup presensi
        $(document).on('click', '.lihat-absensi', function (e) {
            e.preventDefault();
            let userId = $(this).data('id');
            let name = $(this).closest('tr').find('h6').text().trim();
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
                    console.error('AJAX Error:', err);
                }
            });
        });
    </script>
@endpush
@endsection