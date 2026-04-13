@extends('layout.dashboard')

@section('title', 'Edit Treatment')
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
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold text-dark">Edit Data Treatment</h4>
                    <a href="{{ url()->previous() }}" class="btn btn-light rounded-pill px-4 btn-cancel">Kembali</a>
                </div>


                <div class="card-body">
                    <form action="{{ route('treatment.update', $treatment->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted">Gambar Treatment Saat Ini</label>
                            <div class="text-center mb-3">
                                @if($treatment->image)
                                    <img src="https://{{ env('SUPABASE_PROJECT_REF') }}.supabase.co/storage/v1/object/public/{{ env('SUPABASE_BUCKET') }}/{{ $treatment->image }}"
                                        class="img-fluid rounded-4 shadow-sm" style="max-width: 250px; height: auto;" alt="{{ $treatment->name }}">
                                @else
                                    <img src="{{ asset('assets/images/no-image.jpg') }}" width="200" class="img-fluid rounded-4 shadow-sm">
                                @endif
                            </div>
                            
                            <label class="form-label fw-bold text-muted">Ganti Gambar (Opsional)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="ti ti-upload"></i></span>
                                <input type="file" name="image" class="form-control bg-light border-0">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted">Nama Treatment</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="ti ti-按摩"></i></span>
                                    <input type="text" name="name" class="form-control bg-light border-0" value="{{ $treatment->name }}" required>
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
                                <input type="text" name="category" class="form-control bg-light border-0" placeholder="Ketik nama kategori baru disini jika tidak ada di daftar atas">
                            </div>
                        </div>

                        <div class="card bg-light-warning border-0 rounded-4 mb-4">
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" class="form-check-input" id="is_promo" name="is_promo" value="1" {{ $treatment->is_promo ? 'checked' : '' }} style="transform: scale(1.2); margin-top: 4px;">
                                    <label class="form-check-label ms-2 fw-bold text-warning-dark" for="is_promo">Aktifkan Promo Treatment</label>
                                </div>

                                <div class="row align-items-end" id="promo_fields" style="display: {{ $treatment->is_promo ? 'flex' : 'none' }};">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-muted">Jenis Potongan</label>
                                        <select name="promo_type" class="form-select border-0">
                                            <option value="percent" {{ $treatment->promo_type == 'percent' ? 'selected' : '' }}>Persen (%)</option>
                                            <option value="fixed" {{ $treatment->promo_type == 'fixed' ? 'selected' : '' }}>Nominal (Rp)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-muted">Nilai Potongan</label>
                                        <input type="number" name="promo_value" class="form-control border-0" placeholder="Contoh: 10 atau 50000" value="{{ $treatment->promo_value }}">
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
                            @foreach($treatment->details as $index => $detail)
                                <div class="detail_item card border-light-subtle rounded-4 mb-4 shadow-none fade-in">
                                    <div class="card-body bg-light rounded-4">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label text-muted small fw-bold">Kategori/Nama Varian</label>
                                                <input type="text" name="details[{{ $index }}][name]" class="form-control border-0" placeholder="Contoh: Rambut Pendek" value="{{ $detail->name }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-muted small fw-bold">Durasi</label>
                                                <div class="input-group shadow-none">
                                                    <input type="number" name="details[{{ $index }}][duration]" class="form-control border-0" placeholder="Durasi" value="{{ $detail->duration }}" required>
                                                    <span class="input-group-text bg-white border-0 text-muted">menit</span>
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-3">
                                                <div class="form-check form-switch mb-3">
                                                    <input type="checkbox" class="form-check-input stylist-price-toggle" name="details[{{ $index }}][has_stylist_price]" value="1" id="has_stylist_price_{{ $index }}" {{ $detail->has_stylist_price ? 'checked' : '' }}>
                                                    <label class="form-check-label ms-2 text-primary fw-medium" for="has_stylist_price_{{ $index }}">Gunakan Harga Khusus Berdasarkan Stylist (Senior/Junior)</label>
                                                </div>

                                                <div class="normal-price-container" style="display: {{ $detail->has_stylist_price ? 'none' : 'block' }};">
                                                    <div class="input-group shadow-none">
                                                        <span class="input-group-text bg-white border-0 text-muted">Rp</span>
                                                        <input type="number" name="details[{{ $index }}][price]" class="form-control border-0 normal-price-input" placeholder="Harga Standar" value="{{ $detail->price }}" {{ $detail->has_stylist_price ? '' : 'required' }}>
                                                    </div>
                                                </div>

                                                <div class="stylist-price-container" style="display: {{ $detail->has_stylist_price ? 'block' : 'none' }};">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <div class="input-group shadow-none">
                                                                <span class="input-group-text bg-white border-0 text-muted">Rp</span>
                                                                <input type="number" name="details[{{ $index }}][price_senior]" class="form-control border-0 stylist-price-input" placeholder="Harga Khusus Stylist Senior" value="{{ $detail->price_senior }}" {{ $detail->has_stylist_price ? 'required' : '' }}>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group shadow-none">
                                                                <span class="input-group-text bg-white border-0 text-muted">Rp</span>
                                                                <input type="number" name="details[{{ $index }}][price_junior]" class="form-control border-0 stylist-price-input" placeholder="Harga Khusus Stylist Junior" value="{{ $detail->price_junior }}" {{ $detail->has_stylist_price ? 'required' : '' }}>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-3">
                                                <label class="form-label text-muted small fw-bold">Keterangan Spesifik</label>
                                                <textarea name="details[{{ $index }}][description]" class="form-control border-0" rows="2" placeholder="Catatan opsional...">{{ $detail->description }}</textarea>
                                            </div>
                                        </div>
                                        <div class="text-end mt-3 border-top pt-3 border-light-subtle d-flex justify-content-end">
                                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 remove-detail">
                                                <i class="ti ti-trash me-1"></i> Hapus Varian
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-5">
                            <a href="{{ route('treatment.index') }}" class="btn btn-light rounded-pill px-4 btn-cancel">Batal</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm"><i class="ti ti-device-floppy me-2"></i> Update Treatment</button>
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

        let detail_index = {{ $treatment->details->count() }};
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
                    title: 'Batalkan perubahan?',
                    text: "Setiap perubahan yang baru Anda buat tidak akan tersimpan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, batalkan!',
                    cancelButtonText: 'Kembali edit'
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
    </script>
@endsection