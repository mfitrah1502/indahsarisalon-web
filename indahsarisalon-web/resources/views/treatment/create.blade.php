@extends('layout.dashboard')

@section('title', 'Tambah Treatment')
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

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
                    <h4 class="mb-0 fw-bold text-dark">Tambah Treatment</h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('treatment.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted mt-3">Gambar Treatment</label>
                            <div id="drop-area"
                                class="rounded-4 bg-light d-flex flex-column align-items-center justify-content-center"
                                style="border: 2px dashed #d1d5db; padding: 40px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                                <i class="ti ti-upload text-muted mb-2" style="font-size: 2rem;"></i>
                                <p class="mb-0 text-muted">Drag & drop gambar di sini atau klik untuk pilih</p>
                                <input type="file" name="image" id="imageInput" accept="image/*" hidden>
                            </div>

                            <div class="text-center">
                                <img id="imagePreview" class="img-fluid mt-3 rounded-4 shadow-sm" style="max-width: 250px; display: none;">
                                <br>
                                <button type="button" id="removeImage" class="btn btn-outline-danger rounded-pill mt-3 px-4" style="display: none;">
                                    <i class="ti ti-trash me-1"></i> Hapus Gambar
                                </button>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted">Nama Treatment</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="ti ti-按摩"></i></span>
                                    <input type="text" name="name" class="form-control bg-light border-0" placeholder="Contoh: Hair Spa" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted">Pilih Kategori</label>
                                <select name="category_id" class="form-select bg-light border-0">
                                    <option value="">-- Kategori Terdaftar --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ isset($treatment) && $treatment->category_id == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted">Atau Tambah Kategori Baru</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="ti ti-plus"></i></span>
                                <input type="text" name="category" class="form-control bg-light border-0" value="{{ old('category') }}" placeholder="Ketik nama kategori baru disini jika tidak ada di daftar atas">
                            </div>
                        </div>

                        <div class="card bg-light-warning border-0 rounded-4 mb-4">
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" class="form-check-input" id="is_promo" name="is_promo" value="1" style="transform: scale(1.2); margin-top: 4px;">
                                    <label class="form-check-label ms-2 fw-bold text-warning-dark" for="is_promo">Aktifkan Promo Treatment</label>
                                </div>
                                <div class="row align-items-end" id="promo_fields" style="display:none;">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-muted">Jenis Potongan</label>
                                        <select name="promo_type" class="form-select border-0">
                                            <option value="percent">Persen (%)</option>
                                            <option value="fixed">Nominal (Rp)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-muted">Nilai Potongan</label>
                                        <input type="number" name="promo_value" class="form-control border-0" placeholder="Contoh: 10 atau 50000">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 border-light-subtle">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-bold text-dark"><i class="ti ti-list text-primary me-2"></i>Detail Treatment & Harga</h5>
                            <button type="button" id="add_detail" class="btn btn-light-primary rounded-pill px-3 shadow-none">
                                <i class="ti ti-plus me-1"></i> Tambah Variasi
                            </button>
                        </div>

                        <div id="details_wrapper">
                            <div class="detail_item card border-light-subtle rounded-4 mb-4 shadow-none">
                                <div class="card-body bg-light rounded-4">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label text-muted small fw-bold">Kategori/Nama Varian</label>
                                            <input type="text" name="details[0][name]" class="form-control border-0" placeholder="Contoh: Rambut Pendek" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted small fw-bold">Durasi</label>
                                            <div class="input-group shadow-none">
                                                <input type="number" name="details[0][duration]" class="form-control border-0" placeholder="Durasi" required>
                                                <span class="input-group-text bg-white border-0 text-muted">menit</span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-12 mt-3">
                                            <div class="form-check form-switch mb-3">
                                                <input type="checkbox" class="form-check-input stylist-price-toggle" name="details[0][has_stylist_price]" value="1" id="has_stylist_price_0">
                                                <label class="form-check-label ms-2 text-primary fw-medium" for="has_stylist_price_0">Gunakan Harga Khusus Berdasarkan Stylist (Senior/Junior)</label>
                                            </div>

                                            <div class="normal-price-container">
                                                <div class="input-group shadow-none">
                                                    <span class="input-group-text bg-white border-0 text-muted">Rp</span>
                                                    <input type="number" name="details[0][price]" class="form-control border-0 normal-price-input" placeholder="Harga Standar" required>
                                                </div>
                                            </div>

                                            <div class="stylist-price-container" style="display: none;">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <div class="input-group shadow-none">
                                                            <span class="input-group-text bg-white border-0 text-muted">Rp</span>
                                                            <input type="number" name="details[0][price_senior]" class="form-control border-0 stylist-price-input" placeholder="Harga Khusus Stylist Senior">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-group shadow-none">
                                                            <span class="input-group-text bg-white border-0 text-muted">Rp</span>
                                                            <input type="number" name="details[0][price_junior]" class="form-control border-0 stylist-price-input" placeholder="Harga Khusus Stylist Junior">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-3">
                                            <label class="form-label text-muted small fw-bold">Keterangan Spesifik</label>
                                            <textarea name="details[0][description]" class="form-control border-0" rows="2" placeholder="Catatan opsional..."></textarea>
                                        </div>
                                    </div>
                                    <div class="text-end mt-3 border-top pt-3 border-light-subtle d-flex justify-content-end">
                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 remove-detail">
                                            <i class="ti ti-trash me-1"></i> Hapus Varian
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-5">
                            <a href="{{ route('treatment.index') }}" class="btn btn-light rounded-pill px-4 btn-cancel">Batal</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm"><i class="ti ti-device-floppy me-2"></i> Simpan Treatment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/fonts/custom-font.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/js/theme.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        layout_change('light');
        font_change('Roboto');
        change_box_container('false');
        layout_caption_change('true');
        layout_rtl_change('false');
        preset_change('preset-1');

        $('#is_promo').change(function () {
            if ($(this).is(':checked')) {
                $('#promo_fields').show();
            } else {
                $('#promo_fields').hide();
            }
        });

        let detail_index = 1;
        $('#add_detail').click(function () {
            let html = `<div class="detail_item card border-light-subtle rounded-4 mb-4 shadow-none fade-in">
                <div class="card-body bg-light rounded-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Kategori/Nama Varian</label>
                            <input type="text" name="details[${detail_index}][name]" class="form-control border-0" placeholder="Contoh: Rambut Pendek" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Durasi</label>
                            <div class="input-group shadow-none">
                                <input type="number" name="details[${detail_index}][duration]" class="form-control border-0" placeholder="Durasi" required>
                                <span class="input-group-text bg-white border-0 text-muted">menit</span>
                            </div>
                        </div>
                        
                        <div class="col-12 mt-3">
                            <div class="form-check form-switch mb-3">
                                <input type="checkbox" class="form-check-input stylist-price-toggle" name="details[${detail_index}][has_stylist_price]" value="1" id="has_stylist_price_${detail_index}">
                                <label class="form-check-label ms-2 text-primary fw-medium" for="has_stylist_price_${detail_index}">Gunakan Harga Khusus Berdasarkan Stylist (Senior/Junior)</label>
                            </div>

                            <div class="normal-price-container">
                                <div class="input-group shadow-none">
                                    <span class="input-group-text bg-white border-0 text-muted">Rp</span>
                                    <input type="number" name="details[${detail_index}][price]" class="form-control border-0 normal-price-input" placeholder="Harga Standar" required>
                                </div>
                            </div>

                            <div class="stylist-price-container" style="display: none;">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="input-group shadow-none">
                                            <span class="input-group-text bg-white border-0 text-muted">Rp</span>
                                            <input type="number" name="details[${detail_index}][price_senior]" class="form-control border-0 stylist-price-input" placeholder="Harga Khusus Stylist Senior">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group shadow-none">
                                            <span class="input-group-text bg-white border-0 text-muted">Rp</span>
                                            <input type="number" name="details[${detail_index}][price_junior]" class="form-control border-0 stylist-price-input" placeholder="Harga Khusus Stylist Junior">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-3">
                            <label class="form-label text-muted small fw-bold">Keterangan Spesifik</label>
                            <textarea name="details[${detail_index}][description]" class="form-control border-0" rows="2" placeholder="Catatan opsional..."></textarea>
                        </div>
                    </div>
                    <div class="text-end mt-3 border-top pt-3 border-light-subtle d-flex justify-content-end">
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 remove-detail">
                            <i class="ti ti-trash me-1"></i> Hapus Varian
                        </button>
                    </div>
                </div>
            </div>`;
            $('#details_wrapper').append(html);
            detail_index++;
        });

        // Event listener untuk menghapus detail
        $(document).on('click', '.remove-detail', function() {
            // Jangan hapus jika hanya tersisa 1 detail
            if ($('.detail_item').length > 1) {
                $(this).closest('.detail_item').remove();
            } else {
                alert("Minimal harus ada 1 detail treatment.");
            }
        });

        // Toggle required attributes when checkbox changes
        $(document).on('change', '.stylist-price-toggle', function() {
            let container = $(this).closest('.detail_item');
            if ($(this).is(':checked')) {
                container.find('.normal-price-container').hide();
                container.find('.normal-price-input').removeAttr('required').val('');
                container.find('.stylist-price-container').show();
                container.find('.stylist-price-input').prop('required', true);
            } else {
                container.find('.normal-price-container').show();
                container.find('.normal-price-input').prop('required', true);
                container.find('.stylist-price-container').hide();
                container.find('.stylist-price-input').removeAttr('required').val('');
            }
        });

        // Cek perubahan data
        let isSubmitting = false;
        let initialData = $('form').serialize();

        $('form').on('submit', function() {
            isSubmitting = true;
        });

        function isFormModified() {
            let modified = $('form').serialize() !== initialData;
            $('input[type="file"]').each(function() {
                if (this.files.length > 0) modified = true;
            });
            return modified;
        }

        // Konfirmasi Batal
        $(document).on('click', '.btn-cancel', function(e) {
            e.preventDefault();
            let url = $(this).attr('href');
            
            if (isFormModified()) {
                Swal.fire({
                    title: 'Batalkan input data?',
                    text: "Semua perubahan atau data yang sudah diisi tidak akan disimpan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, batalkan!',
                    cancelButtonText: 'Lanjutkan mengisi'
                }).then((result) => {
                    if (result.isConfirmed) {
                        isSubmitting = true;
                        window.location.href = url;
                    }
                });
            } else {
                isSubmitting = true;
                window.location.href = url;
            }
        });

        // Konfirmasi saat back di browser web
        window.addEventListener('beforeunload', function (e) {
            if (!isSubmitting && isFormModified()) {
                e.preventDefault();
                e.returnValue = ''; 
            }
        });

        const dropArea = document.getElementById('drop-area');
        const inputFile = document.getElementById('imageInput');
        const preview = document.getElementById('imagePreview');
        const removeBtn = document.getElementById('removeImage');

        // Klik area → buka file
        dropArea.addEventListener('click', () => inputFile.click());

        // Drag over
        dropArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropArea.style.background = '#f1f1f1';
        });

        dropArea.addEventListener('dragleave', () => {
            dropArea.style.background = '#fff';
        });

        // Drop file
        dropArea.addEventListener('drop', (e) => {
            e.preventDefault();
            dropArea.style.background = '#fff';

            const file = e.dataTransfer.files[0];
            handleFile(file);
        });

        // Input change
        inputFile.addEventListener('change', function () {
            const file = this.files[0];
            handleFile(file);
        });

        function handleFile(file) {
            if (!file) return;

            // ✅ Validasi tipe
            const allowedTypes = ['image/jpeg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                alert('Hanya JPG atau PNG!');
                return;
            }

            // ✅ Validasi ukuran (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran maksimal 2MB!');
                return;
            }

            // ✅ Preview
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                removeBtn.style.display = 'inline-block';
            }
            reader.readAsDataURL(file);
        }

        // ✅ Hapus gambar
        removeBtn.addEventListener('click', function () {
            inputFile.value = '';
            preview.src = '';
            preview.style.display = 'none';
            removeBtn.style.display = 'none';
        });
    </script>
@endsection