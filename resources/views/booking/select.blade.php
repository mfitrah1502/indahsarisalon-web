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
                        <div class="p-3 mb-4 rounded" style="background:#f8f9fa;">
                            <h5 class="mb-2">💆 {{ $treatment->name }}</h5>
                            <ul class="mb-2 ps-3">
                                @foreach($treatment->details as $detail)
                                    <li>
                                        {{ $detail->name }} - {{ $detail->duration }} menit
                                        <strong>(Rp {{ number_format($detail->price) }})</strong>
                                    </li>
                                @endforeach
                            </ul>
                            <strong>Total: <span id="totalPriceDisplay1">Rp
                                    {{ number_format($treatment->details->sum('price')) }}</span></strong>
                        </div>

                        <form id="bookingForm">
                            <div class="row">
                                {{-- Jika login sebagai Admin atau Karyawan, tampilkan input Nama Pelanggan --}}
                                @if(in_array(Auth::user()->role, ['admin', 'karyawan']))
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold">👤 Nama Pelanggan (Offline)</label>
                                        <input type="text" name="customer_name_input" id="customer_name_input" class="form-control" placeholder="Masukkan nama pelanggan..." required>
                                        <small class="text-muted">Gunakan ini jika pelanggan tidak memiliki akun/HP.</small>
                                    </div>
                                @endif

                                @php
                                    $hasStylistPrice = $treatment->details->contains('has_stylist_price', true);
                                @endphp

                                @if($hasStylistPrice)
                                    <!-- KATEGORI STYLIST -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">🔖 Kategori Stylist</label>
                                        <select id="stylist_category" class="form-select">
                                            <option value="">-- Semua Kategori --</option>
                                            <option value="senior">Senior</option>
                                            <option value="junior">Junior</option>
                                        </select>
                                    </div>

                                    <!-- STYLIST -->
                                    <div class="col-md-6 mb-3" id="stylist_container" style="display: none;">
                                        <label class="form-label">👩‍🎨 Stylist</label>
                                        <select name="stylist_id" id="stylist" class="form-select" required>
                                            <option value="">-- Pilih Stylist --</option>
                                            @foreach($stylists as $stylist)
                                                <option value="{{ $stylist->id }}"
                                                    data-kategori="{{ strtolower($stylist->kategori) }}">
                                                    {{ $stylist->name }}
                                                    {{ $stylist->kategori ? '(' . ucfirst($stylist->kategori) . ')' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <!-- TANGGAL -->
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">📅 Tanggal</label>
                                    <input type="date" name="reservation_date" id="reservation_date" class="form-control"
                                        required>
                                </div>

                                <!-- JAM -->
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">⏰ Jam</label>
                                    <input type="time" name="reservation_time" id="reservation_time" class="form-control"
                                        required>
                                </div>

                            </div>
                        </form>
                    </div>

                    <!-- STEP 2 -->
                    <div class="tab-pane fade" id="step2">
                        <div class="p-3 rounded" style="background:#f8f9fa;">
                            <h5 class="mb-3">📋 Ringkasan Booking</h5>

                            <p><strong>Treatment:</strong> {{ $treatment->name }}</p>

                            <p><strong>Customer:</strong> <span id="summaryCustomer">{{ Auth::user()->name }}</span></p>

                            <p><strong>Detail:</strong><br>
                                @foreach($treatment->details as $detail)
                                    - {{ $detail->name }} ({{ $detail->duration }} menit) - Rp
                                    {{ number_format($detail->price) }}<br>
                                @endforeach
                            </p>

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

                                <input type="hidden" name="treatment_id" value="{{ $treatment->id }}">
                                <input type="hidden" name="customer_name" id="paymentCustomerName" value="{{ Auth::user()->name }}">
                                <input type="hidden" name="stylist_id" id="paymentStylist">
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

    <!-- MODAL PROSES -->
    <div class="modal fade" id="modalProses" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="mb-3">
                        <i class="ti ti-loader text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h4 id="modalStatusTitle">Booking sedang diproses</h4>
                    <p id="modalStatusDesc" class="text-muted">Terima kasih telah melakukan booking. Silakan klik tombol di bawah untuk kembali.</p>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary px-4">Kembali ke Dashboard</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Midtrans Snap JS -->
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>

    <script>
        layout_change('light');
        font_change('Roboto');
        change_box_container('false');
        layout_caption_change('true');
        layout_rtl_change('false');
        preset_change('preset-1');

        function updateStepper(step) {
            for (let i = 1; i <= 3; i++) {
                let el = document.getElementById('step-indicator-' + i);

                el.classList.remove('active', 'completed');

                if (i < step) {
                    el.classList.add('completed');
                } else if (i === step) {
                    el.classList.add('active');
                }
            }
        }
        let currentStep = 1;
        const totalSteps = 3;

        const nextBtn = document.getElementById('nextStep');
        const prevBtn = document.getElementById('prevStep');

        function showStep(step) {
            updateStepper(step); // Memanggil update visual untuk step indicator lingkaran
            for (let i = 1; i <= totalSteps; i++) {
                const tab = document.getElementById('step' + i);
                const btn = document.getElementById('step' + i + '-tab'); // Boleh null jika tidak pakai btn
                if (i === step) {
                    if (tab) tab.classList.add('show', 'active');
                    if (btn) btn.classList.add('active');
                } else {
                    if (tab) tab.classList.remove('show', 'active');
                    if (btn) btn.classList.remove('active');
                }
            }

            prevBtn.style.display = step === 1 ? 'none' : 'inline-block';
            nextBtn.style.display = step === totalSteps ? 'none' : 'inline-block';
        }

        prevBtn.addEventListener('click', () => { currentStep--; showStep(currentStep); });
        nextBtn.addEventListener('click', () => {
                if (currentStep === 1) {
                    const s = document.getElementById('stylist');
                    const d = document.getElementById('reservation_date');
                    const time = document.getElementById('reservation_time');

                    // Validasi step 1
                    if(!d.value || !time.value) {
                        alert('Silakan pilih tanggal dan waktu reservasi.');
                        return;
                    }

                    // isi ringkasan
                    let stylistNameText = (s && s.selectedIndex > 0) ? s.selectedOptions[0].text : '-';
                    document.getElementById('summaryStylist').innerText = stylistNameText;
                    document.getElementById('summaryDatetime').innerText = d.value + ' ' + time.value;

                    // Update Nama Pelanggan jika Karyawan
                    const customName = document.getElementById('customer_name_input');
                    if(customName && customName.value) {
                        document.getElementById('summaryCustomer').innerText = customName.value;
                        document.getElementById('paymentCustomerName').value = customName.value;
                    }

                    // isi hidden form payment
                    document.getElementById('paymentStylist').value = s ? s.value : '';
                    document.getElementById('paymentDate').value = d.value;
                    document.getElementById('paymentTime').value = time.value;
                }
            currentStep++;
            showStep(currentStep);
        });

        // AJAX FORM SUBMISSION
        $('#finalBookingForm').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            
            submitBtn.prop('disabled', true).text('⏳ Menyimpan...');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.payment_method === 'transfer' && response.snap_token) {
                        // Triger Midtrans Snap
                        snap.pay(response.snap_token, {
                            onSuccess: function(result) { $('#modalProses').modal('show'); },
                            onPending: function(result) { $('#modalProses').modal('show'); },
                            onError: function(result) { $('#modalProses').modal('show'); },
                            onClose: function() { $('#modalProses').modal('show'); }
                        });
                    } else {
                        // Cash payment logic
                        if (response.payment_method === 'cash') {
                            const isStaff = {{ in_array(Auth::user()->role, ['admin', 'karyawan']) ? 'true' : 'false' }};
                            if (isStaff) {
                                $('#modalStatusTitle').text('Pembayaran Berhasil! ✅');
                                $('#modalStatusDesc').text('Booking telah berhasil dicatat dan status pembayaran ditandai sebagai LUNAS.');
                            } else {
                                $('#modalStatusTitle').text('Booking Berhasil! 📅');
                                $('#modalStatusDesc').text('Booking Anda telah masuk ke sistem. Silakan lakukan pembayaran di lokasi (Cash).');
                            }
                        }
                        $('#modalProses').modal('show');
                    }
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan: ' + (xhr.responseJSON?.message || 'Gagal menyimpan booking'));
                    submitBtn.prop('disabled', false).text('✅ Bayar & Konfirmasi');
                }
            });
        });

        // Inisialisasi step pertama
        showStep(currentStep);

        // Logic Filter Kategori Stylist
        let stylistCategoryEl = document.getElementById('stylist_category');
        if (stylistCategoryEl) {
            stylistCategoryEl.addEventListener('change', function () {
                let selectedCategory = this.value.toLowerCase();
                let stylistContainer = document.getElementById('stylist_container');
                let stylistSelect = document.getElementById('stylist');
                let options = stylistSelect.querySelectorAll('option');

                // Reset seleksi
                stylistSelect.value = "";

                // Tampilkan/Sembunyikan container dropdown stylist
                if (selectedCategory === "") {
                    stylistContainer.style.display = 'none';
                } else {
                    stylistContainer.style.display = 'block';
                }

                options.forEach(option => {
                    // Jangan sembunyikan option default
                    if (option.value === "") {
                        option.style.display = 'block';
                        return;
                    }

                    let kategoriOption = option.getAttribute('data-kategori');

                    // Jika tidak ada kategori yang dipilih, tampilkan semua (meskipun sudah di hide di level container)
                    if (selectedCategory === "") {
                        option.style.display = 'block';
                    } else {
                        // Sematkan filter sesuai kategori
                        if (kategoriOption === selectedCategory) {
                            option.style.display = 'block';
                        } else {
                            option.style.display = 'none';
                        }
                    }
                });
            });
        }

        // Update harga berdasarkan stylist yang dipilih
        let stylistEl = document.getElementById('stylist');
        if (stylistEl) {
            stylistEl.addEventListener('change', function () {
                let selectedOption = this.options[this.selectedIndex];
                if (!selectedOption) return;

                let kategori = selectedOption.getAttribute('data-kategori');
                let details = @json($treatment->details);
                let newPrice = 0;

                details.forEach(function (detail) {
                    if (detail.has_stylist_price && kategori) {
                        if (kategori === 'senior') {
                            newPrice += parseInt(detail.price_senior || 0);
                        } else if (kategori === 'junior') {
                            newPrice += parseInt(detail.price_junior || 0);
                        } else {
                            newPrice += parseInt(detail.price || 0);
                        }
                    } else {
                        newPrice += parseInt(detail.price || 0);
                    }
                });

                let formattedPrice = 'Rp ' + new Intl.NumberFormat('id-ID').format(newPrice);
                document.getElementById('totalPriceDisplay1').innerText = formattedPrice;
                document.getElementById('totalPriceDisplay2').innerText = formattedPrice;
            });
        }

    </script>
@endsection