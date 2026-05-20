@extends('layout.dashboard')

@section('title', 'Booking Appointment')

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
        text-truncate: ellipsis;
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
    .stylist-card-modern.active .check-mark {
        display: flex;
    }
    .stylist-card-modern.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        filter: grayscale(0.8);
    }
    .stylist-card-modern.busy .stylist-name::after {
        content: '(Sibuk)';
        color: #dc3545;
        font-size: 0.6rem;
        display: block;
    }

    .stepper-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .step {
        text-align: center;
        position: relative;
        flex: 1;
    }

    .step:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 18px;
        right: -50%;
        width: 100%;
        height: 3px;
        background-color: #e0e0e0;
        z-index: 0;
    }

    .step-circle {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background-color: #e0e0e0;
        color: #fff;
        line-height: 35px;
        margin: 0 auto;
        font-weight: bold;
        position: relative;
        z-index: 1;
    }

    .step.active .step-circle {
        background-color: #0d6efd;
    }

    .step.completed .step-circle {
        background-color: #28a745;
    }

    .step.completed .step-circle::after {
        content: '✔';
    }

    .step-label {
        margin-top: 8px;
        font-size: 13px;
    }

    .extra-small {
        font-size: 0.75rem;
    }

    .cursor-pointer {
        cursor: pointer;
    }

    /* Animation spin untuk loader icon */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .spin {
        animation: spin 1.5s linear infinite;
        display: inline-block;
    }
