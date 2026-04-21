
        const allStylists = [];
        // Map avatars separately since we have an accessor but Laravel json encode might not include it by default
        allStylists.forEach(s => {
            s.avatar_url = "1"; // Initial fallback
        });
        
        // Re-map with actual calculated URLs from PHP to be safe
        const stylistAvatars = {
            
        };
        allStylists.forEach(s => {
            s.avatar_url = stylistAvatars[s.id];
        });

        // Initialize min date & time logic
        function initTimeSelection() {
            const dateInput = document.getElementById('reservation_date');
            const timeSelect = document.getElementById('reservation_time');
            
            // Tanggal Libur dari Backend
            const holidayDates = 1;

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
            
        ];

        window.togglePrimaryDetail = function (checkbox) {
            const isMulti = 1;
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
                            <div class="mt-3">
                                <label class="extra-small text-muted mb-2"><i class="ti ti-hand-click me-1"></i>Pilih Stylist:</label>
                                <div class="stylist-grid" data-detail-id="${d.id}">
                                    ${allStylists.map(s => {
                                        const isSelected = d.stylistId == s.id;
                                        return \`
                                            <div class="stylist-card-modern \${isSelected ? 'active' : ''} stylist-item-\${s.id}" 
                                                 data-stylist-id="\${s.id}" 
                                                 data-kategori="\${s.kategori ? s.kategori.toLowerCase() : ''}"
                                                 onclick="updateItemStylistCards(\${d.id}, \${s.id}, this)">
                                                <div class="check-mark"><i class="ti ti-check"></i></div>
                                                <div class="avatar-container">
                                                    <img src="\${s.avatar_url}" alt="\${s.name}">
                                                </div>
                                                <span class="stylist-name">\${s.name ? s.name.split(' ')[0] : ''}</span>
                                                <span class="stylist-cat">\${s.kategori || ''}</span>
                                            </div>
                                        \`;
                                    }).join('')}
                                </div>
                            </div>
                            ` : `
                            <div class="mt-3">
                                <div class="extra-small text-muted mt-1"><i class="ti ti-info-circle me-1"></i>Harga tetap untuk layanan ini.</div>
                            </div>
                            `}
                        </div>
                    `;
                container.insertAdjacentHTML('beforeend', itemHtml);
            });

            const formattedTotal = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
            document.getElementById('totalPriceDisplay1').innerText = formattedTotal;
            document.getElementById('totalPriceDisplay2').innerText = formattedTotal;
            document.getElementById('paymentTreatmentInputs').innerHTML = hiddenInputs;

            // Show or hide the global stylist section based on selection
            const showGlobal = selectedDetails.some(d => d.hasStylistPrice);
            const globalSection = document.getElementById('globalStylistSection');
            if (globalSection) {
                globalSection.style.display = showGlobal ? '' : 'none';
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
                url: "1",
                method: 'POST',
                data: {
                    _token: "1",
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
            selectedDetails.forEach((d, index) => {
                const busyIds = busyStylistsMap[index] || [];
                const container = document.querySelector(`.stylist-grid[data-detail-id="${d.id}"]`);
                if (!container) return;

                const cards = container.querySelectorAll('.stylist-card-modern');
                cards.forEach(card => {
                    const sid = parseInt(card.getAttribute('data-stylist-id'));
                    const isOff = offWorkStylists.includes(sid);
                    const isBusy = busyIds.includes(sid);

                    // Reset special classes first
                    card.classList.remove('busy', 'disabled');
                    card.style.display = '';

                    if (isOff) {
                        card.style.display = 'none'; // Completely hide if off work
                    } else if (isBusy) {
                        card.classList.add('busy', 'disabled');
                    } else {
                        // Normal state
                    }

                    // If selected stylist becomes unavailable, reset
                    if (d.stylistId == sid && (isOff || isBusy)) {
                        card.classList.remove('active');
                        d.stylistId = null;
                        d.stylistKategori = null;
                        renderSelectedTreatments(); // Refresh to show price reset
                    }
                });
            });

            // Also update the global stylist grid
            const globalGrid = document.getElementById('global_stylist_grid');
            if (globalGrid) {
                const globalCards = globalGrid.querySelectorAll('.stylist-card-modern');
                globalCards.forEach(card => {
                    const sid = card.getAttribute('data-stylist-id');
                    if (!sid) return; // Skip reset card
                    const isOff = offWorkStylists.includes(parseInt(sid));
                    card.style.display = isOff ? 'none' : '';
                });
            }
        }

        window.updateItemStylistCards = function (detailId, stylistId, element) {
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

        window.updateGlobalStylist = function (stylistId, element) {
            const kat = stylistId ? element.getAttribute('data-kategori') : null;
            
            // UI Update for Global
            element.parentElement.querySelectorAll('.stylist-card-modern').forEach(c => c.classList.remove('active'));
            element.classList.add('active');

            selectedDetails.forEach(d => {
                // We only apply this to details that have stylist selection enabled
                if (d.hasStylistPrice) {
                    d.stylistId = stylistId;
                    d.stylistKategori = kat;
                }
            });
            renderSelectedTreatments();
        };

        // Render initial details if there is only 1 variant auto-selected
        if (selectedDetails.length > 0) {
            renderSelectedTreatments();
        }

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
                    csrf.value = '1';
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
                    $('#modalStatusAction').html('<a href="1" class="btn btn-primary px-4">Lihat Riwayat Booking</a>');
                    $('#modalProses').modal('show');
                },
                onClose: function () {
                    showSuccessFinal('transfer'); // Menampilkan pesan 'Booking Menunggu Pembayaran'
                }
            });
        }

        function showSuccessFinal(method) {
            const isStaff = 1;

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

            $('#modalStatusAction').html('<a href="1" class="btn btn-primary px-4">Lihat Riwayat Booking</a>');
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
                        <button class="btn btn-outline-secondary" onclick="window.location.href='1'">Nanti Saja</button>
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
                    _token: '1',
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
        checkStylistAvailability();
        showStep(currentStep);
    