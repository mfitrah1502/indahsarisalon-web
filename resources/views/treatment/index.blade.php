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
</style>

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Daftar Treatment</h4>
                    <a href="{{ route('treatment.create') }}" class="btn btn-primary">Tambah Treatment</a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success m-3">{{ session('success') }}</div>
                @endif

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <select id="filterCategory" class="form-control">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->name }}" {{ request('category') == $category->name ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select id="sortBy" class="form-control">
                                <option value="">Sort By</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama A-Z
                                </option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama Z-A
                                </option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Harga
                                    Terendah → Tertinggi</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga
                                    Tertinggi → Terendah</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" id="searchInput" class="form-control" placeholder="Cari treatment..."
                                value="{{ request('search') }}">
                        </div>
                    </div>

                    
                    <!-- Tambahkan tombol di atas table -->
<div class="mb-3">
    <button id="btnViewCategories" class="btn btn-secondary">Lihat Kategori</button>
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

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Promo</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($treatments as $index => $treatment)
                                <tr class="treatment-row" data-name="{{ $treatment->name }}"
                                    data-category="{{ $treatment->category ? $treatment->category->name : 'Empty'}}"
                                    data-promo="{{ $treatment->is_promo ? $treatment->promo_type . ' ' . $treatment->promo_value : 'Tidak ada' }}"
                                    data-details='@json($treatment->details)'
                                    data-image="{{ $treatment->image ?? ''}}">

                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $treatment->name }}</td>
                                    <td>{{ $treatment->category ? $treatment->category->name : 'Empty' }}</td>

                                    <td>
                                        Rp {{ number_format($treatment->details->min('price') ?? 0, 0, ',', '.') }}
                                    </td>

                                    <td>
                                        {{ $treatment->is_promo ? $treatment->promo_type . ' ' . $treatment->promo_value : '-' }}
                                    </td>

                                    <td>
                                        <button class="btn btn-sm btn-info view-detail">Lihat</button>

                                        <a href="{{ route('treatment.edit', $treatment->id) }}"
                                            class="btn btn-sm btn-warning">Edit</a>

                                        <form action="{{ route('treatment.destroy', $treatment->id) }}" method="POST"
                                            style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-sm btn-danger"
                                                onclick="return confirm('Hapus treatment ini?')">
                                                Hapus
                                            </button>
                                        </form>
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
                    {{ $treatments->links() }}

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
                }
            });
        }

        $('#filterCategory, #sortBy').change(applyFilterSortSearch);
        $('#searchInput').on('keyup', function (e) {
            if (e.keyCode === 13) applyFilterSortSearch();
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
    if(!confirm('Yakin ingin menghapus kategori ini?')) return;

    let row = $(this).closest('tr');
    let id = row.data('id');

    $.ajax({
        url: '/categories/' + id,
        type: 'POST', // 🔥 ubah
        data: {
            _token: '{{ csrf_token() }}',
            _method: 'DELETE'
        },
        success: function(res){
            row.remove();
        },
        error: function(err){
            console.log(err.responseText);
            alert('Gagal menghapus kategori');
        }
    });
});
    </script>
@endpush
@endsection