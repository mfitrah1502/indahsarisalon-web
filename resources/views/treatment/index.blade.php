@extends('layout.dashboard')

@section('title', 'Manajemen Treatment')
<style>
    .treatment-card-table {
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    .treatment-card-table tr {
        background: #fff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border-radius: 12px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .treatment-card-table tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .treatment-card-table td {
        padding: 1.5rem 1rem !important;
        vertical-align: middle;
        border: none !important;
    }

    .treatment-card-table td:first-child { border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
    .treatment-card-table td:last-child { border-top-right-radius: 12px; border-bottom-right-radius: 12px; }

    .category-badge {
        padding: 6px 14px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.75rem;
        background: #fdf2f8;
        color: #db2777;
    }

    .action-btn {
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        transition: all 0.2s;
    }

    .promo-tag {
        background: #fff7ed;
        color: #ea580c;
        border: 1px solid #ffedd5;
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }
</style>

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Manajemen Treatment</h3>
                    <p class="text-muted mb-0">Kelola daftar layanan salon dan pengaturan harga.</p>
                </div>
                <div class="d-flex gap-2">
                    <button id="btnViewCategories" class="btn btn-light-primary rounded-pill px-4">
                        <i class="ti ti-category me-1"></i> Kelola Kategori
                    </button>
                    <a href="{{ route('treatment.create') }}" class="btn btn-primary rounded-pill px-4 shadow">
                        <i class="ti ti-plus me-1"></i> Tambah Treatment
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
                        <div class="col-md-3">
                            <label class="small fw-bold text-muted mb-2">Cari Treatment</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="ti ti-search text-muted"></i></span>
                                <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Ketik nama layanan..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="small fw-bold text-muted mb-2">Filter Kategori</label>
                            <select id="filterCategory" class="form-select border-0 shadow-none">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->name }}" {{ request('category') == $category->name ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="small fw-bold text-muted mb-2">Urutkan</label>
                            <select id="sortBy" class="form-select border-0 shadow-none">
                                <option value="">Default (Terbaru)</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama A-Z</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama Z-A</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table treatment-card-table">
                            <thead>
                                <tr class="bg-transparent shadow-none">
                                    <th class="text-muted small fw-bold px-3 py-2">LAYANAN</th>
                                    <th class="text-muted small fw-bold py-2">KATEGORI</th>
                                    <th class="text-muted small fw-bold py-2">HARGA MULAI</th>
                                    <th class="text-muted small fw-bold py-2">PROMO</th>
                                    <th class="text-muted small fw-bold py-2 text-end px-3">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($treatments as $treatment)
                                    <tr class="treatment-row" 
                                        data-name="{{ $treatment->name }}"
                                        data-category="{{ $treatment->category->name ?? '-' }}"
                                        data-promo="{{ $treatment->is_promo ? ($treatment->promo_type == 'percent' ? $treatment->promo_value.'%' : 'Rp '.number_format($treatment->promo_value)) : '-' }}"
                                        data-details='@json($treatment->details)'
                                        data-image="{{ $treatment->image }}">
                                        <td class="px-3">
                                            <div class="d-flex align-items-center">
                                                <div class="treatment-icon me-3">
                                                    @if($treatment->image)
                                                        <img src="https://{{ env('SUPABASE_PROJECT_REF') }}.supabase.co/storage/v1/object/public/{{ env('SUPABASE_BUCKET') }}/{{ $treatment->image }}" 
                                                             class="rounded-3 shadow-sm" width="50" height="50" style="object-fit:cover;">
                                                    @else
                                                        <div class="bg-light rounded-3 d-flex align-items-center justify-content-center" width="50" height="50">
                                                            <i class="ti ti-photo text-muted fs-4"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold">{{ $treatment->name }}</h6>
                                                    <small class="text-muted">{{ $treatment->details->count() }} Variasi</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="category-badge">{{ $treatment->category->name ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark">Rp {{ number_format($treatment->details->min('price') ?? 0, 0, ',', '.') }}</span>
                                        </td>
                                        <td>
                                            @if($treatment->is_promo)
                                                <span class="promo-tag">
                                                    <i class="ti ti-discount-2 me-1"></i>
                                                    {{ $treatment->promo_type == 'percent' ? $treatment->promo_value.'%' : 'Rp '.number_format($treatment->promo_value) }}
                                                </span>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end px-3">
                                            <div class="d-flex justify-content-end gap-2">
                                                <button class="btn btn-light action-btn view-detail text-info" title="Lihat Detail">
                                                    <i class="ti ti-eye fs-5"></i>
                                                </button>
                                                <a href="{{ route('treatment.edit', $treatment->id) }}" class="btn btn-light action-btn text-warning" title="Edit">
                                                    <i class="ti ti-edit fs-5"></i>
                                                </a>
                                                <form action="{{ route('treatment.destroy', $treatment->id) }}" method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-light action-btn text-danger" title="Hapus" onclick="return confirm('Hapus treatment ini?')">
                                                        <i class="ti ti-trash fs-5"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="py-4">
                                                <i class="ti ti-search fs-1 text-muted mb-3 d-block"></i>
                                                <h5 class="text-muted">Tidak ada treatment yang ditemukan</h5>
                                                <p class="small text-muted">Coba ubah filter atau kata kunci pencarian Anda.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $treatments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Kategori -->
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold"><i class="ti ti-category me-2 text-pink"></i>Kelola Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="formAddCategory" class="mb-4">
                        @csrf
                        <div class="input-group shadow-sm rounded-3 overflow-hidden border">
                            <input type="text" id="newCategoryName" class="form-control border-0" placeholder="Kategori baru...">
                            <button type="submit" class="btn btn-primary border-0 px-3">Tambah</button>
                        </div>
                    </form>

                    <div class="table-responsive rounded-3 border" style="max-height: 400px;">
                        <table class="table table-hover align-middle mb-0" id="categoryTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="small fw-bold py-3">NAMA KATEGORI</th>
                                    <th class="small fw-bold text-end py-3">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $category)
                                    <tr data-id="{{ $category->id }}">
                                        <td contenteditable="true" class="editable-category fw-medium">{{ $category->name }}</td>
                                        <td class="text-end">
                                            <button class="btn btn-link text-danger btn-delete-category p-0 shadow-none">
                                                <i class="ti ti-trash fs-5"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Treatment -->
    <div class="modal fade" id="treatmentDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-body p-0">
                    <div class="row g-0">
                        <div class="col-md-5 bg-light d-flex align-items-center justify-content-center p-4">
                            <img id="popupImage" src="" class="img-fluid rounded-4 shadow-sm" style="max-height: 300px; width: 100%; object-fit: cover;">
                        </div>
                        <div class="col-md-7 p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <span id="popupCategory" class="category-badge mb-2 d-inline-block"></span>
                                    <h3 id="popupName" class="fw-bold text-dark"></h3>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="promo-info mb-4" id="popupPromoWrapper">
                                <span class="promo-tag">
                                    <i class="ti ti-discount-2 me-1"></i> Promo: <span id="popupPromo"></span>
                                </span>
                            </div>

                            <h6 class="fw-bold mb-3">Variasi & Harga:</h6>
                            <div class="list-group list-group-flush rounded-3 border overflow-hidden" id="popupDetails">
                                <!-- Ajax Content -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
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
        // Modal Instances
        const categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));
        const detailModal = new bootstrap.Modal(document.getElementById('treatmentDetailModal'));

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
                    $('.treatment-card-table tbody').html(response);
                }
            });
        }

        $('#filterCategory, #sortBy').change(applyFilterSortSearch);
        $('#searchInput').on('keyup', function (e) {
            if (e.keyCode === 13) applyFilterSortSearch();
        });

        // View Detail Modal
        $(document).on('click', '.view-detail', function () {
            let row = $(this).closest('tr');
            let details = row.data('details');
            let image = row.data('image');
            
            $('#popupName').text(row.data('name'));
            $('#popupCategory').text(row.data('category'));
            
            if(row.data('promo') && row.data('promo') !== '-') {
                $('#popupPromo').text(row.data('promo'));
                $('#popupPromoWrapper').show();
            } else {
                $('#popupPromoWrapper').hide();
            }

            let baseUrl = "https://{{ env('SUPABASE_PROJECT_REF') }}.supabase.co/storage/v1/object/public/{{ env('SUPABASE_BUCKET') }}/";
            $('#popupImage').attr('src', image ? baseUrl + image : "{{ asset('assets/images/no-image.jpg') }}");

            let html = '';
            details.forEach(function (d) {
                html += `
                    <div class="list-group-item p-3 border-0 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold">${d.name}</span>
                            <span class="fw-bold text-primary">Rp ${new Intl.NumberFormat('id-ID').format(d.price)}</span>
                        </div>
                        <div class="d-flex gap-3 small text-muted">
                            <span><i class="ti ti-clock me-1"></i>${d.duration} mnt</span>
                            ${d.description ? `<span><i class="ti ti-info-circle me-1"></i>${d.description}</span>` : ''}
                        </div>
                    </div>`;
            });

            $('#popupDetails').html(html);
            detailModal.show();
        });

        // Category Modal
        $('#btnViewCategories').click(() => categoryModal.show());

        // Add Category
        $('#formAddCategory').submit(function (e) {
            e.preventDefault();
            let name = $('#newCategoryName').val().trim();
            if(!name) return;

            $.ajax({
                url: "{{ route('categories.store') }}",
                type: "POST",
                data: { name: name, _token: '{{ csrf_token() }}' },
                success: function(res){
                    let category = res.data ?? res;
                    $('#categoryTable tbody').append(`
                        <tr data-id="${category.id}">
                            <td contenteditable="true" class="editable-category fw-medium">${category.name}</td>
                            <td class="text-end">
                                <button class="btn btn-link text-danger btn-delete-category p-0 shadow-none">
                                    <i class="ti ti-trash fs-5"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                    $('#newCategoryName').val('');
                }
            });
        });

        // Edit Category Inline
        $(document).on('blur', '.editable-category', function () {
            let row = $(this).closest('tr');
            let id = row.data('id');
            let name = $(this).text().trim();

            if(!name) return;

            $.ajax({
                url: '/categories/' + id,
                type: 'POST',
                data: { name: name, _token: '{{ csrf_token() }}', _method: 'PUT' }
            });
        });

        // Delete Category
        $(document).on('click', '.btn-delete-category', function () {
            if(!confirm('Hapus kategori ini?')) return;
            let row = $(this).closest('tr');
            let id = row.data('id');

            $.ajax({
                url: '/categories/' + id,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                success: () => row.remove()
            });
        });
    </script>
@endpush
@endsection