</style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h4 class="mb-1">📅 Booking Appointment</h4>
                    <small class="text-muted">Ikuti langkah untuk menyelesaikan booking</small>

                    <!-- STEP INDICATOR -->
                    <div class="stepper-wrapper mb-4">
                        <div class="step active" id="step-indicator-1">
                            <div class="step-circle">1</div>
                            <div class="step-label">Data Booking</div>
                        </div>
                        <div class="step" id="step-indicator-2">
                            <div class="step-circle">2</div>
                            <div class="step-label">Ringkasan</div>
                        </div>
                        <div class="step" id="step-indicator-3">
                            <div class="step-circle">3</div>
                            <div class="step-label">Pembayaran</div>
                        </div>
                    </div>
                </div>

                <div class="card-body tab-content">

                    <!-- STEP 1 -->
                    <div class="tab-pane fade show active" id="step1">

                        <!-- INFO TREATMENT -->
                        <!-- VARIANT CHECKLIST (Hanya muncul jika treatment utama punya banyak detail) -->
                        @if($treatment->details->count() > 1 && $preSelectedDetails->isEmpty())
                            <div class="p-3 mb-3 bg-light-warning rounded-3 border border-warning border-opacity-25 shadow-sm">
                                <h6 class="fw-bold mb-3 text-dark"><i class="ti ti-list-check me-1"></i>Pilih Detail Layanan:
                                    <span class="text-primary">{{ $treatment->name }}</span></h6>
                                <p class="small text-muted mb-3">Layanan ini memiliki beberapa pilihan. Silakan pilih
                                    {{ $treatment->allow_multi_select ? 'satu atau lebih' : 'salah satu' }} yang Anda inginkan:
                                </p>
                                <div class="row g-2">
                                    @foreach($treatment->details as $d)
                                        <div class="col-sm-6">
                                            <div class="form-check card-radio p-0 h-100">
                                                <input class="form-check-input d-none primary-detail-checkbox"
                                                    type="{{ $treatment->allow_multi_select ? 'checkbox' : 'radio' }}"
                                                    name="primary_detail" id="detail_{{ $d->id }}" value="{{ $d->id }}"
                                                    data-parent-id="{{ $treatment->id }}"
                                                    data-name="{{ $d->name }}" data-parent-name="{{ $treatment->name }}"
                                                    data-price="{{ $d->price }}" data-price-senior="{{ $d->price_senior }}"
                                                    data-price-junior="{{ $d->price_junior }}"
                                                    data-has-stylist-price="{{ $d->has_stylist_price ? '1' : '0' }}"
                                                    data-is-promo="{{ $treatment->is_promo ? '1' : '0' }}"
                                                    data-promo-type="{{ $treatment->promo_type }}"
                                                    data-promo-value="{{ $treatment->promo_value }}"
                                                    data-is-coloring="{{ (stripos($treatment->category->name ?? '', 'Coloring') !== false) ? '1' : '0' }}"
                                                    data-duration="{{ $d->duration }}"
                                                    data-image="{{ $d->image_url ? (strpos($d->image_url, 'http') === 0 ? $d->image_url : env('SUPABASE_URL') . '/storage/v1/object/public/' . env('SUPABASE_BUCKET') . '/' . $d->image_url) : ( $treatment->image ? (strpos($treatment->image, 'http') === 0 ? $treatment->image : env('SUPABASE_URL') . '/storage/v1/object/public/' . (($treatment->is_promo && env('SUPABASE_PROMO_BUCKET')) ? env('SUPABASE_PROMO_BUCKET') : env('SUPABASE_BUCKET')) . '/' . $treatment->image) : asset('assets/images/no-image.jpg') ) }}"
                                                    onchange="togglePrimaryDetail(this)">
                                                <label class="form-check-label p-2 w-100 border rounded cursor-pointer h-100"
                                                    for="detail_{{ $d->id }}">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span class="fw-bold small">{{ $d->name }}</span>
                                                        <i class="ti ti-circle-check-filled text-success check-icon"
                                                            style="display:none;"></i>
                                                    </div>
                                                    <div class="d-flex justify-content-between">
                                                        <small class="text-muted extra-small">{{ $d->duration }} menit</small>
                                                        <small class="fw-bold text-primary variant-price-container" data-detail-id="{{ $d->id }}">
                                                            @php
                                                                $isPromo = $treatment->is_promo;
                                                                $promoType = $treatment->promo_type;
                                                                $promoValue = $treatment->promo_value;
                                                                $isColoring = (stripos($treatment->category->name ?? '', 'Coloring') !== false);
                                                                $hasLoyalty = Auth::user()->has_coloring_loyalty;
                                                                
                                                                $showStrikethrough = $isPromo || ($isColoring && $hasLoyalty);

                                                                $applyDiscounts = function ($p) use ($isPromo, $promoType, $promoValue, $isColoring, $hasLoyalty) {
                                                                    $final = $p;
                                                                    if ($isPromo) {
                                                                        $pType = strtolower($promoType);
                                                                        if (in_array($pType, ['percentage', 'percent', 'persen']))
                                                                            $final = $p - ($p * $promoValue / 100);
                                                                        else
                                                                            $final = (float) $promoValue;
                                                                    }
                                                                    if ($isColoring && $hasLoyalty) {
                                                                        $final = $final - ($final * 35 / 100);
                                                                    }
                                                                    return $final;
                                                                };
                                                            @endphp

                                                            @if($d->has_stylist_price)
                                                                @php
                                                                    $prices = array_filter([(int) $d->price_senior, (int) $d->price_junior]);
                                                                    $minPrice = count($prices) > 0 ? min($prices) : (int) $d->price;
                                                                    $maxPrice = count($prices) > 0 ? max($prices) : (int) $d->price;

                                                                    $minFinal = $applyDiscounts($minPrice);
                                                                    $maxFinal = $applyDiscounts($maxPrice);
                                                                @endphp
                                                                <span class="strikethrough-part" style="{{ $showStrikethrough ? '' : 'display:none;' }}">
                                                                    <span class="text-muted text-decoration-line-through extra-small me-1">Rp {{ number_format($minPrice, 0) }}</span>
                                                                </span>
                                                                <span class="final-price-part">
                                                                    @if($minFinal != $maxFinal)
                                                                        Rp {{ number_format(max(0, $minFinal), 0) }} - {{ number_format(max(0, $maxFinal), 0) }}
                                                                    @else
                                                                        Rp {{ number_format(max(0, $minFinal), 0) }}
                                                                    @endif
                                                                </span>
                                                            @else
                                                                @php $priceFinal = $applyDiscounts($d->price); @endphp
                                                                <span class="strikethrough-part" style="{{ $showStrikethrough ? '' : 'display:none;' }}">
                                                                    <span class="text-muted text-decoration-line-through extra-small me-1">Rp {{ number_format($d->price, 0) }}</span>
                                                                </span>
                                                                <span class="final-price-part">
                                                                    Rp {{ number_format(max(0, $priceFinal), 0) }}
                                                                </span>
                                                            @endif
                                                        </small>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div id="selectedTreatmentsContainer">
                            {{-- Will be populated by JS --}}
                        </div>



                        <div class="p-3 mb-4 rounded border-start border-primary border-4 bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Total Pembayaran:</span>
                                <h4 class="mb-0 text-primary fw-bold" id="totalPriceDisplay1">Rp 0</h4>
                            </div>
                        </div>

                        <form id="bookingForm">
                            <div class="row">
                                {{-- Hanya muncul jika login sebagai Admin, Owner, atau Karyawan --}}
                                @if($isStaff || strtolower(Auth::user()->role) === 'karyawan')
                                    <div class="col-md-12 mb-4">
                                        <div class="p-4 rounded-4 border bg-white shadow-sm">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <label class="form-label fw-bold mb-0"><i class="ti ti-users me-1"></i>Informasi Pelanggan</label>
                                                <button type="button" class="btn btn-sm btn-light-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#modalCustomerList">
                                                    <i class="ti ti-search me-1"></i>Pilih dari Daftar Pelanggan
                                                </button>
                                            </div>

                                            <div class="form-floating mb-3">
                                                <input type="text" name="customer_name_input" id="customer_name_input" class="form-control bg-light" placeholder="Nama Pelanggan" required>
                                                <label for="customer_name_input">Nama Pelanggan</label>
                                            </div>

                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" id="customer_phone_input" class="form-control bg-light" placeholder="08xxxxxxxx">
                                                        <label for="customer_phone_input">No. WhatsApp</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="email" id="customer_email_input" class="form-control bg-light" placeholder="email@example.com">
                                                        <label for="customer_email_input">Email</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="selected_user_id" id="selected_user_id">
                                            <div id="member_badge" class="mt-2" style="display: none;">
                                                <span class="badge bg-soft-success text-success"><i class="ti ti-medal me-1"></i>Pelanggan Terdaftar</span>
                                                <button type="button" class="btn btn-link btn-sm text-danger p-0 ms-2" onclick="clearSelectedCustomer()">Hapus</button>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @php
                                    $hasStylistPrice = $treatment->details->contains('has_stylist_price', true);
                                @endphp

                                {{-- Hidden placeholder container to prevent JS errors --}}
                                <div id="globalStylistSection" style="display: none !important;">
                                    <div id="global_stylist_grid"></div>
                                </div>
                                @php
                                    $now = \Carbon\Carbon::now();
                                    $cutoff = \Carbon\Carbon::today()->setHour(10)->setMinute(30);
                                    // Jika sudah lewat jam 10:30, minimal booking adalah besok
                                    $initialDate = $now->greaterThan($cutoff) ? \Carbon\Carbon::tomorrow()->toDateString() : \Carbon\Carbon::today()->toDateString();
                                @endphp
                                <!-- TANGGAL -->
                                <div class="col-md-3 mb-3" @if(request()->filled('reservation_date')) style="display: none;" @endif>
                                    <label class="form-label">📅 Tanggal</label>
                                    <input type="date" name="reservation_date" id="reservation_date" class="form-control"
                                        min="{{ $initialDate }}" value="{{ request()->input('reservation_date', $initialDate) }}" required>
                                </div>

                                <!-- JAM -->
                                <div class="{{ request()->filled('reservation_date') ? 'col-md-6' : 'col-md-3' }} mb-3">
                                    <label class="form-label">⏰ Jam Reservasi</label>
                                    <select name="reservation_time" id="reservation_time" class="form-select" required>
                                        <option value="">-- Pilih Jam --</option>
                                    </select>
                                    <small class="text-muted extra-small">Batas reservasi: 09:00 - 10:30</small>
                                </div>

                            </div>
                        </form>
                    </div>

                    <!-- STEP 2 -->
                    <div class="tab-pane fade" id="step2">
                        <div class="p-3 rounded bg-light">
                            <h5 class="mb-3">📋 Ringkasan Booking</h5>

                            <p class="mb-1"><strong>Customer:</strong> <span id="summaryCustomer">{{ Auth::user()->name }}</span></p>
                            <p class="mb-1"><strong>No. HP:</strong> <span id="summaryPhone">{{ Auth::user()->phone ?? '-' }}</span></p>
                            <p class="mb-3"><strong>Email:</strong> <span id="summaryEmail">{{ Auth::user()->email ?? '-' }}</span></p>

                            <div id="summaryTreatments">
                                <!-- Will be populated by JS -->
                            </div>

                            <p class="mt-3"><strong>Stylist:</strong> <span id="summaryStylist"></span></p>
                            <p><strong>Waktu:</strong> <span id="summaryDatetime"></span></p>

                            <hr>
                            <h5>Total: <span id="totalPriceDisplay2">Rp
                                    {{ number_format($treatment->details->sum('price')) }}</span></h5>
                        </div>
                    </div>

                    <!-- STEP 3 -->
                    <div class="tab-pane fade" id="step3">
                        <div class="p-3 rounded bg-light">
                            <h5 class="mb-3">💳 Pembayaran</h5>

                            <form method="POST" action="{{ route('booking.store') }}" id="finalBookingForm">
                                @csrf

                                <div id="paymentTreatmentInputs">
                                    {{-- Will be populated by JS with hidden inputs for treatment_detail_ids[] and
                                    stylist_ids[] --}}
                                </div>
                                <input type="hidden" name="customer_name" id="paymentCustomerName"
                                    value="{{ Auth::user()->name }}">
                                <input type="hidden" name="reservation_date" id="paymentDate">
                                <input type="hidden" name="reservation_time" id="paymentTime">
                                <input type="hidden" name="customer_email" id="paymentCustomerEmail" value="{{ Auth::user()->email }}">
                                <input type="hidden" name="customer_phone" id="paymentCustomerPhone" value="{{ Auth::user()->phone }}">
                                <input type="hidden" name="selected_user_id" id="paymentSelectedUserId" value="{{ Auth::user()->role === 'pelanggan' ? Auth::user()->id : '' }}">

                                <div class="mb-3">
                                    <label class="form-label">Metode Pembayaran</label>
                                    <select name="payment_method" class="form-select" required>
                                        <option value="">-- Pilih Metode --</option>
                                        @if(in_array(strtolower(Auth::user()->role), ['owner', 'admin', 'karyawan']))
                                            <option value="Tunai">Tunai</option>
                                        @endif
                                        <option value="Transfer">Transfer Bank (Midtrans)</option>
                                        <option value="QRIS">QRIS / E-Wallet (Midtrans)</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-success w-100">
                                    ✅ Bayar & Konfirmasi
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- NAV BUTTON -->
                    <div class="d-flex justify-content-between mt-4">
                        <button class="btn btn-light" id="prevStep">← Kembali</button>
                        <button class="btn btn-primary" id="nextStep">Lanjut →</button>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- JS -->


    <!-- Modal Pilih Treatment -->
    <div class="modal fade" id="modalAddTreatment" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title text-white fw-bold"><i class="ti ti-layout-grid me-2"></i>Katalog Treatment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <!-- TOP BAR: CATEGORY & SEARCH -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-5">
                            <div class="input-group bg-white rounded-3 shadow-sm">
                                <span class="input-group-text border-0 bg-transparent text-muted"><i
                                        class="ti ti-search"></i></span>
                                <input type="text" id="searchTreatment" class="form-control border-0 bg-transparent py-2"
                                    placeholder="Cari layanan...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select id="modalCategoryFilter" class="form-select border-0 shadow-sm py-2">
                                <option value="all">Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- TREATMENT GRID -->
                    <div class="row g-4" id="treatmentList">
                        @foreach($allTreatments as $item)
                            @php
                                $imageUrl = asset('assets/images/no-image.jpg');
                                $hasImage = false;

                                // 1. Cek gambar utama treatment
                                if ($item->image) {
                                    if (strpos($item->image, 'http') === 0) {
                                        $imageUrl = $item->image;
                                        $hasImage = true;
                                    } else {
                                        $bucket = ($item->is_promo && env('SUPABASE_PROMO_BUCKET')) ? env('SUPABASE_PROMO_BUCKET') : env('SUPABASE_BUCKET');
                                        $imageUrl = env('SUPABASE_URL') . '/storage/v1/object/public/' . $bucket . '/' . $item->image;
                                        $hasImage = true;
                                    }
                                }

                                // 2. Jika gambar utama kosong, coba cari dari detail/variasi
                                if (!$hasImage) {
                                    $firstDetail = $item->details->first();
                                    if ($firstDetail && $firstDetail->image_url) {
                                        if (strpos($firstDetail->image_url, 'http') === 0) {
                                            $imageUrl = $firstDetail->image_url;
                                        } else {
                                            $imageUrl = env('SUPABASE_URL') . '/storage/v1/object/public/' . env('SUPABASE_BUCKET') . '/' . $firstDetail->image_url;
                                        }
                                        $hasImage = true;
                                    }
                                }
                            @endphp
                            <div class="col-md-4 col-lg-3 treatment-item-container" data-category="{{ $item->category_id }}"
                                data-name="{{ strtolower($item->name) }}">
                                <div class="card h-100 border-0 shadow-sm treatment-card-modal">
                                    <div class="position-relative overflow-hidden" style="height: 180px;">
                                        <img src="{{ $imageUrl }}" class="card-img-top w-100 h-100 object-fit-cover"
                                            alt="{{ $item->name }}">
                                        <span
                                            class="badge bg-dark bg-opacity-75 position-absolute top-0 end-0 m-2 small">{{ $item->category->name ?? 'Service' }}</span>
                                    </div>
                                    <div class="card-body p-3">
                                        <h6 class="fw-bold mb-3 text-truncate">{{ $item->name }}</h6>
                                        <div class="detail-selection-list">
                                            @foreach($item->details as $d)
                                                <div
                                                    class="detail-item-box p-2 mb-2 rounded border bg-white d-flex justify-content-between align-items-center">
                                                    <div style="max-width: 65%;">
                                                        <div class="small fw-bold lh-sm">{{ $d->name }}</div>
                                                        <small class="text-muted extra-small">{{ $d->duration }} menit</small>
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="small fw-bold text-primary mb-1">
                                                            @php
                                                                $isItemPromo = $item->is_promo;
                                                                $itemPromoType = $item->promo_type;
                                                                $itemPromoValue = $item->promo_value;

                                                                $applyItemPromo = function ($p) use ($isItemPromo, $itemPromoType, $itemPromoValue) {
                                                                    if (!$isItemPromo)
                                                                        return $p;
                                                                    if ($itemPromoType === 'percentage' || $itemPromoType === 'percent')
                                                                        return $p - ($p * $itemPromoValue / 100);
                                                                    return $p - $itemPromoValue;
                                                                };
                                                            @endphp

                                                            @if($d->has_stylist_price)
                                                                @php
                                                                    $iPrices = array_filter([(int) $d->price_senior, (int) $d->price_junior]);
                                                                    $iMin = count($iPrices) > 0 ? min($iPrices) : (int) $d->price;
                                                                    $iMax = count($iPrices) > 0 ? max($iPrices) : (int) $d->price;

                                                                    $iMinPromo = $applyItemPromo($iMin);
                                                                    $iMaxPromo = $applyItemPromo($iMax);
                                                                @endphp
                                                                @if($isItemPromo)
                                                                    <span class="text-muted text-decoration-line-through extra-small me-1">Rp {{ number_format($iMin, 0) }}</span>
                                                                @endif
                                                                @if($iMinPromo != $iMaxPromo)
                                                                    Rp {{ number_format(max(0, $iMinPromo), 0) }} - {{ number_format(max(0, $iMaxPromo), 0) }}
                                                                @else
                                                                    Rp {{ number_format(max(0, $iMinPromo), 0) }}
                                                                @endif
                                                            @else
                                                                @php $iPriceP = $applyItemPromo($d->price); @endphp
                                                                @if($isItemPromo)
                                                                    <span class="text-muted text-decoration-line-through extra-small me-1">Rp {{ number_format($d->price, 0) }}</span>
                                                                @endif
                                                                Rp {{ number_format(max(0, $iPriceP), 0) }}
                                                            @endif
                                                        </div>
                                                        <button type="button" class="btn btn-primary btn-xs add-detail-btn"
                                                            data-id="{{ $d->id }}" 
                                                            data-parent-id="{{ $item->id }}"
                                                            data-name="{{ $d->name }}"
                                                            data-parent-name="{{ $item->name }}" data-price="{{ $d->price }}"
                                                            data-price-senior="{{ $d->price_senior }}"
                                                            data-price-junior="{{ $d->price_junior }}"
                                                            data-has-stylist-price="{{ $d->has_stylist_price ? '1' : '0' }}"
                                                            data-is-promo="{{ $item->is_promo ? '1' : '0' }}"
                                                            data-promo-type="{{ $item->promo_type }}"
                                                            data-promo-value="{{ $item->promo_value }}"
                                                            data-is-coloring="{{ (stripos($item->category->name ?? '', 'Coloring') !== false) ? '1' : '0' }}"
                                                            data-duration="{{ $d->duration }}"
                                                            data-image="{{ $imageUrl }}">
                                                            Pilih
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($isStaff)
        <!-- MODAL DAFTAR PELANGGAN -->
        <div class="modal fade" id="modalCustomerList" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title text-white fw-bold"><i class="ti ti-users me-2"></i>Daftar Pelanggan</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="p-3 bg-light border-bottom">
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-white border-end-0"><i class="ti ti-search text-muted"></i></span>
                                <input type="text" id="customerSearchInput" class="form-control border-start-0 ps-0" placeholder="Cari nama, email, atau no handphone...">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Nama Pelanggan</th>
                                        <th>Kontak</th>
                                        <th class="text-end pe-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="customerTableBody">
                                    @forelse($customers as $c)
                                        <tr class="customer-row" data-search="{{ strtolower($c->name . ' ' . $c->email . ' ' . $c->phone) }}">
                                            <td class="ps-3">
                                                <div class="fw-bold text-dark">{{ $c->name }}</div>
                                                <div class="small text-muted">
                                                    @if(isset($c->status) && $c->status === 'guest')
                                                        <span class="badge bg-secondary opacity-50 px-2 rounded-pill">Guest</span>
                                                    @else
                                                        ID: #{{ $c->id }}
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="small"><i class="ti ti-mail me-1"></i>{{ $c->email ?? '-' }}</div>
                                                <div class="small"><i class="ti ti-brand-whatsapp me-1"></i>{{ $c->phone ?? '-' }}</div>
                                            </td>
                                            <td class="text-end pe-3">
                                                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" 
                                                    onclick="selectCustomerFromModal({{ $c->id }}, '{{ addslashes($c->name) }}', '{{ $c->phone }}', '{{ $c->email }}')">
                                                    Pilih
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">Belum ada pelanggan terdaftar.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL KONFIRMASI AKHIR -->
    <div class="modal fade" id="modalConfirmBooking" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white fw-bold"><i class="ti ti-checkbox me-2"></i>Konfirmasi Pesanan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted">Apakah data booking Anda sudah benar?</p>
                    <div class="list-group list-group-flush border rounded mb-3">
                        <div class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Metode Bayar:</span>
                            <span class="fw-bold" id="confirmPaymentMethod"></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Total Pembayaran:</span>
                            <span class="fw-bold text-success" id="confirmTotal"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cek Lagi</button>
                    <button type="button" class="btn btn-success px-4" id="btnFinalConfirm">Ya, Selesaikan Booking</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL PROSES -->
    <div class="modal fade" id="modalProses" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-body text-center py-4" id="modalStatusContent">
                    <div class="mb-3" id="modalStatusIconContainer">
                        <i class="ti ti-loader text-primary spin" style="font-size: 3rem;"></i>
                    </div>
                    <h4 id="modalStatusTitle">Booking sedang diproses</h4>
                    <p id="modalStatusDesc" class="text-muted">Terima kasih telah melakukan booking. Silakan klik tombol di
                        bawah untuk kembali.</p>
                    <div id="modalStatusAction">
                        <a href="{{ route('dashboard') }}" class="btn btn-primary px-4">Kembali ke Dashboard</a>
                    </div>
                </div>
            </div>
     @push('scripts')
    <!-- Midtrans Snap JS -->
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        window.lastCreatedBookingId = null;
        const allStylists = @json($stylists);
        // Map avatars separately since we have an accessor but Laravel json encode might not include it by default
        allStylists.forEach(s => {
            s.avatar_url = "{{ asset('assets/images/user/avatar-2.jpg') }}"; // Initial fallback
        });

        // Re-map with actual calculated URLs from PHP to be safe
        const stylistAvatars = {
            @foreach($stylists as $s)
                "{{ $s->id }}": "{{ $s->avatar_url }}",
            @endforeach
        };
        allStylists.forEach(s => {
            s.avatar_url = stylistAvatars[s.id];
        });

        // Initialize variables
        const isStaff = @json($isStaff);
        const customers = @json($customers);
        let hasColoringLoyalty = {{ Auth::user()->has_coloring_loyalty ? 'true' : 'false' }};

        // Logic for Customer Selection Modal (Staff Only)
        if (isStaff) {
            const searchInput = document.getElementById('customerSearchInput');
            const rows = document.querySelectorAll('.customer-row');
            const nameInput = document.getElementById('customer_name_input');
            const phoneInput = document.getElementById('customer_phone_input');
            const emailInput = document.getElementById('customer_email_input');
            const userIdInput = document.getElementById('selected_user_id');
            const memberBadge = document.getElementById('member_badge');
            const customerModalEl = document.getElementById('modalCustomerList');
            const customerModal = customerModalEl ? new bootstrap.Modal(customerModalEl) : null;

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const q = this.value.toLowerCase();
                    rows.forEach(row => {
                        row.style.display = row.getAttribute('data-search').includes(q) ? '' : 'none';
                    });
                });
            }

            window.selectCustomerFromModal = function(id, name, phone, email) {
                nameInput.value = name;
                phoneInput.value = phone || '';
                emailInput.value = email || '';
                userIdInput.value = id;
                if (memberBadge) memberBadge.style.display = 'block';

                // Update Loyalty Status dynamically for Staff booking
                const selectedCustomer = customers.find(c => c.id == id);
                if (selectedCustomer) {
                    hasColoringLoyalty = selectedCustomer.has_coloring_loyalty;
                    if (memberBadge) {
                        if (selectedCustomer.status === 'guest') {
                            memberBadge.innerHTML = '<span class="badge bg-secondary opacity-50 text-white"><i class="ti ti-user me-1"></i>Guest</span><button type="button" class="btn btn-link btn-sm text-danger p-0 ms-2" onclick="clearSelectedCustomer()">Hapus</button>';
                        } else {
                            memberBadge.innerHTML = '<span class="badge bg-soft-success text-success"><i class="ti ti-medal me-1"></i>Pelanggan Terdaftar</span><button type="button" class="btn btn-link btn-sm text-danger p-0 ms-2" onclick="clearSelectedCustomer()">Hapus</button>';
                        }
                    }
                    updateVariantLabels(); // Update selection grid labels
                    renderSelectedTreatments(); // Recalculate prices for selected list
                }

                // Close modal safely
                const modalEl = document.getElementById('modalCustomerList');
                if (modalEl) {
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) {
                        modalInstance.hide();
                    } else {
                        // Fallback if instance not found
                        $(modalEl).modal('hide');
                    }

                    // Force remove backdrop if it gets stuck (common BS5 issue)
                    setTimeout(() => {
                        if (document.querySelector('.modal-backdrop')) {
                            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                            document.body.classList.remove('modal-open');
                            document.body.style.overflow = '';
                            document.body.style.paddingRight = '';
                        }
                    }, 400);
                }
            };

            window.clearSelectedCustomer = function() {
                nameInput.value = '';
                phoneInput.value = '';
                emailInput.value = '';
                userIdInput.value = '';
                if (memberBadge) memberBadge.style.display = 'none';

                // Reset to logged-in user loyalty status
                hasColoringLoyalty = {{ Auth::user()->has_coloring_loyalty ? 'true' : 'false' }};
                updateVariantLabels();
                renderSelectedTreatments();
            };
        }

        // New function to update prices in the variant grid
        function updateVariantLabels() {
            document.querySelectorAll('.variant-price-container').forEach(container => {
                const detailId = container.getAttribute('data-detail-id');
                const checkbox = document.getElementById('detail_' + detailId);
                if (!checkbox) return;

                // Extract data from checkbox
                const data = {
                    price: parseInt(checkbox.getAttribute('data-price')),
                    priceSenior: parseInt(checkbox.getAttribute('data-price-senior') || checkbox.getAttribute('data-price')),
                    priceJunior: parseInt(checkbox.getAttribute('data-price-junior') || checkbox.getAttribute('data-price')),
                    hasStylistPrice: checkbox.getAttribute('data-has-stylist-price') === '1',
                    isPromo: checkbox.getAttribute('data-is-promo') === '1',
                    promoType: checkbox.getAttribute('data-promo-type'),
                    promoValue: parseInt(checkbox.getAttribute('data-promo-value') || 0),
                    isColoring: checkbox.getAttribute('data-is-coloring') === '1'
                };

                const showStrikethrough = data.isPromo || (hasColoringLoyalty && data.isColoring);
                
                // Update Strikethrough visibility
                const strikethroughPart = container.querySelector('.strikethrough-part');
                if (strikethroughPart) {
                    strikethroughPart.style.display = showStrikethrough ? '' : 'none';
                }

                // Calculate final price (we use min price for display in ranges)
                const applyLocalDiscounts = (p) => {
                    let final = p;
                    if (data.isPromo) {
                        if (data.promoType === 'percentage' || data.promoType === 'percent')
                            final = p - (p * data.promoValue / 100);
                        else
                            final = p - data.promoValue;
                    }
                    if (hasColoringLoyalty && data.isColoring) {
                        final = final - (final * 35 / 100);
                    }
                    return Math.max(0, final);
                };

                const finalPricePart = container.querySelector('.final-price-part');
                if (finalPricePart) {
                    if (data.hasStylistPrice) {
                        const minFinal = applyLocalDiscounts(data.priceJunior < data.priceSenior ? data.priceJunior : data.priceSenior);
                        const maxFinal = applyLocalDiscounts(data.priceJunior > data.priceSenior ? data.priceJunior : data.priceSenior);
                        
                        if (minFinal !== maxFinal) {
                            finalPricePart.innerText = `Rp ${new Intl.NumberFormat('id-ID').format(minFinal)} - ${new Intl.NumberFormat('id-ID').format(maxFinal)}`;
                        } else {
                            finalPricePart.innerText = `Rp ${new Intl.NumberFormat('id-ID').format(minFinal)}`;
                        }
                    } else {
                        const final = applyLocalDiscounts(data.price);
                        finalPricePart.innerText = `Rp ${new Intl.NumberFormat('id-ID').format(final)}`;
                    }
                }
            });
        }

        window.updateCustomPrice = function(detailId, val) {
            const detail = selectedDetails.find(d => d.id == detailId);
            if (detail) {
                detail.customPrice = val ? parseInt(val) : undefined;
                renderSelectedTreatments();
            }
        };

        // Initialize min date & time logic
        function initTimeSelection() {
            const dateInput = document.getElementById('reservation_date');
            const timeSelect = document.getElementById('reservation_time');

            // Tanggal Libur dari Backend
            const holidayDates = {!! json_encode($holidays) !!};

            // 1. Flatpickr Logic
            const initialDateVal = dateInput.value || 'today';
            const fp = flatpickr(dateInput, {
                locale: 'id',
                dateFormat: 'Y-m-d',
                minDate: 'today',
                disable: holidayDates,
                defaultDate: initialDateVal,
                onChange: function(selectedDates, dateStr) {
                    updateTimeSlots();
                    checkStylistAvailability();
                }
            });

            // If today is > 18:00 or a holiday, find next available date
            function findNextAvailable() {
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('reservation_date')) return; // Skip if pre-selected in previous step

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
            findNextAvailable();

            // 2. Generate Time Slots
            function updateTimeSlots() {
                const selectedDate = dateInput.value;
                const now = new Date();

                // Perbandingan tanggal lokal yang lebih akurat
                const selectedDateObj = new Date(selectedDate);
                const isToday = now.toDateString() === selectedDateObj.toDateString();

                timeSelect.innerHTML = '<option value="">-- Pilih Jam --</option>';

                for (let h = 9; h <= 10; h++) {
                    for (let m = 0; m < 60; m += 15) {
                        // Batasi jam booking maksimal jam 10:30 (Permintaan Client)
                        if (h === 10 && m > 30) break;

                        const timeVal = `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;

                        // Jika tanggal yang dipilih adalah hari ini, sembunyikan jam yang sudah lewat
                        if (isToday) {
                            if (h < now.getHours() || (h === now.getHours() && m <= now.getMinutes())) {
                                continue;
                            }
                        }

                        const option = document.createElement('option');
                        option.value = timeVal;
                        option.textContent = timeVal;
                        timeSelect.appendChild(option);
                    }
                }

                // Pastikan select tidak disabled
                timeSelect.disabled = false;
            }

            // Jalankan pertama kali
            updateTimeSlots();
        }
        initTimeSelection();

        let currentStep = 1;
        const totalSteps = 3;

        const nextBtn = document.getElementById('nextStep');
        const prevBtn = document.getElementById('prevStep');

        // MULTIPLE TREATMENTS LOGIC (V3: Choice-based Primary)
        let selectedDetails = [
            @foreach($preSelectedDetails as $d)
                {
                    id: {{ $d->id }},
                    parentId: {{ $d->treatment_id }},
                    name: {!! json_encode($d->name) !!},
                    parentName: {!! json_encode($d->treatment->name) !!},
                    price: {{ (int) $d->price }},
                    priceSenior: {{ (int) ($d->price_senior ?: $d->price) }},
                    priceJunior: {{ (int) ($d->price_junior ?: $d->price) }},
                    hasStylistPrice: {{ $d->has_stylist_price ? 'true' : 'false' }},
                    duration: {{ (int) $d->duration }},
                    isPrimary: {{ $d->treatment_id == $treatment->id ? 'true' : 'false' }},
                    isPromo: {{ $d->treatment->is_promo ? 'true' : 'false' }},
                    promoType: {!! json_encode($d->treatment->promo_type) !!},
                    promoValue: {{ (int) $d->treatment->promo_value }},
                    isColoring: {{ (stripos($d->treatment->category->name ?? '', 'Coloring') !== false) ? 'true' : 'false' }},
                    @php
                        $initImg = asset('assets/images/no-image.jpg');
                        $hasInitImg = false;
                        if ($d->treatment->image) {
                            if (strpos($d->treatment->image, 'http') === 0) { $initImg = $d->treatment->image; $hasInitImg = true; }
                            else { 
                                $bucket = ($d->treatment->is_promo && env('SUPABASE_PROMO_BUCKET')) ? env('SUPABASE_PROMO_BUCKET') : env('SUPABASE_BUCKET');
                                $initImg = env('SUPABASE_URL') . '/storage/v1/object/public/' . $bucket . '/' . $d->treatment->image; 
                                $hasInitImg = true;
                            }
                        }
                        if (!$hasInitImg && $d->image_url) {
                            if (strpos($d->image_url, 'http') === 0) { $initImg = $d->image_url; }
                            else { $initImg = env('SUPABASE_URL') . '/storage/v1/object/public/' . env('SUPABASE_BUCKET') . '/' . $d->image_url; }
                        }
                    @endphp
                    image: {!! json_encode($initImg) !!},
                    stylistId: {{ request()->query('stylist_id') ? (int) request()->query('stylist_id') : 'null' }},
                    stylistKategori: {!! request()->query('stylist_id') && ($preSelStylist = \App\Models\User::find(request()->query('stylist_id'))) ? json_encode(strtolower($preSelStylist->kategori)) : 'null' !!}
                },
            @endforeach
            @if($preSelectedDetails->isEmpty() && $treatment->details->count() === 1)
                @foreach($treatment->details as $d)
                    {
                        id: {{ $d->id }},
                        parentId: {{ $treatment->id }},
                        name: {!! json_encode($d->name) !!},
                        parentName: {!! json_encode($treatment->name) !!},
                        price: {{ (int) $d->price }},
                        priceSenior: {{ (int) ($d->price_senior ?: $d->price) }},
                        priceJunior: {{ (int) ($d->price_junior ?: $d->price) }},
                        hasStylistPrice: {{ $d->has_stylist_price ? 'true' : 'false' }},
                        duration: {{ (int) $d->duration }},
                        isPrimary: true,
                        isPromo: {{ $treatment->is_promo ? 'true' : 'false' }},
                        promoType: {!! json_encode($treatment->promo_type) !!},
                        promoValue: {{ (int) $treatment->promo_value }},
                        isColoring: {{ (stripos($treatment->category->name ?? '', 'Coloring') !== false) ? 'true' : 'false' }},
                        @php
                            $initImg = asset('assets/images/no-image.jpg');
                            $hasInitImg = false;
                            if ($treatment->image) {
                                if (strpos($treatment->image, 'http') === 0) { $initImg = $treatment->image; $hasInitImg = true; }
                                else { 
                                    $bucket = ($treatment->is_promo && env('SUPABASE_PROMO_BUCKET')) ? env('SUPABASE_PROMO_BUCKET') : env('SUPABASE_BUCKET');
                                    $initImg = env('SUPABASE_URL') . '/storage/v1/object/public/' . $bucket . '/' . $treatment->image; 
                                    $hasInitImg = true;
                                }
                            }
                            if (!$hasInitImg && $d->image_url) {
                                if (strpos($d->image_url, 'http') === 0) { $initImg = $d->image_url; }
                                else { $initImg = env('SUPABASE_URL') . '/storage/v1/object/public/' . env('SUPABASE_BUCKET') . '/' . $d->image_url; }
                            }
                        @endphp
                        image: {!! json_encode($initImg) !!},
                        stylistId: {{ request()->query('stylist_id') ? (int) request()->query('stylist_id') : 'null' }},
                        stylistKategori: {!! request()->query('stylist_id') && ($preSelStylist = \App\Models\User::find(request()->query('stylist_id'))) ? json_encode(strtolower($preSelStylist->kategori)) : 'null' !!}
                    },
                @endforeach
            @endif
        ];

        // Mark pre-selected items as checked in the variant list
        document.addEventListener('DOMContentLoaded', function() {
            selectedDetails.forEach(d => {
                const checkbox = document.getElementById(`detail_${d.id}`);
                if (checkbox) {
                    checkbox.checked = true;
                    // Trigger label update if any
                    const label = checkbox.nextElementSibling;
                    if (label && label.tagName === 'LABEL') {
                        label.classList.add('bg-light-primary', 'border-primary', 'shadow-sm');
                        const icon = label.querySelector('.check-icon');
                        if (icon) icon.style.display = 'block';
                    }
                }
            });
            renderSelectedTreatments();
        });

        window.togglePrimaryDetail = function (checkbox) {
            resetLastCreatedBookingId();
            const isMulti = {{ $treatment->allow_multi_select ? 'true' : 'false' }};
            const id = parseInt(checkbox.value);

            if (!isMulti) {
                // If single selection, remove other primary details first
                selectedDetails = selectedDetails.filter(d => !d.isPrimary);
                // Reset other visual states (border/icons) for variants
                document.querySelectorAll('.primary-detail-checkbox').forEach(cb => {
                    const lbl = cb.nextElementSibling;
                    const icn = lbl.querySelector('.check-icon');
                    lbl.classList.remove('bg-white', 'border-primary', 'shadow-sm');
                    if (icn) icn.style.display = 'none';
                });
            }

            const label = checkbox.nextElementSibling;
            const icon = label.querySelector('.check-icon');

            if (checkbox.checked) {
                label.classList.add('bg-white', 'border-primary', 'shadow-sm');
                if (icon) icon.style.display = 'block';

                if (!selectedDetails.some(d => d.id === id)) {
                    selectedDetails.push({
                        id: id,
                        parentId: parseInt(checkbox.getAttribute('data-parent-id')),
                        name: checkbox.getAttribute('data-name'),
                        parentName: checkbox.getAttribute('data-parent-name'),
                        price: parseInt(checkbox.getAttribute('data-price')),
                        priceSenior: parseInt(checkbox.getAttribute('data-price-senior') || checkbox.getAttribute('data-price')),
                        priceJunior: parseInt(checkbox.getAttribute('data-price-junior') || checkbox.getAttribute('data-price')),
                        hasStylistPrice: checkbox.getAttribute('data-has-stylist-price') === '1',
                        duration: parseInt(checkbox.getAttribute('data-duration')),
                        isPrimary: true,
                        isPromo: checkbox.getAttribute('data-is-promo') === '1',
                        promoType: checkbox.getAttribute('data-promo-type'),
                        promoValue: parseInt(checkbox.getAttribute('data-promo-value') || 0),
                        isColoring: checkbox.getAttribute('data-is-coloring') === '1',
                        image: checkbox.getAttribute('data-image')
                    });
                }
            } else {
                label.classList.remove('bg-white', 'border-primary', 'shadow-sm');
                if (icon) icon.style.display = 'none';
                selectedDetails = selectedDetails.filter(d => d.id !== id);
            }
            renderSelectedTreatments();
            checkStylistAvailability();
        };

        function renderSelectedTreatments() {
            const container = document.getElementById('selectedTreatmentsContainer');
            if (!container) return;
            container.innerHTML = '';

            let total = 0;
            let hiddenInputs = '';

            selectedDetails.forEach((d, index) => {
                // Gunakan harga kustom jika ada, jika tidak gunakan harga kalkulasi standar
                const basePrice = calculateDetailPrice(d);
                const currentPrice = d.customPrice !== undefined ? d.customPrice : basePrice;

                total += currentPrice;
                hiddenInputs += `<input type="hidden" name="treatment_detail_ids[]" value="${d.id}">`;
                hiddenInputs += `<input type="hidden" name="stylist_ids[]" value="${d.stylistId || ''}">`;
                hiddenInputs += `<input type="hidden" name="custom_prices[]" value="${d.customPrice !== undefined ? d.customPrice : ''}">`;

                const itemHtml = `
                        <div class="p-3 mb-3 rounded border-start border-3 border-primary bg-white shadow-sm">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-start">
                                    <div class="me-3 position-relative" style="width: 60px; height: 60px;">
                                        <img src="${d.image || '{{ asset('assets/images/no-image.jpg') }}'}" 
                                             class="rounded shadow-sm w-100 h-100 object-fit-cover border" 
                                             alt="${d.name}">
                                    </div>
                                    <div>
                                        <div class="small text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">${d.parentName}</div>
                                        <div class="fw-bold text-dark">${d.name} <small class="text-muted fw-normal">(${d.duration} mnt)</small></div>
                                    @if($isStaff)
                                        <div class="input-group input-group-sm mt-1" style="max-width: 150px;" onclick="event.stopPropagation()">
                                            <span class="input-group-text bg-light">Rp</span>
                                            <input type="number" class="form-control" value="${currentPrice}" 
                                                   onclick="event.stopPropagation()"
                                                   onchange="updateCustomPrice(${d.id}, this.value)" 
                                                   placeholder="Harga">
                                        </div>
                                    @else
                                        <div class="text-primary small fw-semibold">
                                            ${(hasColoringLoyalty && d.isColoring) || d.isPromo ? `<span class="text-muted text-decoration-line-through me-1" style="font-size: 0.7rem;">Rp ${new Intl.NumberFormat('id-ID').format(getOriginalDetailPrice(d))}</span>` : ''}
                                            Rp ${new Intl.NumberFormat('id-ID').format(currentPrice)}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <button type="button" class="btn btn-icon btn-link-danger btn-sm" onclick="removeDetail(${d.id})"><i class="ti ti-trash"></i></button>
                                </div>
                            </div>
                            ${d.hasStylistPrice ? '' : ''}
                        </div>
                    `;
                container.insertAdjacentHTML('beforeend', itemHtml);
            });

            const formattedTotal = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
            document.getElementById('totalPriceDisplay1').innerText = formattedTotal;
            document.getElementById('totalPriceDisplay2').innerText = formattedTotal;
            document.getElementById('paymentTreatmentInputs').innerHTML = hiddenInputs;

            // The global stylist section is kept hidden as the stylist is pre-selected in Step 1
            const globalSection = document.getElementById('globalStylistSection');
            if (globalSection) {
                globalSection.style.display = 'none';
            }

            // Apply busy states if we have them
            applyBusyStylists();
        }

        let busyStylistsMap = {};
        let offWorkStylists = [];

        window.checkStylistAvailability = function () {
            const date = document.getElementById('reservation_date').value;
            const time = document.getElementById('reservation_time').value;

            if (!date || selectedDetails.length === 0) return;

            $.ajax({
                url: "{{ route('booking.check_stylist_availability') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    reservation_date: date,
                    reservation_time: time,
                    selected_details: selectedDetails.map(d => ({id: d.id}))
                },
                success: function (response) {
                    if (response.is_holiday) {
                        alert(response.message || 'Salon tutup pada tanggal ini.');
                        busyStylistsMap = {};
                        applyBusyStylists(); // Clear current
                        return;
                    }
                    busyStylistsMap = response.conflicts;
                    offWorkStylists = response.off_work_ids || [];
                    applyBusyStylists();
                }
            });
        };

        function applyBusyStylists() {
            const globalGrid = document.getElementById('global_stylist_grid');
            if (!globalGrid) return;

            // Collect all busy IDs across all selected treatments
            const allBusyIds = new Set();
            Object.values(busyStylistsMap).forEach(busyIds => {
                busyIds.forEach(id => allBusyIds.add(id));
            });

            const cards = globalGrid.querySelectorAll('.stylist-card-modern');
            cards.forEach(card => {
                const sid = parseInt(card.getAttribute('data-stylist-id'));
                if (!sid) return; // Skip "Reset" card

                const isOff = offWorkStylists.includes(sid);
                const isBusy = allBusyIds.has(sid);

                card.classList.remove('busy', 'disabled');
                card.style.display = isOff ? 'none' : '';
                
                if (!isOff && isBusy) {
                    card.classList.add('busy', 'disabled');
                }

                // If currently selected stylist becomes unavailable, reset global selection
                const currentStylistId = selectedDetails.length > 0 ? selectedDetails[0].stylistId : null;
                if (currentStylistId == sid && (isOff || isBusy)) {
                    const resetCard = globalGrid.querySelector('[data-stylist-id=""]');
                    if (resetCard) {
                        // Manually trigger reset state
                        globalGrid.querySelectorAll('.stylist-card-modern').forEach(c => c.classList.remove('active'));
                        resetCard.classList.add('active');
                        selectedDetails.forEach(d => {
                            d.stylistId = null;
                            d.stylistKategori = null;
                        });
                        renderSelectedTreatments();
                    }
                }
            });
        }

        window.updateItemStylistCards = function (detailId, stylistId, element) {
            resetLastCreatedBookingId();
            const item = selectedDetails.find(d => d.id === detailId);
            if (!item) return;

            // Check if card is disabled (busy)
            if (element.classList.contains('disabled')) {
                alert('Mohon maaf, stylist ini sudah memiliki jadwal pada jam tersebut.');
                return;
            }

            // Toggle logic
            if (item.stylistId == stylistId) {
                item.stylistId = null;
                item.stylistKategori = null;
                element.classList.remove('active');
            } else {
                item.stylistId = stylistId;
                item.stylistKategori = element.getAttribute('data-kategori');
                // Remove active from peers
                element.parentElement.querySelectorAll('.stylist-card-modern').forEach(c => c.classList.remove('active'));
                element.classList.add('active');
            }
            renderSelectedTreatments();
        };

        function getOriginalDetailPrice(d) {
            let originalPrice = d.price;
            if (d.hasStylistPrice && d.stylistKategori) {
                if (d.stylistKategori === 'senior') originalPrice = d.priceSenior;
                else if (d.stylistKategori === 'junior') originalPrice = d.priceJunior;
            }
            return originalPrice;
        }

        function calculateDetailPrice(d) {
            let finalPrice = getOriginalDetailPrice(d);
            
            // Apply Promo (Manual/Bundling)
            if (d.isPromo) {
                const pType = (d.promoType || '').toLowerCase();
                if (pType === 'percentage' || pType === 'percent' || pType === 'persen') {
                    finalPrice = finalPrice - (finalPrice * d.promoValue / 100);
                } else {
                    finalPrice = d.promoValue;
                }
            }

            // Apply Coloring Loyalty (35%)
            if (hasColoringLoyalty && d.isColoring) {
                finalPrice = finalPrice - (finalPrice * 35 / 100);
            }

            return Math.max(0, finalPrice);
        }

        window.updateItemStylist = function (detailId, stylistId) {
            const item = selectedDetails.find(d => d.id === detailId);
            if (item) {
                item.stylistId = stylistId;
                // Get category from option attribute
                const selector = document.querySelector(`.stylist-selector[data-id="${detailId}"]`);
                const selectedOption = selector.options[selector.selectedIndex];
                item.stylistKategori = selectedOption ? selectedOption.getAttribute('data-kategori') : null;
                renderSelectedTreatments();
            }
        };

        function resetLastCreatedBookingId() {
            window.lastCreatedBookingId = null;
        }

        window.updateGlobalStylist = function (stylistId, element) {
            resetLastCreatedBookingId();
            const kat = stylistId ? element.getAttribute('data-kategori') : null;

            // UI Update for Global
            element.parentElement.querySelectorAll('.stylist-card-modern').forEach(c => c.classList.remove('active'));
            element.classList.add('active');

            selectedDetails.forEach(d => {
                d.stylistId = stylistId;
                d.stylistKategori = kat;
            });
            renderSelectedTreatments();
        };

        // Render initial details if there is only 1 variant auto-selected
        if (selectedDetails.length > 0) {
            renderSelectedTreatments();
        }

        // Trigger availability check when date or time changes
        document.getElementById('reservation_date').addEventListener('change', function() {
            resetLastCreatedBookingId();
            checkStylistAvailability();
        });
        document.getElementById('reservation_time').addEventListener('change', function() {
            resetLastCreatedBookingId();
            checkStylistAvailability();
        });

        window.removeDetail = function (id) {
            resetLastCreatedBookingId();
            selectedDetails = selectedDetails.filter(d => d.id !== id);
            
            // Also uncheck the checkbox if it exists in the UI (catalog)
            const checkbox = document.getElementById(`detail_${id}`);
            if (checkbox) {
                checkbox.checked = false;
                const label = checkbox.nextElementSibling;
                if (label) {
                    label.classList.remove('bg-white', 'border-primary', 'shadow-sm', 'bg-light-primary');
                    const icon = label.querySelector('.check-icon');
                    if (icon) icon.style.display = 'none';
                }
            }
            
            renderSelectedTreatments();
            checkStylistAvailability();
        };

        // Modal Add Detail logic
        document.querySelectorAll('.add-detail-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                resetLastCreatedBookingId();
                const id = parseInt(this.getAttribute('data-id'));
                const parentId = parseInt(this.getAttribute('data-parent-id'));
                const parentName = this.getAttribute('data-parent-name');

                if (selectedDetails.some(d => d.id === id)) {
                    alert('Layanan ini sudah ditambahkan.');
                    return;
                }

                // If another variant of the same treatment is already added, ask to replace it
                const existingIndex = selectedDetails.findIndex(d => d.parentId === parentId);
                if (existingIndex !== -1) {
                    if (confirm(`Kategori "${parentName}" sudah ada di daftar. Ganti dengan varian ini?`)) {
                        // Remove the old one
                        selectedDetails.splice(existingIndex, 1);
                    } else {
                        return;
                    }
                }

                selectedDetails.push({
                    id: id,
                    parentId: parentId,
                    name: this.getAttribute('data-name'),
                    parentName: parentName,
                    price: parseInt(this.getAttribute('data-price')),
                    priceSenior: parseInt(this.getAttribute('data-price-senior') || this.getAttribute('data-price')),
                    priceJunior: parseInt(this.getAttribute('data-price-junior') || this.getAttribute('data-price')),
                    hasStylistPrice: this.getAttribute('data-has-stylist-price') === '1',
                    duration: parseInt(this.getAttribute('data-duration')),
                    isPrimary: false,
                    isPromo: this.getAttribute('data-is-promo') === '1',
                    promoType: this.getAttribute('data-promo-type'),
                    promoValue: parseInt(this.getAttribute('data-promo-value') || 0),
                    isColoring: this.getAttribute('data-is-coloring') === '1',
                    image: this.getAttribute('data-image'),
                    stylistId: selectedDetails.length > 0 ? selectedDetails[0].stylistId : null,
                    stylistKategori: selectedDetails.length > 0 ? selectedDetails[0].stylistKategori : null
                });

                renderSelectedTreatments();
                checkStylistAvailability();
                // Feedback visual
                this.classList.replace('btn-primary', 'btn-success');
                this.innerText = 'Ditambah';
                setTimeout(() => {
                    this.classList.replace('btn-success', 'btn-primary');
                    this.innerText = 'Pilih';
                }, 1000);
            });
        });

        // Search & Category Filter in Modal
        const searchInput = document.getElementById('searchTreatment');
        const catFilter = document.getElementById('modalCategoryFilter');

        function filterModalTreatments() {
            const q = searchInput.value.toLowerCase();
            const cat = catFilter.value;

            document.querySelectorAll('.treatment-item-container').forEach(item => {
                const name = item.getAttribute('data-name');
                const catId = item.getAttribute('data-category');

                const matchesSearch = name.includes(q);
                const matchesCat = (cat === 'all' || catId === cat);

                item.style.display = (matchesSearch && matchesCat) ? 'block' : 'none';
            });
        }

        searchInput.addEventListener('input', filterModalTreatments);
        catFilter.addEventListener('change', filterModalTreatments);

        function updateStepper(step) {
            for (let i = 1; i <= totalSteps; i++) {
                const indicator = document.getElementById('step-indicator-' + i);
                if (!indicator) continue;
                if (i < step) {
                    indicator.classList.add('completed');
                    indicator.classList.remove('active');
                } else if (i === step) {
                    indicator.classList.add('active');
                    indicator.classList.remove('completed');
                } else {
                    indicator.classList.remove('active', 'completed');
                }
            }
        }

        function showStep(step) {
            updateStepper(step);
            for (let i = 1; i <= totalSteps; i++) {
                const tab = document.getElementById('step' + i);
                if (i === step) {
                    if (tab) tab.classList.add('show', 'active');
                } else {
                    if (tab) tab.classList.remove('show', 'active');
                }
            }

            prevBtn.style.display = step === 1 ? 'none' : 'inline-block';
            nextBtn.style.display = step === totalSteps ? 'none' : 'inline-block';
        }

        prevBtn.addEventListener('click', () => { currentStep--; showStep(currentStep); });
        nextBtn.addEventListener('click', () => {
            if (currentStep === 1) {
                const dateInput = document.getElementById('reservation_date');
                const timeInput = document.getElementById('reservation_time');

                if (selectedDetails.length === 0) {
                    alert('Silakan pilih setidaknya satu layanan.');
                    return;
                }

                if (!dateInput.value || !timeInput.value) {
                    alert('Silakan pilih tanggal dan waktu reservasi.');
                    return;
                }

                if (selectedDetails.some(item => item.hasStylistPrice && !item.stylistId)) {
                    if (!confirm('Ada layanan yang belum dipilih stylist-nya. Lanjutkan?')) return;
                }

                // Render Summary
                let summaryHtml = '<p class="fw-bold mb-1">Layanan terpilih:</p><div class="list-group list-group-flush mb-3">';
                selectedDetails.forEach(detail => {
                    const basePrice = calculateDetailPrice(detail);
                    const currentPrice = detail.customPrice !== undefined ? detail.customPrice : basePrice;

                    let sNameText = '';

                    let discountBadge = '';
                    if (hasColoringLoyalty && detail.isColoring) {
                        discountBadge = '<span class="badge bg-soft-info text-info extra-small ms-1">Loyalty 35%</span>';
                    }

                    summaryHtml += `
                            <div class="list-group-item px-0 py-1 d-flex justify-content-between align-items-center border-0 border-bottom">
                                <div>
                                    <div class="small fw-bold">${detail.parentName} - ${detail.name} ${detail.customPrice !== undefined ? '<span class="badge bg-soft-warning text-warning extra-small ms-1">Custom Price</span>' : ''} ${discountBadge}</div>
                                    ${sNameText}
                                </div>
                                <div class="text-end">
                                    ${((hasColoringLoyalty && detail.isColoring) || detail.isPromo) && detail.customPrice === undefined ? `<div class="text-muted text-decoration-line-through extra-small">Rp ${new Intl.NumberFormat('id-ID').format(getOriginalDetailPrice(detail))}</div>` : ''}
                                    <span class="fw-bold">Rp ${new Intl.NumberFormat('id-ID').format(currentPrice)}</span>
                                </div>
                            </div>
                        `;
                });
                summaryHtml += '</div>';
                document.getElementById('summaryTreatments').innerHTML = summaryHtml;

                // Update global summary stylist
                let globalStylistName = 'Default';
                if (selectedDetails.length > 0 && selectedDetails[0].stylistId) {
                    const found = allStylists.find(s => s.id == selectedDetails[0].stylistId);
                    if (found) {
                        globalStylistName = found.name;
                    }
                } else {
                    const activeGlobalCard = document.querySelector('#global_stylist_grid .stylist-card-modern.active');
                    if (activeGlobalCard) {
                        const nameText = activeGlobalCard.querySelector('.stylist-name').innerText;
                        globalStylistName = (nameText === 'Reset' ? 'Default' : nameText);
                    }
                }
                document.getElementById('summaryStylist').innerText = globalStylistName;
                document.getElementById('summaryDatetime').innerText = dateInput.value + ' ' + timeInput.value;

                const customName = document.getElementById('customer_name_input');
                const customPhone = document.getElementById('customer_phone_input');
                const customEmail = document.getElementById('customer_email_input');
                const selUserId = document.getElementById('selected_user_id');

                if (customName && customName.value) {
                    document.getElementById('summaryCustomer').innerText = customName.value;
                    document.getElementById('paymentCustomerName').value = customName.value;
                }
                if (customPhone && customPhone.value) {
                    document.getElementById('summaryPhone').innerText = customPhone.value;
                    document.getElementById('paymentCustomerPhone').value = customPhone.value;
                }
                if (customEmail && customEmail.value) {
                    document.getElementById('summaryEmail').innerText = customEmail.value;
                    document.getElementById('paymentCustomerEmail').value = customEmail.value;
                }
                if (selUserId && selUserId.value) document.getElementById('paymentSelectedUserId').value = selUserId.value;

                document.getElementById('paymentDate').value = dateInput.value;
                document.getElementById('paymentTime').value = timeInput.value;
            }
            currentStep++;
            showStep(currentStep);
        });

        // STEP 3: FINAL CONFIRMATION & SUBMIT
        const finalForm = document.getElementById('finalBookingForm');
        const modalConfirm = new bootstrap.Modal(document.getElementById('modalConfirmBooking'));
        const btnFinalConfirm = document.getElementById('btnFinalConfirm');

        $(finalForm).on('submit', function (e) {
            e.preventDefault();
            const method = this.payment_method.value;
            if (!method) { alert('Pilih metode pembayaran.'); return; }

            document.getElementById('confirmPaymentMethod').innerText = (method === 'Tunai' ? 'Bayar Tunai' : (method === 'QRIS' ? 'QRIS / E-Wallet' : 'Transfer Bank (Midtrans)'));
            document.getElementById('confirmTotal').innerText = document.getElementById('totalPriceDisplay1').innerText;

            modalConfirm.show();
        });

        btnFinalConfirm.addEventListener('click', function () {
            modalConfirm.hide();
            submitBooking();
        });

        function showModalStatus(state, title, desc, actionHtml) {
            let iconHtml = '';
            if (state === 'loading') {
                iconHtml = '<i class="ti ti-loader text-primary spin" style="font-size: 3.5rem; display: inline-block;"></i>';
            } else if (state === 'success') {
                iconHtml = '<i class="ti ti-circle-check text-success" style="font-size: 3.5rem;"></i>';
            } else if (state === 'pending') {
                iconHtml = '<i class="ti ti-clock text-warning" style="font-size: 3.5rem;"></i>';
            } else if (state === 'error') {
                iconHtml = '<i class="ti ti-circle-x text-danger" style="font-size: 3.5rem;"></i>';
            } else if (state === 'question') {
                iconHtml = '<i class="ti ti-help-circle text-info" style="font-size: 3.5rem;"></i>';
            }

            $('#modalStatusIconContainer').html(iconHtml);
            $('#modalStatusTitle').text(title);
            if (desc.startsWith('<') || desc.includes('<strong>')) {
                $('#modalStatusDesc').html(desc);
            } else {
                $('#modalStatusDesc').text(desc);
            }
            if (actionHtml !== undefined && actionHtml !== null) {
                $('#modalStatusAction').html(actionHtml);
            }
            $('#modalProses').modal('show');
        }

        function submitBooking() {
            const form = $(finalForm);
            const submitBtn = form.find('button[type="submit"]');

            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');

            // Show loading modal immediately to guide user
            showModalStatus('loading', 'Booking sedang diproses...', 'Mohon tunggu sebentar, data booking Anda sedang disimpan.', '');

            const bookingId = window.lastCreatedBookingId;
            const url = bookingId ? `/booking/${bookingId}/update-payment-method` : form.attr('action');

            $.ajax({
                url: url,
                method: 'POST',
                data: form.serialize(),
                success: function (response) {
                    if (response.booking_id) {
                        window.lastCreatedBookingId = response.booking_id;
                    }
                    if ((response.payment_method === 'Transfer' || response.payment_method === 'QRIS' || response.payment_method === 'transfer') && response.snap_token) {
                        handleMidtrans(response.snap_token, response.booking_id || bookingId);
                    } else {
                        showSuccessFinal(response.payment_method);
                    }
                },
                error: function (xhr) {
                    submitBtn.prop('disabled', false).text('✅ Bayar & Konfirmasi');
                    showModalStatus(
                        'error',
                        'Booking Gagal ❌',
                        xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data booking.',
                        '<button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>'
                    );
                }
            });
        }

        function handleMidtrans(token, bookingId) {
            // Hide loading modal first so Midtrans overlay opens correctly
            $('#modalProses').modal('hide');

            snap.pay(token, {
                onSuccess: function (result) {
                    // Update database secara frontend (karena webhook midtrans tidak jalan di localhost)
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/booking/pay/${bookingId}`;
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';
                    form.appendChild(csrf);
                    document.body.appendChild(form);
                    form.submit();
                },
                onPending: function (result) {
                    showSuccessFinal('transfer');
                },
                onError: function (result) {
                    const isStaff = {{ ($isStaff || strtolower(Auth::user()->role) === 'karyawan') ? 'true' : 'false' }};
                    const historyUrl = isStaff ? "{{ route('admin.bookings.index') }}" : "{{ route('booking.history') }}";
                    const btnText = isStaff ? 'Lihat Status Pemesanan' : 'Lihat Riwayat Booking';
                    showModalStatus(
                        'error',
                        'Pembayaran Gagal ❌',
                        'Mohon maaf, transaksi Anda gagal diproses.',
                        `<a href="${historyUrl}" class="btn btn-primary px-4">${btnText}</a>`
                    );
                },
                onClose: function () {
                    const isStaff = {{ ($isStaff || strtolower(Auth::user()->role) === 'karyawan') ? 'true' : 'false' }};
                    const historyUrl = isStaff ? "{{ route('admin.bookings.index') }}" : "{{ route('booking.history') }}";
                    Swal.fire({
                        title: 'Pembayaran Belum Selesai ⏳',
                        text: isStaff
                            ? 'Apakah Anda ingin mencoba lagi/mengganti metode pembayaran, atau bayar nanti melalui Status Pemesanan?'
                            : 'Apakah Anda ingin mencoba lagi/mengganti metode pembayaran, atau bayar nanti melalui Riwayat Pemesanan?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '🔄 Coba Lagi / Ganti Metode',
                        cancelButtonText: isStaff ? '📅 Bayar Nanti (Ke Status Pemesanan)' : '📅 Bayar Nanti (Ke Riwayat)',
                        confirmButtonColor: '#EA8290',
                        cancelButtonColor: '#6c757d',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // User stays on page to change method or try again
                            const form = $(finalForm);
                            const submitBtn = form.find('button[type="submit"]');
                            submitBtn.prop('disabled', false).text('✅ Bayar & Konfirmasi');
                        } else {
                            // Redirect to history/status
                            window.location.href = historyUrl;
                        }
                    });
                }
            });
        }

        function showSuccessFinal(method) {
            const isStaff = {{ ($isStaff || strtolower(Auth::user()->role) === 'karyawan') ? 'true' : 'false' }};
            const historyUrl = isStaff ? "{{ route('admin.bookings.index') }}" : "{{ route('booking.history') }}";
            const btnText = isStaff ? 'Lihat Status Pemesanan' : 'Lihat Riwayat Booking';
            const actionHtml = `<a href="${historyUrl}" class="btn btn-primary px-4">${btnText}</a>`;
            const m = method ? method.toLowerCase() : '';

            if (m === 'tunai') {
                if (isStaff) {
                    showModalStatus(
                        'success',
                        'Booking Berhasil Dicatat! 📅',
                        'Booking telah berhasil dicatat dengan status pembayaran BELUM BAYAR.',
                        actionHtml
                    );
                } else {
                    showModalStatus(
                        'success',
                        'Booking Berhasil! 📅',
                        'Booking Anda telah masuk ke sistem. Silakan lakukan pembayaran tunai di lokasi (salon) setelah treatment selesai.',
                        actionHtml
                    );
                }
            } else {
                showModalStatus(
                    'pending',
                    'Booking Menunggu Pembayaran ⏳',
                    'Pesanan Anda telah dicatat. Mohon selesaikan pembayaran agar jadwal dapat dikonfirmasi.',
                    actionHtml
                );
            }
        }

        function showPendingPayment(bookingId) {
            const isStaff = {{ ($isStaff || strtolower(Auth::user()->role) === 'karyawan') ? 'true' : 'false' }};
            const historyUrl = isStaff ? "{{ route('admin.bookings.index') }}" : "{{ route('booking.history') }}";
            const desc = isStaff
                ? `Pembayaran belum selesai. Anda bisa melanjutkan pembayaran melalui Status Pemesanan, atau jika ingin <strong>bayar di tempat</strong>, Anda bisa mengganti metodenya sekarang.`
                : `Pembayaran belum selesai. Anda bisa melanjutkan pembayaran melalui Riwayat Booking, atau jika ingin <strong>bayar di tempat</strong>, Anda bisa mengganti metodenya sekarang.`;
            const actionHtml = `
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-secondary" onclick="window.location.href='${historyUrl}'">Nanti Saja</button>
                    <button class="btn btn-success" onclick="switchPaymentToTunai(${bookingId})">Ganti ke Bayar Tunai</button>
                </div>
            `;
            showModalStatus('question', 'Lanjutkan Pembayaran?', desc, actionHtml);
        }

        window.switchPaymentToTunai = function (id) {
            const btn = event.target;
            $(btn).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Memproses...');

            $.ajax({
                url: `/booking/${id}/update-payment-method`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    payment_method: 'tunai'
                },
                success: function (response) {
                    showSuccessFinal('tunai');
                },
                error: function (xhr) {
                    $(btn).prop('disabled', false).text('Ganti ke Bayar Tunai');
                    Swal.fire({
                        title: 'Gagal Mengubah Metode ❌',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan saat mengubah metode pembayaran.',
                        icon: 'error',
                        confirmButtonColor: '#EA8290'
                    });
                }
            });
        };

        // Initial setup
        renderSelectedTreatments();
        showStep(currentStep);

        // Logic Filter Kategori Stylist & Stylist Details (Tetap ada di bawah)
        let stylistCategoryEl = document.getElementById('stylist_category');
        if (stylistCategoryEl) {
            stylistCategoryEl.addEventListener('change', function () {
                let selectedCategory = this.value.toLowerCase();
                let stylistContainer = document.getElementById('stylist_container');
                let stylistSelect = document.getElementById('stylist');
                let options = stylistSelect.querySelectorAll('option');

                stylistSelect.value = "";
                if (selectedCategory === "") {
                    stylistContainer.style.display = 'none';
                } else {
                    stylistContainer.style.display = 'block';
                }

                options.forEach(option => {
                    if (option.value === "") {
                        option.style.display = 'block';
                        return;
                    }
                    let kategoriOption = option.getAttribute('data-kategori');
                    option.style.display = (selectedCategory === "" || kategoriOption === selectedCategory) ? 'block' : 'none';
                });
                renderSelectedTreatments();
            });
        }

        // Update harga berdasarkan stylist yang dipilih
        let stylistEl = document.getElementById('stylist');
        if (stylistEl) {
            stylistEl.addEventListener('change', renderSelectedTreatments);
        }

        // Initial setup
        renderSelectedTreatments();
        checkStylistAvailability();
        showStep(currentStep);
    </script>
@endpush

@endsection