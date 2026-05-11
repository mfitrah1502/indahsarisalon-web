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
                                    <th class="text-muted small fw-bold py-2">STATUS</th>
                                    <th class="text-muted small fw-bold py-2">HARGA</th>
                                    <th class="text-muted small fw-bold py-2 text-end px-3">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($treatments as $treatment)
                                    @php
                                        $detailImage = $treatment->details->whereNotNull('image_url')->first();
                                        $hasImage = false;
                                        
                                        if ($detailImage && $detailImage->image_url) {
                                            $imageUrl = $detailImage->image_url;
                                            $hasImage = true;
                                        } elseif (!$treatment->image) {
                                            $imageUrl = asset('assets/images/no-image.jpg');
                                        } elseif (strpos($treatment->image, 'http') === 0) {
                                            $imageUrl = $treatment->image;
                                            $hasImage = true;
                                        } else {
                                            $bucket = ($treatment->is_promo && env('SUPABASE_PROMO_BUCKET')) ? env('SUPABASE_PROMO_BUCKET') : env('SUPABASE_BUCKET');
                                            $imageUrl = env('SUPABASE_URL') . '/storage/v1/object/public/' . $bucket . '/' . $treatment->image;
                                            $hasImage = true;
                                        }
                                    @endphp
                                    <tr class="treatment-row" 
                                        data-id="{{ $treatment->id }}"
                                        data-name="{{ $treatment->name }}"
                                        data-category="{{ $treatment->category->name ?? '-' }}"
                                        data-promo-end="{{ $treatment->promo_end_date ? \Carbon\Carbon::parse($treatment->promo_end_date)->format('d F Y') : '' }}"
                                        data-details='@json($treatment->details)'
                                        data-image="{{ $imageUrl }}">
                                        <td class="px-3">
                                            <div class="d-flex align-items-center">
                                                <div class="treatment-icon me-3">
                                                    @if($hasImage)
                                                        <img src="{{ $imageUrl }}" 
                                                             class="rounded-3 shadow-sm" width="50" height="50" style="object-fit:cover;">
                                                    @else
                                                        <div class="bg-light rounded-3 d-flex align-items-center justify-content-center" style="width:50px; height:50px;">
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
                                                @if($treatment->is_promo)
                                                    <div class="d-block">
                                                        <span class="promo-tag shadow-sm animate__animated animate__pulse animate__infinite mb-1 d-inline-block">
                                                            <i class="ti ti-discount-2 me-1"></i>PROMO
                                                        </span>
                                                        @if($treatment->promo_start_date || $treatment->promo_end_date)
                                                            <div class="mt-1 extra-small text-pink-600 fw-bold" style="font-size: 0.65rem;">
                                                                <i class="ti ti-calendar-event me-1"></i>
                                                                {{ $treatment->promo_start_date ? $treatment->promo_start_date->format('d/m') : '...' }}
                                                                -
                                                                {{ $treatment->promo_end_date ? $treatment->promo_end_date->format('d/m/y') : '...' }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="category-badge">{{ $treatment->category->name ?? '-' }}</span>
                                                @endif
                                        </td>
                                        <td>
                                            @if($treatment->is_active)
                                                <span class="badge bg-light-success text-success border border-success border-opacity-10 px-3 rounded-pill">
                                                    <i class="ti ti-circle-check me-1"></i> Aktif
                                                </span>
                                            @else
                                                <span class="badge bg-light-danger text-danger border border-danger border-opacity-10 px-3 rounded-pill">
                                                    <i class="ti ti-circle-x me-1"></i> Non-aktif
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark">
                                                @php
                                                    $minPrice = $treatment->details->min('price') ?? 0;
                                                    $maxPrice = $treatment->details->max('price') ?? 0;
                                                @endphp
                                                @if($minPrice != $maxPrice)
                                                    Rp {{ number_format($minPrice, 0, ',', '.') }} - Rp {{ number_format($maxPrice, 0, ',', '.') }}
                                                @else
                                                    Rp {{ number_format($minPrice, 0, ',', '.') }}
                                                @endif
                                            </span>
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

    <!-- Broadcast Promo Modal -->
    <div class="modal fade" id="broadcastModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-success text-white border-bottom-0 rounded-top-4">
                    <h5 class="modal-title fw-bold"><i class="ti ti-brand-whatsapp me-2"></i>Siarkan Promo ke Pelanggan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('treatment.broadcast') }}" method="POST" id="broadcastForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <p class="text-muted mb-4">Sistem akan mengambil semua treatment yang statusnya sedang promo dan mengirimkan daftarnya ke WhatsApp seluruh pelanggan yang terdaftar.</p>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Unggah Banner Promo (Opsional)</label>
                            <input type="file" name="promo_images[]" class="form-control" multiple accept="image/*">
                            <small class="text-muted d-block mt-2">
                                <i class="ti ti-info-circle text-info"></i> Anda bisa memilih lebih dari 1 gambar. Gambar yang diunggah di sini akan dikirim ke pelanggan.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-success rounded-pill px-4" onclick="confirmBroadcast(this)">
                            <i class="ti ti-send me-1"></i> Kirim Sekarang
                        </button>
                    </div>
                </form>
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
                        <div class="col-md-5 bg-light d-flex align-items-center justify-content-center p-4 position-relative">
                            <!-- Single Image -->
                            <img id="popupImage" src="" class="img-fluid rounded-4 shadow-sm" style="max-height: 300px; width: 100%; object-fit: cover;">
                            
                            <!-- Carousel for multiple images -->
                            <div id="popupImageCarousel" class="carousel slide w-100" data-bs-ride="carousel" style="display: none;">
                                <div class="carousel-inner rounded-4 shadow-sm" id="popupImageCarouselInner">
                                    <!-- Carousel items go here -->
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#popupImageCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.5); border-radius: 50%; padding: 15px;"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#popupImageCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.5); border-radius: 50%; padding: 15px;"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-7 p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <span id="popupCategory" class="category-badge mb-2 d-inline-block"></span>
                                    <h3 id="popupName" class="fw-bold text-dark"></h3>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="promo-info mb-4" id="popupPromoWrapper" style="display: none;">
                                <span class="promo-tag">
                                    <i class="ti ti-discount-2 me-1"></i> PROMO SPESIAL
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
                    <button type="button" class="btn btn-outline-success rounded-pill px-4 me-auto" id="btnSpreadPromo">
                        <i class="ti ti-share me-1"></i> Sebarkan
                    </button>
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Choice Spread -->
    <div class="modal fade" id="spreadChoiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold"><i class="ti ti-share me-2 text-primary"></i>Sebarkan Ke:</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-grid gap-3">
                        <button class="btn btn-outline-primary py-3 rounded-4" id="btnToCustomer">
                            <i class="ti ti-users fs-2 d-block mb-1"></i>
                            Kirim ke Pelanggan
                        </button>
                        <button class="btn btn-outline-success py-3 rounded-4" id="btnToCommunity">
                            <i class="ti ti-brand-whatsapp fs-2 d-block mb-1"></i>
                            Kirim ke Komunitas
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Select Customer -->
    <div class="modal fade" id="selectCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-primary text-white border-bottom-0 rounded-top-4">
                    <h5 class="modal-title fw-bold"><i class="ti ti-users me-2"></i>Pilih Pelanggan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="input-group mb-3 shadow-sm rounded-pill overflow-hidden border">
                        <span class="input-group-text bg-white border-0"><i class="ti ti-search text-muted"></i></span>
                        <input type="text" id="customerSearchInput" class="form-control border-0" placeholder="Cari nama atau nomor pelanggan...">
                    </div>
                    <div class="customer-list-scrollable" style="max-height: 400px; overflow-y: auto;">
                        <div class="list-group list-group-flush" id="customerList">
                            @foreach($customers as $customer)
                                <label class="list-group-item list-group-item-action d-flex align-items-center gap-3 p-3 border-0 border-bottom customer-item" data-search="{{ strtolower($customer->name) }} {{ $customer->phone }}">
                                    <input class="form-check-input flex-shrink-0" type="radio" name="selectedCustomer" value="{{ $customer->phone }}" data-name="{{ $customer->name }}">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold text-dark">{{ $customer->name }}</div>
                                        <small class="text-muted"><i class="ti ti-brand-whatsapp me-1"></i>{{ $customer->phone }}</small>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4" id="btnConfirmSendCustomer">
                        <i class="ti ti-send me-1"></i> Kirim Ke WA
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .customer-item:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }
        .customer-item input:checked + div {
            font-weight: bold;
        }
    </style>
    @endpush
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
            
            // Image Carousel Logic
            let carouselImages = [];
            if (image) {
                carouselImages.push(image);
            }
            if (details && details.length > 0) {
                details.forEach(function(d) {
                    if (d.image_url) {
                        carouselImages.push(d.image_url);
                    }
                });
            }

            if (carouselImages.length > 1) {
                $('#popupImage').hide();
                let innerHtml = '';
                carouselImages.forEach(function(img, idx) {
                    let active = idx === 0 ? 'active' : '';
                    innerHtml += `
                        <div class="carousel-item ${active}">
                            <img src="${img}" class="d-block w-100" style="max-height: 300px; object-fit: cover;">
                        </div>
                    `;
                });
                $('#popupImageCarouselInner').html(innerHtml);
                $('#popupImageCarousel').show();
            } else if (carouselImages.length === 1) {
                $('#popupImageCarousel').hide();
                $('#popupImage').attr('src', carouselImages[0]).show();
            } else {
                $('#popupImageCarousel').hide();
                $('#popupImage').attr('src', '').hide();
            }
            
            let isPromo = row.data('category') == 'Promo';

            let html = '';
            details.forEach(function (d) {
                let currentPrice = d.price;
                let priceHtml = `<span class="fw-bold text-primary">Rp ${new Intl.NumberFormat('id-ID').format(currentPrice)}</span>`;

                if (isPromo) {
                    $('#popupPromoWrapper').show();
                }

                html += `
                    <div class="list-group-item p-3 border-0 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold">${d.name}</span>
                            ${priceHtml}
                        </div>
                        <div class="d-flex gap-3 small text-muted">
                            <span><i class="ti ti-clock me-1"></i>${d.duration} mnt</span>
                            ${d.description ? `<span><i class="ti ti-info-circle me-1"></i>${d.description}</span>` : ''}
                        </div>
                    </div>`;
            });

            $('#popupDetails').html(html);
            
            // Store current treatment data for spreading
            $('#btnSpreadPromo').data('treatment', {
                id: row.data('id'),
                name: row.data('name'),
                category: row.data('category'),
                promoEnd: row.data('promo-end'),
                image: image,
                details: details
            });

            detailModal.show();
        });

        // Spread Promo Logic
        const spreadChoiceModal = new bootstrap.Modal(document.getElementById('spreadChoiceModal'));
        const selectCustomerModal = new bootstrap.Modal(document.getElementById('selectCustomerModal'));

        $('#btnSpreadPromo').click(function() {
            spreadChoiceModal.show();
        });

        $('#btnToCustomer').click(function() {
            spreadChoiceModal.hide();
            selectCustomerModal.show();
        });

        $('#btnToCommunity').click(function() {
            const treatment = $('#btnSpreadPromo').data('treatment');
            const communityLink = "https://chat.whatsapp.com/GisLhO7PqBwF9VfC6L9A9P"; // Default community link from history
            
            spreadPromo(null, treatment, communityLink);
        });

        $('#btnConfirmSendCustomer').click(function() {
            const selected = $('input[name="selectedCustomer"]:checked');
            if (selected.length === 0) {
                alert('Pilih pelanggan terlebih dahulu');
                return;
            }

            const treatment = $('#btnSpreadPromo').data('treatment');
            const phone = selected.val();
            const name = selected.data('name');

            spreadPromo({ phone: phone, name: name }, treatment);
        });

        // Customer Search logic
        $('#customerSearchInput').on('keyup', function() {
            const val = $(this).val().toLowerCase();
            $('.customer-item').each(function() {
                const searchTxt = $(this).data('search');
                if (searchTxt.indexOf(val) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        async function spreadPromo(customer, treatment, groupLink = null) {
            // 1. Construct Caption
            let caption = `Halo ${customer ? '*' + customer.name + '*' : 'semuanya'}! 🌸\n\n`;
            caption += `Ada promo menarik di *Indah Sari Salon*:\n\n`;
            caption += `*${treatment.name}*\n`;
            
            // Get price display (first price or range)
            let prices = treatment.details.map(d => d.price);
            let minPrice = Math.min(...prices);
            let maxPrice = Math.max(...prices);
            let priceText = minPrice === maxPrice 
                ? `Rp ${new Intl.NumberFormat('id-ID').format(minPrice)}`
                : `Mulai Rp ${new Intl.NumberFormat('id-ID').format(minPrice)}`;
            
            caption += `Hanya *${priceText}*!\n\n`;

            // List variations
            let variations = treatment.details.map(d => d.name).join(', ');
            caption += `Treatment: ${variations}\n`;

            if (treatment.promoEnd) {
                caption += `Berlaku sampai: ${treatment.promoEnd}\n`;
            }

            caption += `\nYuk booking sekarang lewat aplikasi atau hubungi kami langsung!\n`;
            caption += `Klik link ini: ${window.location.origin}/booking/select/${treatment.id || ''}\n\nSampai jumpa di salon!`;

            // 2. Copy Image to Clipboard
            const btn = customer ? $('#btnConfirmSendCustomer') : $('#btnToCommunity');
            const originalHtml = btn.html();
            btn.html('<span class="spinner-border spinner-border-sm me-1"></span> Mengcopy Gambar...');
            btn.prop('disabled', true);

            try {
                if (treatment.image && !treatment.image.includes('no-image.jpg')) {
                    const copied = await copyImageToClipboard(treatment.image);
                    if (copied) {
                        alert('Gambar promo telah di-copy otomatis! Silakan PASTE (Ctrl+V) saat WhatsApp terbuka.');
                    }
                }
            } catch (e) {
                console.error('Gagal mengcopy gambar:', e);
            }

            // 3. Redirect to WhatsApp
            let waUrl = '';
            const encodedCaption = encodeURIComponent(caption);
            
            if (groupLink) {
                waUrl = `https://wa.me/?text=${encodedCaption}`;
            } else {
                let formattedPhone = customer.phone.replace(/[^0-9]/g, '');
                if (formattedPhone.startsWith('0')) {
                    formattedPhone = '62' + formattedPhone.substring(1);
                }
                waUrl = `https://wa.me/${formattedPhone}?text=${encodedCaption}`;
            }

            btn.html(originalHtml);
            btn.prop('disabled', false);
            
            if (customer) selectCustomerModal.hide();
            spreadChoiceModal.hide();

            // Use a slight timeout to ensure modals are closing and browser registers the intent
            setTimeout(() => {
                const waWindow = window.open(waUrl, '_blank');
                if (!waWindow) {
                    // Fallback: if blocked, use location.href or show a link
                    if(confirm('Pop-up WhatsApp terblokir oleh browser. Klik OK untuk mencoba membuka di tab ini.')) {
                        window.location.href = waUrl;
                    }
                }
            }, 100);
        }

        async function copyImageToClipboard(imageUrl) {
            try {
                const response = await fetch(imageUrl);
                const blob = await response.blob();
                
                // Clipboard API requires PNG for images in most browsers
                // If it's not PNG, we might need to convert it.
                // But let's try direct first.
                
                let blobToCopy = blob;
                if (blob.type !== 'image/png') {
                    // Convert to PNG using Canvas
                    blobToCopy = await convertToPng(blob);
                }

                const data = [new ClipboardItem({ [blobToCopy.type]: blobToCopy })];
                await navigator.clipboard.write(data);
                return true;
            } catch (err) {
                console.error('Gagal menyalin gambar ke clipboard:', err);
                return false;
            }
        }

        function convertToPng(blob) {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0);
                    canvas.toBlob((pngBlob) => {
                        resolve(pngBlob);
                    }, 'image/png');
                };
                img.onerror = reject;
                img.src = URL.createObjectURL(blob);
            });
        }

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
        function confirmBroadcast(btn) {
            if (confirm('Apakah Anda yakin ingin mengirimkan pesan promo dan gambar-gambar ini ke SELURUH pelanggan? Proses ini mungkin memakan waktu.')) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengirim...';
                document.getElementById('broadcastForm').submit();
            }
        }
    </script>
@endpush
@endsection