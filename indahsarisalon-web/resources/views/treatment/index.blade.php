@extends('layout.dashboard')

@section('title', 'Manajemen Treatment')
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

    /* Fix pagination icons */
    .pagination svg {
        width: 1rem;
        height: 1rem;
    }

    .pagination .page-link {
        padding: 0.5rem 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .pagination {
        margin-top: 20px;
        justify-content: center;
    }

    /* Fix SweetAlert z-index behind custom popups */
    .swal2-container {
        z-index: 10000 !important;
    }
</style>

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold text-dark">Daftar Treatment</h4>
                    <a href="{{ route('treatment.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="ti ti-plus me-1"></i> Tambah Treatment
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success m-3">{{ session('success') }}</div>
                @endif

                <div class="card-body">
                    <ul class="nav nav-tabs mb-4" id="treatmentTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="aktif-tab" data-bs-toggle="tab" data-bs-target="#aktif" type="button" role="tab" aria-controls="aktif" aria-selected="true">
                                <i class="ti ti-massage me-2"></i>Daftar Aktif
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="trash-tab" data-bs-toggle="tab" data-bs-target="#trash" type="button" role="tab" aria-controls="trash" aria-selected="false">
                                <i class="ti ti-trash me-2"></i>Keranjang Sampah 
                                @if($trashedTreatments->count() > 0)
                                    <span class="badge bg-danger ms-2">{{ $trashedTreatments->count() }}</span>
                                @endif
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="treatmentTabContent">
                        <!-- TAB AKTIF -->
                        <div class="tab-pane fade show active" id="aktif" role="tabpanel" aria-labelledby="aktif-tab">
                            <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="input-group shadow-none">
                                <span class="input-group-text bg-light border-0"><i class="ti ti-category text-muted"></i></span>
                                <select id="filterCategory" class="form-select bg-light border-0">
                                    <option value="">Semua Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->name }}" {{ request('category') == $category->name ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group shadow-none">
                                <span class="input-group-text bg-light border-0"><i class="ti ti-sort-descending text-muted"></i></span>
                                <select id="sortBy" class="form-select bg-light border-0">
                                    <option value="">Urutkan Berdasarkan</option>
                                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
                                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama (Z-A)</option>
                                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Harga Terendah → Tertinggi</option>
                                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga Tertinggi → Terendah</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group shadow-none">
                                <span class="input-group-text bg-light border-0"><i class="ti ti-search text-muted"></i></span>
                                <input type="text" id="searchInput" class="form-control bg-light border-0" placeholder="Cari treatment..." value="{{ request('search') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Tambahkan tombol di atas table -->
                    <div class="mb-4">
                        <button id="btnViewCategories" class="btn btn-outline-secondary rounded-pill px-3 shadow-none">
                            <i class="ti ti-tags me-1"></i> Lihat Kategori
                        </button>
                    </div>

