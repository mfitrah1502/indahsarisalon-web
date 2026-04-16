@extends('layout.dashboard')

@section('title', 'Manajemen Pelanggan')

@push('styles')
<style>
    .customer-card-table {
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    .customer-card-table tr {
        background: #fff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border-radius: 12px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .customer-card-table tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .customer-card-table td {
        padding: 1.2rem 1rem !important;
        vertical-align: middle;
        border: none !important;
    }

    .customer-card-table td:first-child { border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
    .customer-card-table td:last-child { border-top-right-radius: 12px; border-bottom-right-radius: 12px; }

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
                    <h3 class="fw-bold text-dark mb-1">Manajemen Pelanggan</h3>
                    <p class="text-muted mb-0">Kelola database profil pelanggan salon Indah Sari.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('pelanggan.create') }}" class="btn btn-primary rounded-pill px-4 shadow">
                        <i class="ti ti-plus me-1"></i> Tambah Pelanggan
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
                        <div class="col-md-8">
                            <label class="small fw-bold text-muted mb-2">Cari Nama / Email / Username / Telepon</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="ti ti-search text-muted"></i></span>
                                <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Ketik kata kunci..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted mb-2">Filter Status</label>
                            <select id="filterStatus" class="form-select border-0 shadow-none">
                                <option value="">Semua Status</option>
                                <option value="aktif">Aktif</option>
                                <option value="tidak">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table customer-card-table">
                            <thead>
                                <tr class="bg-transparent shadow-none">
                                    <th class="text-muted small fw-bold px-3 py-2">PELANGGAN</th>
                                    <th class="text-muted small fw-bold py-2">KONTAK</th>
                                    <th class="text-muted small fw-bold py-2">STATUS</th>
                                    <th class="text-muted small fw-bold py-2 text-end px-3">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @include('pelanggan.table')
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Pelanggan -->
    <div class="modal fade" id="customerDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold"><i class="ti ti-user me-2 text-primary"></i>Detail Profil Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="bg-light-primary rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px;">
                            <i class="ti ti-user text-primary fs-1"></i>
                        </div>
                        <h4 id="popupName" class="mt-3 fw-bold mb-0"></h4>
                        <span id="popupUsername" class="text-muted"></span>
                    </div>
                    
                    <div class="list-group list-group-flush rounded-3 border">
                        <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                            <span class="text-muted small"><i class="ti ti-mail me-2"></i>Email</span>
                            <span id="popupEmail" class="fw-medium"></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                            <span class="text-muted small"><i class="ti ti-phone me-2"></i>Telepon</span>
                            <span id="popupPhone" class="fw-medium"></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                            <span class="text-muted small"><i class="ti ti-activity me-2"></i>Status</span>
                            <span id="popupStatus" class="badge rounded-pill px-3"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 w-100" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
    <script>
        // AJAX filter/search
        function applyFilters() {
            let search = $('#searchInput').val();
            let status = $('#filterStatus').val();

            $.ajax({
                url: "{{ route('pelanggan.filter') }}",
                type: "GET",
                data: { search: search, status: status },
                success: function (response) {
                    $('.customer-card-table tbody').html(response);
                }
            });
        }

        $('#filterStatus').change(applyFilters);
        $('#searchInput').on('keyup', function (e) {
            if (e.keyCode === 13) applyFilters();
        });

        // Detail Modal
        $(document).on('click', '.view-detail', function () {
            let btn = $(this);
            $('#popupName').text(btn.data('name')); 
            $('#popupUsername').text('@' + btn.data('username'));
            $('#popupEmail').text(btn.data('email')); 
            $('#popupPhone').text(btn.data('phone') || '-');
            
            let status = btn.data('status');
            let statusBadge = $('#popupStatus');
            statusBadge.text(status.charAt(0).toUpperCase() + status.slice(1));
            statusBadge.removeClass().addClass('badge rounded-pill px-3 ' + (status === 'aktif' ? 'bg-light-success text-success' : 'bg-light-secondary text-secondary'));

            var modal = new bootstrap.Modal(document.getElementById('customerDetailModal'));
            modal.show();
        });
    </script>
@endpush
@endsection