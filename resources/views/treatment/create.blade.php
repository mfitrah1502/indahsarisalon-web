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

<style>
    .upload-zone {
        border: 2px dashed #e2e8f0;
        border-radius: 16px;
        padding: 40px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        background: #f8fafc;
    }

    .upload-zone:hover {
        border-color: #db2777;
        background: #fff1f2;
    }

    .detail-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.3s;
        position: relative;
    }

    .detail-card:hover {
        border-color: #db2777;
        box-shadow: 0 4px 12px rgba(219, 39, 119, 0.08);
    }

    .btn-remove-detail {
        position: absolute;
        top: -10px;
        right: -10px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #ef4444;
        color: #fff;
        border: 2px solid #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        z-index: 10;
    }

    .form-section-title {
        font-size: 0.9rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
    }

    .form-section-title i {
        font-size: 1.25rem;
        margin-right: 0.5rem;
        color: #db2777;
    }

    .glass-input {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 0.6rem 1rem !important;
    }

    .glass-input:focus {
        border-color: #db2777 !important;
        box-shadow: 0 0 0 3px rgba(219, 39, 119, 0.1) !important;
    }
</style>

@section('content')
<div class="container-fluid p-0">
    <form action="{{ route('treatment.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row g-4">
            <!-- Left Column: Primary Info -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="form-section-title">
                            <i class="ti ti-info-circle"></i> Informasi Dasar
                        </div>
                        
                        <div class="mb-4 text-center">
                            <div id="drop-area" class="upload-zone mb-3">
                                <div id="upload-placeholder">
                                    <i class="ti ti-cloud-upload fs-1 text-pink-600 mb-2 d-block"></i>
                                    <span class="fw-bold d-block">Upload Gambar</span>
                                    <small class="text-muted">JPG or PNG (Max. 2MB)</small>
                                </div>
                                <img id="imagePreview" class="img-fluid rounded-3" style="display:none; max-height: 200px;">
                                <input type="file" name="image" id="imageInput" accept="image/*" hidden>
                            </div>
                            <button type="button" id="removeImage" class="btn btn-outline-danger btn-sm rounded-pill" style="display:none;">
                                <i class="ti ti-trash"></i> Hapus Gambar
                            </button>
                        </div>

                        <div class="mb-3">
                            <label class="small fw-bold mb-2">Nama Treatment</label>
                            <input type="text" name="name" class="form-control glass-input" placeholder="Contoh: Hair Ritual" required>
                        </div>

                        <div class="mb-3">
                            <label class="small fw-bold mb-2">Kategori</label>
                            <div class="mb-2">
                                <select name="category_id" class="form-select glass-input">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text border-end-0 bg-white"><i class="ti ti-plus text-muted"></i></span>
                                <input type="text" name="category" class="form-control border-start-0 ps-0" placeholder="Atau tambah kategori baru...">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="form-section-title">
                            <i class="ti ti-settings"></i> Pengaturan
                        </div>

                        <div class="settings-group mb-4 p-3 bg-light rounded-3">
                            <div class="form-check form-switch custom-switch">
                                <input class="form-check-input" type="checkbox" id="allow_multi_select" name="allow_multi_select" value="1" checked>
                                <label class="form-check-label fw-bold small" for="allow_multi_select">Mode Pilihan Banyak</label>
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">
                                Jika aktif, pelanggan bisa memilih lebih dari satu varian layanan ini.
                            </small>
                        </div>

                        <div class="promo-section">
                            <div class="form-check form-switch custom-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="is_promo" name="is_promo" value="1">
                                <label class="form-check-label fw-bold small" for="is_promo">Aktifkan Promo</label>
                            </div>

                            <div id="promo_fields" style="display:none;" class="p-3 bg-white border rounded-3 shadow-sm animate__animated animate__fadeIn">
                                <div class="mb-3">
                                    <label class="small fw-bold mb-2">Jenis Potongan</label>
                                    <div class="d-flex gap-2">
                                        <input type="radio" class="btn-check" name="promo_type" id="type_percent" value="percent" checked>
                                        <label class="btn btn-outline-primary btn-sm rounded-pill flex-fill" for="type_percent">Persen (%)</label>
                                        
                                        <input type="radio" class="btn-check" name="promo_type" id="type_fixed" value="fixed">
                                        <label class="btn btn-outline-primary btn-sm rounded-pill flex-fill" for="type_fixed">Nominal (Rp)</label>
                                    </div>
                                </div>
                                <div>
                                    <label class="small fw-bold mb-2">Nilai Potongan</label>
                                    <input type="number" name="promo_value" class="form-control glass-input" placeholder="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Details -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-section-title mb-0">
                                <i class="ti ti-list-details"></i> Daftar Variasi & Harga
                            </div>
                            <button type="button" id="add_detail" class="btn btn-outline-primary btn-sm rounded-pill">
                                <i class="ti ti-plus me-1"></i> Tambah Variasi
                            </button>
                        </div>

                        <div id="details_wrapper" class="row">
                            <!-- Detail Items will be here -->
                            <div class="col-md-6 mb-4 detail-item-wrapper">
                                <div class="detail_item detail-card p-4">
                                    <button type="button" class="btn btn-remove-detail remove-detail"><i class="ti ti-x"></i></button>
                                    
                                    <div class="mb-3">
                                        <label class="small fw-bold mb-1">Nama Variasi</label>
                                        <input type="text" name="details[0][name]" class="form-control glass-input" placeholder="Contoh: Ukuran Medium" required>
                                    </div>

                                    <div class="row g-2 mb-3">
                                        <div class="col-7">
                                            <label class="small fw-bold mb-1 text-primary">Harga Dasar</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 small">Rp</span>
                                                <input type="number" name="details[0][price]" class="form-control glass-input normal-price-input border-start-0" placeholder="0" required>
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <label class="small fw-bold mb-1">Durasi</label>
                                            <div class="input-group">
                                                <input type="number" name="details[0][duration]" class="form-control glass-input" placeholder="0" required>
                                                <span class="input-group-text bg-light border-start-0 small">mnt</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="stylist-price-toggle-box mb-3">
                                        <div class="form-check form-check-inline">
                                            <input type="checkbox" class="form-check-input stylist-price-toggle" 
                                                name="details[0][has_stylist_price]" value="1" id="has_stylist_price_0">
                                            <label class="form-check-label small fw-bold" for="has_stylist_price_0">Gunakan Harga Senior/Junior</label>
                                        </div>
                                    </div>

                                    <div class="stylist-price-container p-3 bg-light rounded-3 mb-3" style="display: none;">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="extra-small fw-bold mb-1">Senior</label>
                                                <input type="number" name="details[0][price_senior]" class="form-control glass-input form-control-sm stylist-price-input" placeholder="Rp 0">
                                            </div>
                                            <div class="col-6">
                                                <label class="extra-small fw-bold mb-1">Junior</label>
                                                <input type="number" name="details[0][price_junior]" class="form-control glass-input form-control-sm stylist-price-input" placeholder="Rp 0">
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="small fw-bold mb-1">Keterangan (Opsional)</label>
                                        <textarea name="details[0][description]" class="form-control glass-input" rows="2" placeholder="Catatan tambahan..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 border-top pt-4 text-end">
                            <a href="{{ route('treatment.index') }}" class="btn btn-link text-muted me-3">Batal</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 shadow">
                                <i class="ti ti-device-floppy me-1"></i> Simpan Treatment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
    <script>
        // Promo toggle
        $('#is_promo').change(function () {
            if ($(this).is(':checked')) {
                $('#promo_fields').slideDown();
            } else {
                $('#promo_fields').slideUp();
            }
        });

        let detail_index = 1;
        $('#add_detail').click(function () {
            let html = `
            <div class="col-md-6 mb-4 detail-item-wrapper animate__animated animate__fadeInUp">
                <div class="detail_item detail-card p-4">
                    <button type="button" class="btn btn-remove-detail remove-detail"><i class="ti ti-x"></i></button>
                    
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Nama Variasi</label>
                        <input type="text" name="details[${detail_index}][name]" class="form-control glass-input" placeholder="Contoh: Ukuran Long" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-7">
                            <label class="small fw-bold mb-1 text-primary">Harga Dasar</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 small">Rp</span>
                                <input type="number" name="details[${detail_index}][price]" class="form-control glass-input normal-price-input border-start-0" placeholder="0" required>
                            </div>
                        </div>
                        <div class="col-5">
                            <label class="small fw-bold mb-1">Durasi</label>
                            <div class="input-group">
                                <input type="number" name="details[${detail_index}][duration]" class="form-control glass-input" placeholder="0" required>
                                <span class="input-group-text bg-light border-start-0 small">mnt</span>
                            </div>
                        </div>
                    </div>

                    <div class="stylist-price-toggle-box mb-3">
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input stylist-price-toggle" 
                                name="details[${detail_index}][has_stylist_price]" value="1" id="has_stylist_price_${detail_index}">
                            <label class="form-check-label small fw-bold" for="has_stylist_price_${detail_index}">Gunakan Harga Senior/Junior</label>
                        </div>
                    </div>

                    <div class="stylist-price-container p-3 bg-light rounded-3 mb-3" style="display: none;">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="extra-small fw-bold mb-1">Senior</label>
                                <input type="number" name="details[${detail_index}][price_senior]" class="form-control glass-input form-control-sm stylist-price-input" placeholder="Rp 0">
                            </div>
                            <div class="col-6">
                                <label class="extra-small fw-bold mb-1">Junior</label>
                                <input type="number" name="details[${detail_index}][price_junior]" class="form-control glass-input form-control-sm stylist-price-input" placeholder="Rp 0">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="small fw-bold mb-1">Keterangan</label>
                        <textarea name="details[${detail_index}][description]" class="form-control glass-input" rows="2" placeholder="Catatan tambahan..."></textarea>
                    </div>
                </div>
            </div>`;
            $('#details_wrapper').append(html);
            detail_index++;
        });

        // Remove detail
        $(document).on('click', '.remove-detail', function() {
            if ($('.detail_item').length > 1) {
                $(this).closest('.detail-item-wrapper').fadeOut(300, function() { $(this).remove(); });
            } else {
                alert("Minimal harus ada 1 detail treatment.");
            }
        });

        // Stylist price toggle
        $(document).on('change', '.stylist-price-toggle', function() {
            let container = $(this).closest('.detail_item');
            if ($(this).is(':checked')) {
                container.find('.normal-price-input').prop('disabled', true).val('');
                container.find('.stylist-price-container').slideDown();
                container.find('.stylist-price-input').prop('required', true);
            } else {
                container.find('.normal-price-input').prop('disabled', false).prop('required', true);
                container.find('.stylist-price-container').slideUp();
                container.find('.stylist-price-input').removeAttr('required').val('');
            }
        });

        // Image Drop Logic
        const dropArea = document.getElementById('drop-area');
        const inputFile = document.getElementById('imageInput');
        const preview = document.getElementById('imagePreview');
        const placeholder = document.getElementById('upload-placeholder');
        const removeBtn = document.getElementById('removeImage');

        dropArea.addEventListener('click', () => inputFile.click());

        inputFile.addEventListener('change', function () {
            handleFile(this.files[0]);
        });

        function handleFile(file) {
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
                removeBtn.style.display = 'inline-block';
            }
            reader.readAsDataURL(file);
        }

        removeBtn.addEventListener('click', function () {
            inputFile.value = '';
            preview.style.display = 'none';
            placeholder.style.display = 'block';
            removeBtn.style.display = 'none';
        });
    </script>
@endpush
@endsection