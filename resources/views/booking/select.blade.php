@extends('layout.dashboard')

@section('title', 'Booking Appointment')
<link rel="icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap"
    id="main-font-link" />
<link rel="stylesheet" href="{{ asset('assets/fonts/phosphor/duotone/style.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link" />
<link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}" />
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
</style>

<style>
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
</style>
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
                        @if($treatment->details->count() > 1)
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
                                                    data-name="{{ $d->name }}" data-parent-name="{{ $treatment->name }}"
                                                    data-price="{{ $d->price }}" data-price-senior="{{ $d->price_senior }}"
                                                    data-price-junior="{{ $d->price_junior }}"
                                                    data-has-stylist-price="{{ $d->has_stylist_price }}"
                                                    data-duration="{{ $d->duration }}" onchange="togglePrimaryDetail(this)">
                                                <label class="form-check-label p-2 w-100 border rounded cursor-pointer h-100"
                                                    for="detail_{{ $d->id }}">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span class="fw-bold small">{{ $d->name }}</span>
                                                        <i class="ti ti-circle-check-filled text-success check-icon"
                                                            style="display:none;"></i>
                                                    </div>
                                                    <div class="d-flex justify-content-between">
                                                        <small class="text-muted extra-small">{{ $d->duration }} menit</small>
                                                        <small class="fw-bold text-primary">Rp
                                                            {{ number_format($d->price) }}</small>
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

                        <div class="mb-3">
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" data-bs-toggle="modal"
                                data-bs-target="#modalAddTreatment">
                                <i class="ti ti-plus me-1"></i>Tambah Treatment Lainnya
                            </button>
                        </div>

                        <div class="p-3 mb-4 rounded border-start border-primary border-4" style="background:#f0f7ff;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Total Pembayaran:</span>
                                <h4 class="mb-0 text-primary fw-bold" id="totalPriceDisplay1">Rp 0</h4>
                            </div>
                        </div>

                        <form id="bookingForm">
                            <div class="row">
                                {{-- Jika login sebagai Admin atau Karyawan, tampilkan input Nama Pelanggan --}}
                                @if(in_array(Auth::user()->role, ['admin', 'karyawan']))
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold">👤 Nama Pelanggan (Offline)</label>
                                        <input type="text" name="customer_name_input" id="customer_name_input"
                                            class="form-control" placeholder="Masukkan nama pelanggan..." required>
                                        <small class="text-muted">Gunakan ini jika pelanggan tidak memiliki akun/HP.</small>
                                    </div>
                                @endif

                                @php
                                    $hasStylistPrice = $treatment->details->contains('has_stylist_price', true);
                                @endphp

                                {{-- Stylist selection will now be inside the treatment list --}}
                                <div class="col-md-12 mb-4">
                                    <div class="p-3 bg-light border rounded shadow-sm">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label mb-0 fw-bold"><i class="ti ti-users me-1"></i>Pilih
                                                Stylist untuk Semua (Cepat)</label>
                                            <select id="global_stylist_selector" class="form-select form-select-sm w-auto">
                                                <option value="">-- Pilih --</option>
                                                @foreach($stylists as $stylist)
                                                    <option value="{{ $stylist->id }}"
                                                        data-kategori="{{ strtolower($stylist->kategori) }}">
                                                        {{ $stylist->name }} ({{ ucfirst($stylist->kategori) }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <small class="text-muted">Gunakan ini untuk mengatur semua layanan ke satu stylist
                                            yang sama secara otomatis.</small>
                                    </div>
                                </div>
                                <!-- TANGGAL -->
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">📅 Tanggal</label>
                                    <input type="date" name="reservation_date" id="reservation_date" class="form-control"
                                        required>
                                </div>

                                <!-- JAM -->
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">⏰ Jam Reservasi</label>
                                    <select name="reservation_time" id="reservation_time" class="form-select" required>
                                        <option value="">-- Pilih Jam --</option>
                                    </select>
                                    <small class="text-muted extra-small">Jam buka: 09:00 - 18:00</small>
                                </div>

                            </div>
                        </form>
                    </div>

                    <!-- STEP 2 -->
                    <div class="tab-pane fade" id="step2">
                        <div class="p-3 rounded" style="background:#f8f9fa;">
                            <h5 class="mb-3">📋 Ringkasan Booking</h5>

                            <p><strong>Customer:</strong> <span id="summaryCustomer">{{ Auth::user()->name }}</span></p>

                            <div id="summaryTreatments">
                                <!-- Will be populated by JS -->
                            </div>

                            <p><strong>Stylist:</strong> <span id="summaryStylist"></span></p>
                            <p><strong>Waktu:</strong> <span id="summaryDatetime"></span></p>

                            <hr>
                            <h5>Total: <span id="totalPriceDisplay2">Rp
                                    {{ number_format($treatment->details->sum('price')) }}</span></h5>
                        </div>
                    </div>

                    <!-- STEP 3 -->
                    <div class="tab-pane fade" id="step3">
                        <div class="p-3 rounded" style="background:#f8f9fa;">
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

                                <div class="mb-3">
                                    <label class="form-label">Metode Pembayaran</label>
                                    <select name="payment_method" class="form-select" required>
                                        <option value="">-- Pilih Metode --</option>
                                        <option value="cash">Cash</option>
                                        <option value="transfer">Transfer Bank</option>
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
    <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/fonts/custom-font.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/js/theme.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
                                $imageUrl = $item->image
                                    ? env('SUPABASE_URL') . '/storage/v1/object/public/' . env('SUPABASE_BUCKET') . '/' . $item->image
                                    : asset('assets/images/no-image.jpg');
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
                                                        <div class="small fw-bold text-primary mb-1">Rp
                                                            {{ number_format($d->price) }}</div>
                                                        <button type="button" class="btn btn-primary btn-xs add-detail-btn"
                                                            data-id="{{ $d->id }}" data-name="{{ $d->name }}"
                                                            data-parent-name="{{ $item->name }}" data-price="{{ $d->price }}"
                                                            data-price-senior="{{ $d->price_senior }}"
                                                            data-price-junior="{{ $d->price_junior }}"
                                                            data-has-stylist-price="{{ $d->has_stylist_price }}"
                                                            data-duration="{{ $d->duration }}">
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
                    <div class="mb-3">
                        <i class="ti ti-loader text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h4 id="modalStatusTitle">Booking sedang diproses</h4>
                    <p id="modalStatusDesc" class="text-muted">Terima kasih telah melakukan booking. Silakan klik tombol di
                        bawah untuk kembali.</p>
                    <div id="modalStatusAction">
                        <a href="{{ route('dashboard') }}" class="btn btn-primary px-4">Kembali ke Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Midtrans Snap JS -->
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>

    <script>
        // Initialize min date & time logic
        function initTimeSelection() {
            const dateInput = document.getElementById('reservation_date');
            const timeSelect = document.getElementById('reservation_time');
            
            // Tanggal Libur dari Backend
            const holidayDates = {!! json_encode($holidays) !!};

            // 1. Flatpickr Logic
            const fp = flatpickr(dateInput, {
                locale: 'id',
                dateFormat: 'Y-m-d',
                minDate: 'today',
                disable: holidayDates,
                defaultDate: 'today',
                onChange: function(selectedDates, dateStr) {
                    updateTimeSlots();
                    checkStylistAvailability();
                }
            });

            // If today is > 18:00 or a holiday, find next available date
            function findNextAvailable() {
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
                const today = new Date().toISOString().split('T')[0];
                const currentTime = new Date();
                
                timeSelect.innerHTML = '<option value="">-- Pilih Jam --</option>';
                
                let startH = 9;
                let startM = 0;

                for (let h = 9; h <= 18; h++) {
                    for (let m = 0; m < 60; m += 15) {
                        // Max is 18:00
                        if (h === 18 && m > 0) break;

                        const timeVal = `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
                        
                        // If today, only show future slots
                        if (selectedDate === today) {
                            if (h < currentTime.getHours() || (h === currentTime.getHours() && m <= currentTime.getMinutes())) {
                                continue;
                            }
                        }

                        const option = document.createElement('option');
                        option.value = timeVal;
                        option.textContent = timeVal;
                        timeSelect.appendChild(option);
                    }
                }
            }

            // Removed redundant dateInput.addEventListener('change', updateTimeSlots);
            updateTimeSlots();
        }
        initTimeSelection();

        let currentStep = 1;
        const totalSteps = 3;

        const nextBtn = document.getElementById('nextStep');
        const prevBtn = document.getElementById('prevStep');

        // MULTIPLE TREATMENTS LOGIC (V3: Choice-based Primary)
        let selectedDetails = [
            @if($treatment->details->count() === 1)
                @foreach($treatment->details as $d)
                            {
                        id: {{ $d->id }},
                        name: "{{ $d->name }}",
                        parentName: "{{ $treatment->name }}",
                        price: {{ $d->price }},
                        priceSenior: {{ $d->price_senior ?? $d->price }},
                        priceJunior: {{ $d->price_junior ?? $d->price }},
                        hasStylistPrice: {{ $d->has_stylist_price ? 'true' : 'false' }},
                        duration: {{ $d->duration }},
                        isPrimary: true
                    },
                @endforeach
            @endif
            ];

        window.togglePrimaryDetail = function (checkbox) {
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
                        name: checkbox.getAttribute('data-name'),
                        parentName: checkbox.getAttribute('data-parent-name'),
                        price: parseInt(checkbox.getAttribute('data-price')),
                        priceSenior: parseInt(checkbox.getAttribute('data-price-senior') || checkbox.getAttribute('data-price')),
                        priceJunior: parseInt(checkbox.getAttribute('data-price-junior') || checkbox.getAttribute('data-price')),
                        hasStylistPrice: checkbox.getAttribute('data-has-stylist-price') === '1',
                        duration: parseInt(checkbox.getAttribute('data-duration')),
                        isPrimary: true
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
                const price = calculateDetailPrice(d);
                total += price;
                hiddenInputs += `<input type="hidden" name="treatment_detail_ids[]" value="${d.id}">`;
                hiddenInputs += `<input type="hidden" name="stylist_ids[]" value="${d.stylistId || ''}">`;

                const itemHtml = `
                        <div class="p-3 mb-3 rounded border-start border-3 border-primary bg-white shadow-sm">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <div class="small text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">${d.parentName}</div>
                                    <div class="fw-bold text-dark">${d.name} <small class="text-muted fw-normal">(${d.duration} mnt)</small></div>
                                    <div class="text-primary small fw-semibold">Rp ${new Intl.NumberFormat('id-ID').format(price)}</div>
                                </div>
                                <div>
                                    ${d.isPrimary ? '<span class="badge bg-light-primary text-primary rounded-pill">Utama</span>' : `<button type="button" class="btn btn-icon btn-link-danger btn-sm" onclick="removeDetail(${d.id})"><i class="ti ti-trash"></i></button>`}
                                </div>
                            </div>
                            ${d.hasStylistPrice ? `
                            <div class="row g-2 align-items-center">
                                <div class="col-sm-8">
                                    <select class="form-select form-select-sm stylist-selector" data-id="${d.id}" onchange="updateItemStylist(${d.id}, this.value)" required>
                                        <option value="">-- Pilih Stylist --</option>
                                        @foreach($stylists as $stylist)
                                            <option value="{{ $stylist->id }}" 
                                                data-kategori="{{ strtolower($stylist->kategori) }}"
                                                ${d.stylistId == {{ $stylist->id }} ? 'selected' : ''}>
                                                {{ $stylist->name }} (${"{{ ucfirst($stylist->kategori) }}"})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <small class="text-muted d-block text-end"><i class="ti ti-info-circle"></i> Harga Senior/Junior</small>
                                </div>
                            </div>
                            ` : '<div class="extra-small text-muted"><i class="ti ti-info-circle me-1"></i>Pilih stylist tidak tersedia untuk layanan ini.</div>'}
                        </div>
                    `;
                container.insertAdjacentHTML('beforeend', itemHtml);
            });

            const formattedTotal = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
            document.getElementById('totalPriceDisplay1').innerText = formattedTotal;
            document.getElementById('totalPriceDisplay2').innerText = formattedTotal;
            document.getElementById('paymentTreatmentInputs').innerHTML = hiddenInputs;

            // Apply busy states if we have them
            applyBusyStylists();
        }

        let busyStylistsMap = {};

        window.checkStylistAvailability = function () {
            const date = document.getElementById('reservation_date').value;
            const time = document.getElementById('reservation_time').value;

            if (!date || !time || selectedDetails.length === 0) return;

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
                    applyBusyStylists();
                }
            });
        };

        function applyBusyStylists() {
            selectedDetails.forEach((d, index) => {
                const busyIds = busyStylistsMap[index] || [];
                const selector = document.querySelector(`.stylist-selector[data-id="${d.id}"]`);
                if (!selector) return;

                const options = selector.querySelectorAll('option');
                options.forEach(opt => {
                    if (opt.value === "") return;
                    const sid = parseInt(opt.value);
                    if (busyIds.includes(sid)) {
                        opt.disabled = true;
                        if (!opt.textContent.includes('(Sibuk)')) {
                            opt.textContent = opt.textContent + ' (Sibuk)';
                        }
                        // If selected option becomes busy, reset it
                        if (selector.value == sid) {
                            selector.value = "";
                            d.stylistId = null;
                            d.stylistKategori = null;
                        }
                    } else {
                        opt.disabled = false;
                        opt.textContent = opt.textContent.replace(' (Sibuk)', '');
                    }
                });
            });
        }

        function calculateDetailPrice(d) {
            if (d.hasStylistPrice && d.stylistKategori) {
                if (d.stylistKategori === 'senior') return d.priceSenior;
                if (d.stylistKategori === 'junior') return d.priceJunior;
            }
            return d.price;
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

        // Global stylist helper
        document.getElementById('global_stylist_selector')?.addEventListener('change', function () {
            const sid = this.value;
            const kat = this.selectedOptions[0].getAttribute('data-kategori');
            selectedDetails.forEach(d => {
                if (d.hasStylistPrice) {
                    d.stylistId = sid;
                    d.stylistKategori = kat;
                }
            });
            renderSelectedTreatments();
        });

        // Trigger availability check when date or time changes
        document.getElementById('reservation_date').addEventListener('change', checkStylistAvailability);
        document.getElementById('reservation_time').addEventListener('change', checkStylistAvailability);

        window.removeDetail = function (id) {
            selectedDetails = selectedDetails.filter(d => d.id !== id);
            renderSelectedTreatments();
            checkStylistAvailability();
        };

        // Modal Add Detail logic
        document.querySelectorAll('.add-detail-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = parseInt(this.getAttribute('data-id'));
                if (selectedDetails.some(d => d.id === id)) {
                    alert('Layanan ini sudah ditambahkan.');
                    return;
                }

                selectedDetails.push({
                    id: id,
                    name: this.getAttribute('data-name'),
                    parentName: this.getAttribute('data-parent-name'),
                    price: parseInt(this.getAttribute('data-price')),
                    priceSenior: parseInt(this.getAttribute('data-price-senior') || this.getAttribute('data-price')),
                    priceJunior: parseInt(this.getAttribute('data-price-junior') || this.getAttribute('data-price')),
                    hasStylistPrice: this.getAttribute('data-has-stylist-price') === '1',
                    duration: parseInt(this.getAttribute('data-duration')),
                    isPrimary: false
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
                    const price = calculateDetailPrice(detail);
                    // Find stylist name
                    const sSelect = document.querySelector(`.stylist-selector[data-id="${detail.id}"]`);
                    const sName = detail.hasStylistPrice
                        ? ((sSelect && sSelect.selectedIndex > 0) ? sSelect.selectedOptions[0].text : 'Belum dipilih')
                        : 'Tidak tersedia (Tanpa Stylist)';

                    summaryHtml += `
                            <div class="list-group-item px-0 py-1 d-flex justify-content-between align-items-center border-0 border-bottom">
                                <div>
                                    <div class="small fw-bold">${detail.parentName} - ${detail.name}</div>
                                    <div class="extra-small text-muted">Stylist: ${sName}</div>
                                </div>
                                <span class="fw-bold">Rp ${new Intl.NumberFormat('id-ID').format(price)}</span>
                            </div>
                        `;
                });
                summaryHtml += '</div>';
                document.getElementById('summaryTreatments').innerHTML = summaryHtml;

                document.getElementById('summaryStylist').innerText = '(Per Layanan)';
                document.getElementById('summaryDatetime').innerText = dateInput.value + ' ' + timeInput.value;

                const customName = document.getElementById('customer_name_input');
                if (customName && customName.value) {
                    document.getElementById('summaryCustomer').innerText = customName.value;
                    document.getElementById('paymentCustomerName').value = customName.value;
                }

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

            document.getElementById('confirmPaymentMethod').innerText = (method === 'cash' ? 'Bayar Tunai (Cash)' : 'Transfer Bank (Midtrans)');
            document.getElementById('confirmTotal').innerText = document.getElementById('totalPriceDisplay1').innerText;

            modalConfirm.show();
        });

        btnFinalConfirm.addEventListener('click', function () {
            modalConfirm.hide();
            submitBooking();
        });

        function submitBooking() {
            const form = $(finalForm);
            const submitBtn = form.find('button[type="submit"]');

            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function (response) {
                    if (response.payment_method === 'transfer' && response.snap_token) {
                        handleMidtrans(response.snap_token, response.booking_id);
                    } else {
                        showSuccessFinal(response.payment_method);
                    }
                },
                error: function (xhr) {
                    alert('Terjadi kesalahan: ' + (xhr.responseJSON?.message || 'Gagal menyimpan booking'));
                    submitBtn.prop('disabled', false).text('✅ Bayar & Konfirmasi');
                }
            });
        }

        function handleMidtrans(token, bookingId) {
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
                    $('#modalStatusTitle').text('Pembayaran Gagal ❌');
                    $('#modalStatusDesc').text('Mohon maaf, transaksi Anda gagal diproses.');
                    $('#modalStatusAction').html('<a href="{{ route("booking.history") }}" class="btn btn-primary px-4">Lihat Riwayat Booking</a>');
                    $('#modalProses').modal('show');
                },
                onClose: function () {
                    showSuccessFinal('transfer'); // Menampilkan pesan 'Booking Menunggu Pembayaran'
                }
            });
        }

        function showSuccessFinal(method) {
            const isStaff = {{ in_array(Auth::user()->role, ['admin', 'karyawan']) ? 'true' : 'false' }};

            if (method === 'cash') {
                if (isStaff) {
                    $('#modalStatusTitle').text('Pembayaran Berhasil! ✅');
                    $('#modalStatusDesc').text('Booking telah berhasil dicatat dan status pembayaran ditandai sebagai LUNAS.');
                } else {
                    $('#modalStatusTitle').text('Booking Berhasil! 📅');
                    $('#modalStatusDesc').text('Booking Anda telah masuk ke sistem. Silakan lakukan pembayaran di lokasi (Cash).');
                }
            } else {
                $('#modalStatusTitle').text('Booking Menunggu Pembayaran ⏳');
                $('#modalStatusDesc').text('Pesanan Anda telah dicatat. Mohon selesaikan pembayaran agar jadwal dapat dikonfirmasi.');
            }

            $('#modalStatusAction').html('<a href="{{ route("booking.history") }}" class="btn btn-primary px-4">Lihat Riwayat Booking</a>');
            $('#modalProses').modal('show');
        }

        function showPendingPayment(bookingId) {
            $('#modalStatusTitle').text('Lanjutkan Pembayaran?');
            $('#modalStatusDesc').html(`
                    Pembayaran belum selesai. Anda bisa melanjutkan pembayaran melalui Riwayat Booking, 
                    atau jika ingin <strong>bayar di tempat</strong>, Anda bisa mengganti metodenya sekarang.
                `);

            $('#modalStatusAction').html(`
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-secondary" onclick="window.location.href='{{ route('booking.history') }}'">Nanti Saja</button>
                        <button class="btn btn-success" onclick="switchPaymentToCash(${bookingId})">Ganti ke Bayar Tunai (Cash)</button>
                    </div>
                `);
            $('#modalProses').modal('show');
        }

        window.switchPaymentToCash = function (id) {
            const btn = event.target;
            $(btn).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Memproses...');

            $.ajax({
                url: `/booking/${id}/update-payment-method`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    payment_method: 'cash'
                },
                success: function (response) {
                    showSuccessFinal('cash');
                },
                error: function (xhr) {
                    alert('Gagal mengubah metode: ' + (xhr.responseJSON?.message || 'Error'));
                    $(btn).prop('disabled', false).text('Ganti ke Bayar Tunai (Cash)');
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
        showStep(currentStep);
    </script>
@endsection