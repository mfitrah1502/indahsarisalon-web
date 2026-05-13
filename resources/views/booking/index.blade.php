@extends('layout.dashboard')

@section('title', 'Book an Appointment')

<style>
    .treatment-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
        cursor: pointer;
        height: 100%;
    }

    .treatment-card:hover {
        transform: scale(1.03);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    /* 🔥 INI KUNCI PORTRAIT */
    .treatment-card img {
        width: 100%;
        height: 380px;
        /* makin besar = makin portrait */
        object-fit: cover;
    }

    .card-body {
        display: flex;
        flex-direction: column;
    }

    .btn-book {
        margin-top: auto;
        width: 30%;
        border-radius: 8px;
    }

    /* Floating Cart Styles */
    #floatingCart {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        width: 90%;
        max-width: 600px;
        background: #fff;
        border-radius: 50px;
        box-shadow: 0 10px 30px rgba(234, 130, 144, 0.3);
        padding: 12px 25px;
        display: none;
        z-index: 1050;
        border: 2px solid #EA8290;
        animation: slideUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    @keyframes slideUp {
        from { bottom: -100px; opacity: 0; }
        to { bottom: 20px; opacity: 1; }
    }

    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .extra-small {
        font-size: 0.7rem;
    }
</style>
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Book an Appointment</h4>
                </div>

                <div class="card-body">
                    @if(!$isOpen)
                        <div class="alert alert-warning border-0 shadow-sm mb-4 d-flex align-items-center rounded-3">
                            <div class="flex-shrink-0">
                                <i class="ti ti-clock-off fs-1 text-warning me-3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="alert-heading fw-bold mb-1">Hari ini sedang tutup</h5>
                                <p class="mb-0 small">Mohon maaf, jam operasional salon adalah <strong>09:00 - 18:00</strong>.
                                    Anda tetap dapat melakukan booking untuk waktu operasional yang tersedia.</p>
                            </div>
                        </div>
                    @endif

                    <!-- Filter kategori & search -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <select id="categoryFilter" class="form-select">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" id="searchInput" class="form-control" placeholder="Cari treatment...">
                        </div>
                    </div>

                    <!-- Daftar treatment -->
                    <div class="row" id="treatmentList">
                        @include('booking.partials._treatment_list')
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Floating Cart UI -->
    <div id="floatingCart" class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <div class="bg-soft-primary p-2 rounded-circle me-3">
                <i class="ti ti-shopping-cart text-primary fs-4"></i>
            </div>
            <div>
                <div class="fw-bold text-dark" id="cartCount">0 Layanan dipilih</div>
                <div class="extra-small text-muted" id="cartSummary">Silakan pilih layanan</div>
            </div>
        </div>
        <button type="button" id="btnGoToSelect" class="btn btn-primary rounded-pill px-4 shadow-sm">
            Lanjut Booking <i class="ti ti-chevron-right ms-1"></i>
        </button>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                // Theme Config (Safe Check) - only if functions exist
                if (typeof layout_change === 'function') {
                    layout_change('light');
                    font_change('Roboto');
                    change_box_container('false');
                    layout_caption_change('true');
                    layout_rtl_change('false');
                    preset_change('preset-1');
                }

                let searchTimer;

                function loadTreatments() {
                    let category = $('#categoryFilter').val();
                    let search = $('#searchInput').val();

                    console.log('Loading treatments...', { category, search });

                    // Show a subtle loading state
                    $('#treatmentList').css('opacity', '0.5');

                    $.ajax({
                        url: "{{ route('booking.index') }}",
                        type: 'GET',
                        data: {
                            category: category,
                            search: search,
                            is_ajax: 1
                        },
                        success: function (html) {
                            console.log('Treatments loaded successfully');
                            $('#treatmentList').html(html).css('opacity', '1');
                        },
                        error: function (err) {
                            console.error('AJAX Error:', err);
                            $('#treatmentList').css('opacity', '1');
                        }
                    });
                }

                // Use 'input' event instead of 'keyup' to catch all changes (paste, clear, etc.)
                $('#searchInput').on('input', function () {
                    console.log('Search input changed:', $(this).val());
                    clearTimeout(searchTimer);
                    searchTimer = setTimeout(loadTreatments, 500);
                });

                $('#categoryFilter').on('change', function() {
                    console.log('Category filter changed:', $(this).val());
                    loadTreatments();
                });

                // Cart Logic
                let selectedDetails = [];

                $(document).on('click', '.btn-add-cart', function() {
                    const btn = $(this);
                    const detail = {
                        id: btn.data('id'),
                        treatmentId: btn.data('treatment-id'),
                        name: btn.data('name'),
                        treatmentName: btn.data('treatment-name'),
                        allowMulti: btn.data('allow-multi') === 1
                    };

                    // Check if already selected
                    const isExist = selectedDetails.some(d => d.id === detail.id);
                    if (isExist) {
                        // Remove if clicked again
                        selectedDetails = selectedDetails.filter(d => d.id !== detail.id);
                        btn.removeClass('btn-success').addClass('btn-primary').text('Pilih');
                    } else {
                        // Check if same treatment already has another detail selected (and doesn't allow multi)
                        if (!detail.allowMulti) {
                            const sameTreatmentDetail = selectedDetails.find(d => d.treatmentId === detail.treatmentId);
                            if (sameTreatmentDetail) {
                                alert(`Layanan "${detail.treatmentName}" sudah dipilih varian "${sameTreatmentDetail.name}".\n\nAnda hanya dapat memilih satu jenis layanan untuk kategori ini.`);
                                return;
                            }
                        }
                        
                        selectedDetails.push(detail);
                        btn.removeClass('btn-primary').addClass('btn-success').text('Terpilih');
                    }

                    updateCartUI();
                });

                function updateCartUI() {
                    const count = selectedDetails.length;
                    const cart = $('#floatingCart');
                    
                    if (count > 0) {
                        cart.fadeIn();
                        $('#cartCount').text(count + ' Layanan dipilih');
                        
                        // Create summary text
                        const names = selectedDetails.map(d => d.name).join(', ');
                        $('#cartSummary').text(names.length > 40 ? names.substring(0, 37) + '...' : names);
                    } else {
                        cart.fadeOut();
                    }

                    // Update all buttons visual state
                    $('.btn-add-cart').each(function() {
                        const b = $(this);
                        const id = b.data('id');
                        if (selectedDetails.some(d => d.id === id)) {
                            b.removeClass('btn-primary').addClass('btn-success').text('Terpilih');
                        } else {
                            b.removeClass('btn-success').addClass('btn-primary').text(b.hasClass('w-100') ? 'Pilih Treatment' : 'Pilih');
                        }
                    });
                }

                $('#btnGoToSelect').on('click', function() {
                    if (selectedDetails.length === 0) return;
                    
                    const ids = selectedDetails.map(d => d.id).join(',');
                    // Redirect to select page with multiple IDs
                    window.location.href = "{{ route('booking.select', ['treatmentId' => ':id']) }}".replace(':id', selectedDetails[0].treatmentId) + '?details=' + ids;
                });
            });
        </script>
    @endpush
@endsection