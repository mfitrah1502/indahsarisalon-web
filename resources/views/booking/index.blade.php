@extends('layout.dashboard')

@section('title', 'Book an Appointment')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Styling Flatpickr agar senada dengan UI Pink */
    .flatpickr-day.selected {
        background: #EA8290 !important;
        border-color: #EA8290 !important;
    }
    .flatpickr-day.disabled {
        color: #dcdcdc !important;
        background: #f8f9fa !important;
    }

    /* Styling Tanggal Reservasi agar Terang, Jelas, dan Interaktif */
    #main_reservation_date {
        background-color: #fff !important;
        color: #EA8290 !important;
        border: 2px solid #EA8290 !important;
        opacity: 1 !important;
        font-weight: 800 !important;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(234, 130, 144, 0.15) !important;
    }
    #main_reservation_date:hover {
        background-color: #fff5f6 !important;
        border-color: #d66877 !important;
        box-shadow: 0 6px 16px rgba(234, 130, 144, 0.25) !important;
        transform: translateY(-1px);
    }

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

    .treatment-card-highlighted {
        border: 2px solid #EA8290 !important;
        box-shadow: 0 0 20px rgba(234, 130, 144, 0.6) !important;
        animation: card-pulse 1.5s infinite alternate ease-in-out;
    }

    @keyframes card-pulse {
        0% {
            transform: scale(1.0);
            box-shadow: 0 0 15px rgba(234, 130, 144, 0.4);
        }
        100% {
            transform: scale(1.03);
            box-shadow: 0 0 25px rgba(234, 130, 144, 0.8);
        }
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

    /* Stylist Card Modern Styles */
    .stylist-grid {
        display: flex;
        overflow-x: auto;
        gap: 12px;
        padding: 5px 2px 15px 2px;
        scrollbar-width: thin;
        scrollbar-color: #EA8290 transparent;
    }
    .stylist-grid::-webkit-scrollbar {
        height: 6px;
    }
    .stylist-grid::-webkit-scrollbar-thumb {
        background: #EA8290;
        border-radius: 10px;
    }
    .stylist-card-modern {
        flex: 0 0 100px;
        background: #fff;
        border: 2px solid #f0f0f0;
        border-radius: 15px;
        padding: 12px 8px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }
    .stylist-card-modern:hover {
        border-color: #EA8290;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }
    .stylist-card-modern.active {
        border-color: #EA8290;
        background: #fff5f6;
        box-shadow: 0 4px 12px rgba(234, 130, 144, 0.2);
    }
    .stylist-card-modern .avatar-container {
        width: 50px;
        height: 50px;
        margin: 0 auto 8px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid #fff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .stylist-card-modern .avatar-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .stylist-card-modern .stylist-name {
        font-size: 0.75rem;
        font-weight: 700;
        color: #333;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .stylist-card-modern .stylist-cat {
        font-size: 0.6rem;
        color: #888;
        display: block;
    }
    .stylist-card-modern .check-mark {
        position: absolute;
        top: 5px;
        right: 5px;
        background: #EA8290;
        color: #fff;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        font-size: 10px;
        display: none;
        align-items: center;
        justify-content: center;
    }
    .stylist-card-modern.disabled {
        opacity: 0.45;
        background: #f8f9fa;
        border-color: #dee2e6;
        cursor: not-allowed;
        pointer-events: none !important;
        position: relative;
    }
    .stylist-card-modern.disabled::after {
        content: 'Sibuk';
        position: absolute;
        bottom: 2px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 0.55rem;
        background: #dc3545;
        color: #fff;
        padding: 1px 4px;
        border-radius: 4px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stylist-card-modern.disabled.off-work::after {
        content: 'Libur';
        background: #6c757d;
    }

    /* Dimmed/Disabled Treatment Styles */
    .treatment-item-dimmed {
        opacity: 0.35;
        filter: grayscale(0.6);
        transition: all 0.3s ease;
    }
    .treatment-item-dimmed .btn-add-cart {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        cursor: not-allowed;
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
@endpush
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

                    <!-- Pemilihan Waktu & Stylist (Premium Side-by-Side Layout) -->
                    <div class="row g-3 mb-4">
                        <!-- Pilih Tanggal Reservasi -->
                        <div class="col-md-4">
                            <div class="p-3 bg-white border rounded-3 shadow-sm h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <label class="form-label fw-bold mb-2"><i class="ti ti-calendar me-1 text-primary"></i>Pilih Tanggal Reservasi</label>
                                    <p class="small text-muted mb-3">Tentukan tanggal kunjungan Anda ke salon terlebih dahulu. Batas waktu reservasi (09:00-17:00) </p>
                                </div>
                                @php
                                    $now = \Carbon\Carbon::now();
                                    $cutoff = \Carbon\Carbon::today()->setHour(17)->setMinute(0);
                                    $initialDate = $now->greaterThan($cutoff) ? \Carbon\Carbon::tomorrow()->toDateString() : \Carbon\Carbon::today()->toDateString();
                                @endphp
                                <div class="position-relative mt-2">
                                    <input type="text" id="main_reservation_date" class="form-control form-control-lg rounded-3 text-center cursor-pointer" readonly value="{{ $initialDate }}" style="font-size: 1.1rem; height: 50px; padding-left: 45px; padding-right: 45px;">
                                    <div class="position-absolute top-50 start-0 translate-middle-y ps-3 text-primary pointer-events-none" style="pointer-events: none; color: #EA8290 !important;">
                                        <i class="ti ti-calendar fs-4"></i>
                                    </div>
                                    <div class="position-absolute top-50 end-0 translate-middle-y pe-3 text-primary pointer-events-none" style="pointer-events: none; color: #EA8290 !important;">
                                        <i class="ti ti-chevron-down fs-5"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pemilihan Stylist Terlebih Dahulu (Premium Layout) -->
                        <div class="col-md-8">
                            <div class="p-3 bg-white border rounded-3 shadow-sm h-100">
                                <label class="form-label fw-bold mb-2"><i class="ti ti-heart-handshake me-1 text-primary"></i>Pilih Stylist Pilihan Anda</label>
                                <p class="small text-muted mb-3">Silakan pilih stylist terlebih dahulu. Kategori treatment akan disesuaikan dengan posisi keahlian stylist yang dipilih.</p>
                                <div class="stylist-grid" id="main_stylist_grid">
                                    <div class="stylist-card-modern active" data-stylist-id="" data-position="" onclick="selectMainStylist(null, this)">
                                        <div class="check-mark"><i class="ti ti-check"></i></div>
                                        <div class="avatar-container d-flex align-items-center justify-content-center bg-light">
                                            <i class="ti ti-minus text-muted" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <span class="stylist-name">Semua</span>
                                        <span class="stylist-cat">Default</span>
                                    </div>
                                    @forelse($stylists as $stylist)
                                        <div class="stylist-card-modern" 
                                             data-stylist-id="{{ $stylist->id }}" 
                                             data-stylist-name="{{ $stylist->name }}"
                                             data-position="{{ strtolower($stylist->position) }}"
                                             onclick="selectMainStylist({{ $stylist->id }}, this)">
                                            <div class="check-mark"><i class="ti ti-check"></i></div>
                                            <div class="avatar-container">
                                                <img src="{{ $stylist->avatar_url }}" alt="{{ $stylist->name }}">
                                            </div>
                                            <span class="stylist-name">{{ explode(' ', $stylist->name)[0] }}</span>
                                            <span class="stylist-cat">{{ ucwords(strtolower($stylist->position)) }}</span>
                                        </div>
                                    @empty
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

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
        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- Flatpickr JS -->
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
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

                // Global selected stylist tracker
                let selectedStylist = null;

                // Flatpickr Initialization
                const holidayDates = {!! json_encode($holidays ?? []) !!};
                const dateInput = document.getElementById('main_reservation_date');
                
                function updateBookedStylists() {
                    const date = dateInput.value;
                    if (!date) return;

                    $.ajax({
                        url: "{{ route('booking.check_booked_stylists') }}",
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            reservation_date: date
                        },
                        success: function (response) {
                            const bookedIds = response.booked_stylist_ids || [];
                            const offWorkIds = response.off_work_ids || [];

                            $('#main_stylist_grid .stylist-card-modern').each(function () {
                                const card = $(this);
                                const stylistId = parseInt(card.data('stylist-id'));
                                if (!stylistId) return; // Skip "Semua" card

                                const isBooked = bookedIds.includes(stylistId);
                                const isOff = offWorkIds.includes(stylistId);

                                card.removeClass('disabled busy off-work');
                                card.css('pointer-events', '');
                                card.css('opacity', '');
                                
                                if (isBooked) {
                                    card.addClass('busy disabled');
                                    card.css('pointer-events', 'none');
                                    card.css('opacity', '0.5');
                                    if (selectedStylist && selectedStylist.id === stylistId) {
                                        selectMainStylist(null, $('#main_stylist_grid .stylist-card-modern[data-stylist-id=""]'));
                                    }
                                } else if (isOff) {
                                    card.addClass('off-work disabled');
                                    card.css('pointer-events', 'none');
                                    card.css('opacity', '0.5');
                                    if (selectedStylist && selectedStylist.id === stylistId) {
                                        selectMainStylist(null, $('#main_stylist_grid .stylist-card-modern[data-stylist-id=""]'));
                                    }
                                }
                            });
                        }
                    });
                }

                const fp = flatpickr(dateInput, {
                    locale: 'id',
                    dateFormat: 'Y-m-d',
                    minDate: 'today',
                    disable: holidayDates,
                    defaultDate: dateInput.value || 'today',
                    onChange: function(selectedDates, dateStr) {
                        updateBookedStylists();
                    }
                });

                function findNextAvailableMain() {
                    const now = new Date();
                    const hour = now.getHours();
                    const todayStr = now.toISOString().split('T')[0];

                    if (hour >= 18 || holidayDates.includes(todayStr)) {
                        now.setDate(now.getDate() + 1);
                        while(holidayDates.includes(now.toISOString().split('T')[0])) {
                            now.setDate(now.getDate() + 1);
                        }
                        fp.setDate(now);
                    }
                }
                findNextAvailableMain();
                updateBookedStylists();

                function filterTreatmentsLocal() {
                    let categoryId = $('#categoryFilter').val();
                    let search = $('#searchInput').val().toLowerCase().trim();

                    console.log('Filtering treatments locally:', { categoryId, search });

                    let visibleCount = 0;

                    $('.treatment-wrapper').each(function() {
                        let wrapper = $(this);
                        let wrapperCatId = wrapper.data('category-id') ? wrapper.data('category-id').toString() : '';
                        let name = wrapper.data('treatment-name') ? wrapper.data('treatment-name').toString().toLowerCase() : '';

                        let matchesCategory = !categoryId || wrapperCatId === categoryId;
                        let matchesSearch = !search || name.includes(search);

                        if (matchesCategory && matchesSearch) {
                            wrapper.show();
                            visibleCount++;
                        } else {
                            wrapper.hide();
                        }
                    });

                    // Manage "no treatments found" placeholder
                    if (visibleCount === 0) {
                        if ($('#noTreatmentsPlaceholder').length === 0) {
                            $('#treatmentList').append(`
                                <div id="noTreatmentsPlaceholder" class="col-12 text-center py-5">
                                    <div class="mb-3">
                                        <i class="ti ti-search text-muted" style="font-size: 4rem;"></i>
                                    </div>
                                    <h5 class="text-muted">Tidak ada treatment ditemukan</h5>
                                    <p class="small text-muted">Coba ubah kategori atau kata kunci pencarian Anda</p>
                                </div>
                            `);
                        } else {
                            $('#noTreatmentsPlaceholder').show();
                        }
                    } else {
                        $('#noTreatmentsPlaceholder').hide();
                    }
                }

                // Use 'input' event to catch all changes instantly
                $('#searchInput').on('input', function () {
                    filterTreatmentsLocal();
                });

                $('#categoryFilter').on('change', function() {
                    filterTreatmentsLocal();
                });

                // Cart Logic
                let selectedDetails = [];

                // Stylist mapping to categories (all lowercased for match)
                const stylistMapping = {
                    'creative stylist': ['haircut', 'hair coloring', 'hair colouring', 'hair cut', 'promo'],
                    'senior hair technician specialist': ['haircut', 'hair coloring', 'hair colouring', 'hair cut', 'promo'],
                    'senior beautician': ['facial', 'promo'],
                    'senior therapist': ['nail treatment', 'hair ritual', 'promo'],
                    'junior therapist': ['nail treatment', 'hair ritual', 'promo']
                };

                window.selectMainStylist = function(id, element) {
                    const card = $(element);
                    const position = card.data('position') ? card.data('position').toLowerCase() : '';
                    const name = card.data('stylist-name') || '';

                    if (card.hasClass('active')) {
                        return;
                    }

                    // Check compatibility if there are selected treatments
                    if (id !== null && selectedDetails.length > 0 && position) {
                        const allowedCats = stylistMapping[position] || [];
                        const incompatible = selectedDetails.filter(d => {
                            const btn = $(`.btn-add-cart[data-id="${d.id}"]`);
                            const cat = btn.data('category') ? btn.data('category').toLowerCase() : '';
                            return !allowedCats.includes(cat);
                        });

                        if (incompatible.length > 0) {
                            const listNamesHtml = incompatible.map(d => `<li class="text-start fs-6 mb-1"><strong>${d.treatmentName}</strong> (${d.name})</li>`).join('');
                            
                            Swal.fire({
                                title: 'Stylist Tidak Cocok',
                                html: `
                                    <div class="text-start text-muted small mb-3">
                                        Stylist <strong>${name}</strong> (${ucwords(position)}) tidak dapat melayani beberapa treatment pilihan Anda berikut:
                                    </div>
                                    <ul class="ps-3 mb-3 text-danger">
                                        ${listNamesHtml}
                                    </ul>
                                    <div class="text-start text-muted small">
                                        Memilih stylist ini akan membatalkan treatment tersebut. Lanjutkan?
                                    </div>
                                  `,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#EA8290',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText: 'Ya, Lanjutkan',
                                cancelButtonText: 'Batal',
                                customClass: {
                                    popup: 'rounded-4 border-0 shadow-lg'
                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Remove incompatible from selectedDetails
                                    const incompatibleIds = incompatible.map(d => d.id);
                                    selectedDetails = selectedDetails.filter(d => !incompatibleIds.includes(d.id));
                                    
                                    // Apply active stylist change
                                    setActiveStylist(id, card, name, position);
                                    updateCartUI();
                                }
                            });
                            return;
                        }
                    }

                    // If no incompatibilities, just apply
                    setActiveStylist(id, card, name, position);
                };

                function setActiveStylist(id, card, name, position) {
                    // Update active class on stylist cards
                    $('#main_stylist_grid .stylist-card-modern').removeClass('active');
                    card.addClass('active');

                    if (id === null) {
                        selectedStylist = null;
                    } else {
                        selectedStylist = { id: id, name: name, position: position };
                    }

                    // Apply styling/dimming to treatment wrappers
                    applyStylistFilters();
                }

                function ucwords(str) {
                    return str.replace(/\b[a-z]/g, function(letter) {
                        return letter.toUpperCase();
                    });
                }

                function applyStylistFilters() {
                    if (!selectedStylist || !selectedStylist.position) {
                        // Reset all dimming
                        $('.treatment-wrapper').removeClass('treatment-item-dimmed');
                        return;
                    }

                    const allowedCats = stylistMapping[selectedStylist.position] || [];

                    $('.treatment-wrapper').each(function() {
                        const wrapper = $(this);
                        const cat = wrapper.data('category') ? wrapper.data('category').toLowerCase() : '';
                        if (allowedCats.includes(cat)) {
                            wrapper.removeClass('treatment-item-dimmed');
                        } else {
                            wrapper.addClass('treatment-item-dimmed');
                        }
                    });
                }

                $(document).on('click', '.btn-add-cart', function() {
                    const btn = $(this);
                    
                    // Check if parent wrapper is dimmed
                    const wrapper = btn.closest('.treatment-wrapper');
                    if (wrapper.hasClass('treatment-item-dimmed')) {
                        Swal.fire({
                            title: 'Treatment Tidak Sesuai',
                            text: `Layanan ini tidak dapat dipilih karena tidak sesuai dengan keahlian Stylist yang Anda pilih (${ucwords(selectedStylist.position)}).`,
                            icon: 'error',
                            confirmButtonColor: '#EA8290',
                            customClass: {
                                popup: 'rounded-4 border-0 shadow-lg'
                            }
                        });
                        return;
                    }

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
                                Swal.fire({
                                    title: 'Hanya Bisa Memilih Satu Varian',
                                    html: `Layanan <strong>"${detail.treatmentName}"</strong> sudah dipilih varian <strong>"${sameTreatmentDetail.name}"</strong>.<br><br>Anda hanya dapat memilih satu jenis layanan untuk kategori ini.`,
                                    icon: 'info',
                                    confirmButtonColor: '#EA8290',
                                    customClass: {
                                        popup: 'rounded-4 border-0 shadow-lg'
                                    }
                                });
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
                    
                    if (!selectedStylist) {
                        Swal.fire({
                            title: 'Pilih Stylist Dahulu',
                            text: 'Silakan pilih Stylist terlebih dahulu untuk melanjutkan booking.',
                            icon: 'warning',
                            confirmButtonColor: '#EA8290',
                            customClass: {
                                popup: 'rounded-4 border-0 shadow-lg'
                            }
                        });
                        return;
                    }

                    const reservationDate = $('#main_reservation_date').val();
                    if (!reservationDate) {
                        Swal.fire({
                            title: 'Pilih Tanggal Dahulu',
                            text: 'Silakan pilih tanggal reservasi terlebih dahulu.',
                            icon: 'warning',
                            confirmButtonColor: '#EA8290',
                            customClass: {
                                popup: 'rounded-4 border-0 shadow-lg'
                            }
                        });
                        return;
                    }

                    const ids = selectedDetails.map(d => d.id).join(',');
                    // Redirect to select page with multiple IDs, selected stylist_id, and reservation_date
                    window.location.href = "{{ route('booking.select', ['treatmentId' => ':id']) }}".replace(':id', selectedDetails[0].treatmentId) + '?details=' + ids + '&stylist_id=' + selectedStylist.id + '&reservation_date=' + reservationDate;
                });

                // Auto-scroll and highlight logic for treatment_id query parameter
                const urlParams = new URLSearchParams(window.location.search);
                const targetTreatmentId = urlParams.get('treatment_id');
                if (targetTreatmentId) {
                    const targetWrapper = $(`.treatment-wrapper[data-treatment-id="${targetTreatmentId}"]`);
                    if (targetWrapper.length > 0) {
                        // Highlight the card
                        const card = targetWrapper.find('.treatment-card');
                        card.addClass('treatment-card-highlighted');

                        // Automatically expand variants/details collapse if it has one
                        const detailsBtn = targetWrapper.find('[data-bs-toggle="collapse"]');
                        if (detailsBtn.length > 0 && !$(`#details-${targetTreatmentId}`).hasClass('show')) {
                            detailsBtn.trigger('click');
                        }

                        // Scroll to the card smoothly
                        setTimeout(function() {
                            $('html, body').animate({
                                scrollTop: targetWrapper.offset().top - 100
                            }, 800);
                        }, 500);

                        // Remove highlight when interacting with the card
                        targetWrapper.on('click focusin change', function() {
                            card.removeClass('treatment-card-highlighted');
                        });
                    }
                }
            });
        </script>
    @endpush
@endsection