<!-- Popup Daftar Kategori -->
<div id="categoryPopup" class="popup-overlay" style="display:none;">
    <div class="popup-box">
        <span class="popup-close">&times;</span>
        <h4>Daftar Kategori</h4>

        <!-- Form Tambah Kategori -->
        <form id="formAddCategory" class="mb-3">
            @csrf
            <div class="input-group">
                <input type="text" id="newCategoryName" class="form-control" placeholder="Tambah kategori baru">
                <button type="submit" class="btn btn-primary">Tambah</button>
            </div>
        </form>

        <!-- Table Kategori -->
        <table class="table table-bordered" id="categoryTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $index => $category)
                <tr data-id="{{ $category->id }}">
                    <td>{{ $index + 1 }}</td>
                    <td contenteditable="true" class="editable-category">{{ $category->name }}</td>
                    <td>
                        <button class="btn btn-sm btn-danger btn-delete-category">Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th class="ps-3 border-0 rounded-start">No</th>
                                    <th class="border-0">Nama</th>
                                    <th class="border-0">Kategori</th>
                                    <th class="border-0">Harga</th>
                                    <th class="border-0 text-center">Promo</th>
                                    <th class="pe-3 border-0 rounded-end text-center" width="150">Aksi</th>
                                </tr>
                            </thead>
                        <tbody>
                            @forelse($treatments as $index => $treatment)
                                <tr class="treatment-row" data-name="{{ $treatment->name }}"
                                    data-category="{{ $treatment->category ? $treatment->category->name : 'Empty'}}"
                                    data-promo="{{ $treatment->is_promo ? $treatment->promo_type . ' ' . $treatment->promo_value : 'Tidak ada' }}"
                                    data-details='@json($treatment->details)'
                                    data-image="{{ $treatment->image ?? ''}}">

                                    <td class="ps-3">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light-primary text-primary rounded me-2 d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                                <i class="ti ti-massage"></i>
                                            </div>
                                            <span class="fw-medium text-dark">{{ $treatment->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-muted">{{ $treatment->category ? $treatment->category->name : 'Empty' }}</td>

                                    <td class="text-start">
                                        <span class="fw-medium text-dark">Rp {{ number_format($treatment->details->min('price') ?? 0, 0, ',', '.') }}</span>
                                    </td>

                                    <td class="text-center">
                                        @if($treatment->is_promo)
                                            <span class="badge bg-light-warning text-warning rounded-pill px-3">{{ $treatment->promo_type . ' ' . $treatment->promo_value }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <td class="pe-3 text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-icon btn-light-info rounded-circle shadow-none view-detail" data-bs-toggle="tooltip" title="Lihat">
                                                <i class="ti ti-eye"></i>
                                            </button>

                                            <a href="{{ route('treatment.edit', $treatment->id) }}" class="btn btn-icon btn-light-warning rounded-circle shadow-none" data-bs-toggle="tooltip" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </a>

                                            <form action="{{ route('treatment.destroy', $treatment->id) }}" method="POST" style="display:inline-block;" class="delete-treatment-form">
                                                @csrf
                                                @method('DELETE')

                                                <button type="button" class="btn btn-icon btn-light-danger rounded-circle shadow-none btn-delete-treatment" data-bs-toggle="tooltip" title="Hapus">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="6" style="text-align:center;">
                                        Tidak ada data yang ditemukan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        </table>
                    </div>
                            <div class="mt-4">
                                {{ $treatments->links() }}
                            </div>
                        </div>

                        <!-- TAB TRASH -->
                        <div class="tab-pane fade" id="trash" role="tabpanel" aria-labelledby="trash-tab">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 text-center">
                                    <thead class="table-light text-muted">
                                        <tr>
                                            <th class="ps-3 border-0 rounded-start">Nama Treatment</th>
                                            <th class="border-0">Kategori</th>
                                            <th class="border-0">Tanggal Dihapus</th>
                                            <th class="border-0">Sisa Waktu</th>
                                            <th class="pe-3 border-0 rounded-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($trashedTreatments as $item)
                                        <tr>
                                            <td class="ps-3">
                                                <div class="d-flex align-items-center justify-content-center">
                                                    @php
                                                        $baseUrl = "https://" . env('SUPABASE_PROJECT_REF') . ".supabase.co/storage/v1/object/public/" . env('SUPABASE_BUCKET') . "/";
                                                    @endphp
                                                    <img src="{{ $item->image ? $baseUrl . $item->image : asset('assets/images/no-image.jpg') }}" alt="" class="rounded-circle me-2" width="40" height="40" style="object-fit:cover;">
                                                    <span>{{ $item->name }}</span>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-light-info text-info">{{ $item->category->name ?? '-' }}</span></td>
                                            <td>{{ $item->deleted_at->format('d/m/Y H:i') }}</td>
                                            <td><span class="badge bg-light-warning text-warning">{{ round(30 - $item->deleted_at->diffInDays(now())) }} hari lagi</span></td>
                                            <td class="pe-3">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button class="btn btn-sm btn-success rounded-pill px-3 btn-restore" data-id="{{ $item->id }}" data-type="treatment">
                                                        <i class="ti ti-reload me-1"></i> Pulihkan
                                                    </button>
                                                    <button class="btn btn-sm btn-danger rounded-pill px-3 btn-force-delete" data-id="{{ $item->id }}" data-type="treatment">
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
                    <div id="detailPopup" class="popup-overlay" style="display:none;">
                        <div class="popup-box">
                            <span class="popup-close">&times;</span>

                            <h4 id="popupName"></h4>
                             <div class="mb-3 text-center">
    <img id="popupImage"
         src=""
         class="img-fluid mb-2"
         style="max-width: 200px; height:auto;">
</div>
                            <p><b>Kategori:</b> <span id="popupCategory"></span></p>
                            <p><b>Promo:</b> <span id="popupPromo"></span></p>

                            <hr>

                            <h5>Detail Treatment</h5>
                            <ul id="popupDetails"></ul>
                        </div>
                    </div>

                    <!-- Modal Detail Treatment -->
                    <div class="modal fade" id="treatmentDetailModal" tabindex="-1" aria-labelledby="treatmentDetailLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="treatmentDetailLabel">Detail Treatment</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Nama:</strong> <span id="modalName"></span></p>
                                    <p><strong>Kategori:</strong> <span id="modalCategory"></span></p>
                                    <p><strong>Promo:</strong> <span id="modalPromo"></span></p>
                                    <hr>
                                    <h6>Detail</h6>
                                    <ul id="modalDetails" class="list-group"></ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


@push('scripts')
    <script>
        // Initial Config (Safe Check)
        if (typeof layout_change === 'function') {
            layout_change('light');
            font_change('Roboto');
            change_box_container('false');
            layout_caption_change('true');
            layout_rtl_change('false');
            preset_change('preset-1');
        }

        // AJAX filter/sort/search
        function applyFilterSortSearch() {
            let category = $('#filterCategory').val();
            let sort = $('#sortBy').val();
            let search = $('#searchInput').val();

            $.ajax({
                url: "{{ route('treatment.filter') }}",
                type: "GET",
                data: { category: category, sort: sort, search: search },
                success: function (response) {
                    $('table tbody').html(response);
                    
                    // Re-initialize tooltips for new elements
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                }
            });
        }

        $('#filterCategory, #sortBy').change(applyFilterSortSearch);
        $('#searchInput').on('keyup', function (e) {
            if (e.keyCode === 13) applyFilterSortSearch();
        });

        // Tooltip init
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Delete Treatment SweetAlert
        $(document).on('click', '.btn-delete-treatment', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Treatment ini akan dihapus secara permanen!",
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

        // Modal detail treatment (delegated event)
        $(document).on('click', '.view-detail', function () {

            let row = $(this).closest('tr');

            let details = row.data('details');
            

            $('#popupName').text(row.data('name'));
            $('#popupCategory').text(row.data('category'));
            $('#popupPromo').text(row.data('promo'));
            let image = row.data('image');

let baseUrl = "https://{{ env('SUPABASE_PROJECT_REF') }}.supabase.co/storage/v1/object/public/{{ env('SUPABASE_BUCKET') }}/";

if (image) {
    $('#popupImage').attr('src', baseUrl + image);
} else {
    $('#popupImage').attr('src', "{{ asset('assets/images/no-image.jpg') }}");
}

            let html = '';

            details.forEach(function (d) {
                html += `
                                <li>
                                    <b>${d.name}</b><br>
                                    Durasi : ${d.duration} menit<br>
                                    Harga : Rp${d.price}<br>
                                    ${d.description ?? ''}
                                </li>`;
            });

            $('#popupDetails').html(html);

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

        // Tampilkan popup kategori
    $('#btnViewCategories').click(function () {
        $('#categoryPopup').fadeIn();
    });

    $('.popup-close').click(function () {
        $(this).closest('.popup-overlay').fadeOut();
    });

    $('#categoryPopup').click(function (e) {
        if (e.target.id === 'categoryPopup') {
            $(this).fadeOut();
        }
    });

    // Tambah kategori baru
    $('#formAddCategory').submit(function (e) {
        e.preventDefault();
        let name = $('#newCategoryName').val().trim();
        if(!name) return alert('Nama kategori tidak boleh kosong.');

        $.ajax({
            url: "{{ route('categories.store') }}",
            type: "POST",
            data: { name: name, _token: '{{ csrf_token() }}' },
            success: function(res){
                // Tambah ke table
                let category = res.data ?? res;

    let count = $('#categoryTable tbody tr').length + 1;

    $('#categoryTable tbody').append(`
        <tr data-id="${category.id}">
            <td>${count}</td>
            <td contenteditable="true" class="editable-category">${category.name}</td>
            <td><button class="btn btn-sm btn-danger btn-delete-category">Hapus</button></td>
        </tr>
    `);

    $('#newCategoryName').val('');
            },
            error: function(err){
                alert('Gagal menambah kategori.');
            }
        });
    });

    // Edit kategori inline
    $(document).on('blur', '.editable-category', function () {
    let row = $(this).closest('tr');
    let id = row.data('id');
    let name = $(this).text().trim();

    if(!name) return alert('Nama kategori tidak boleh kosong.');

    $.ajax({
        url: '/categories/' + id,
        type: 'POST', // 🔥 ubah
        data: {
            name: name,
            _token: '{{ csrf_token() }}',
            _method: 'PUT' // 🔥 ini kunci
        },
        success: function(res){
            console.log('Kategori diperbarui');
        },
        error: function(err){
            console.log(err.responseText);
            alert('Gagal memperbarui kategori');
        }
    });
});

    // Hapus kategori
    $(document).on('click', '.btn-delete-category', function () {
    let row = $(this).closest('tr');
    let id = row.data('id');

    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Kategori ini akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/categories/' + id,
                type: 'POST', // 🔥 ubah
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                success: function(res){
                    row.remove();
                        Swal.fire('Terhapus!', 'Kategori berhasil dihapus.', 'success');
                    },
                    error: function(err){
                        console.log(err.responseText);
                        Swal.fire('Gagal!', 'Gagal menghapus kategori', 'error');
                    }
                });
            }
        });
    });

    // ACTION: RESTORE
    $(document).on('click', '.btn-restore', function() {
        let id = $(this).data('id');
        let type = $(this).data('type');
        let tr = $(this).closest('tr');
        
        Swal.fire({
            title: 'Pulihkan Treatment?',
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
</script>
@endpush
@endsection