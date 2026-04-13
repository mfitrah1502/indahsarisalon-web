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
            <div class="card">
                <div class="card-header">
                    <h4>Tambah Treatment</h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('treatment.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label>Gambar Treatment</label>
                            <div id="drop-area"
                                style="border:2px dashed #ccc; padding:20px; text-align:center; cursor:pointer;">
                                <p>Drag & drop gambar di sini atau klik untuk pilih</p>
                                <input type="file" name="image" id="imageInput" accept="image/*" hidden>
                            </div>

                            <img id="imagePreview" class="img-fluid mt-2" style="max-width:200px; display:none;">

                            <button type="button" id="removeImage" class="btn btn-danger mt-2" style="display:none;">
                                Hapus Gambar
                            </button>
                            {{-- <input type="file" name="image" class="form-control" accept="image/*">
                            <img id="imagePreview"
                                src="{{ isset($treatment) && $treatment->image ? 'https://' . env('SUPABASE_PROJECT_REF') . '.supabase.co/storage/v1/object/public/' . env('SUPABASE_BUCKET') . '/' . $treatment->image : '' }}"
                                class="img-fluid mt-2"
                                style="max-width:200px; display: {{ isset($treatment) && $treatment->image ? 'block' : 'none' }};">
                            --}}

                        </div>
                        <div class="mb-3">
                            <label>Nama Treatment</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <label>Kategori</label>
                        <select name="category_id" class="form-control">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ isset($treatment) && $treatment->category_id == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>

                        <label>Atau Tambah Kategori Baru</label>
                        <input type="text" name="category" class="form-control" value="{{ old('category') }}"
                            placeholder="Masukkan kategori baru">

                        <div class="mb-3">
                            <label>Promo Treatment</label>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_promo" name="is_promo" value="1">
                                <label class="form-check-label" for="is_promo">Aktifkan Promo</label>
                            </div>
                        </div>

                        <div class="mb-3" id="promo_fields" style="display:none;">
                            <label>Jenis Potongan</label>
                            <select name="promo_type" class="form-control">
                                <option value="percent">Persen (%)</option>
                                <option value="fixed">Nominal</option>
                            </select>
                            <label>Nilai Potongan</label>
                            <input type="number" name="promo_value" class="form-control" placeholder="Masukkan potongan">
                        </div>

                        <hr>
                        <h5>Detail Treatment</h5>
                        <div id="details_wrapper">
                            <div class="detail_item mb-3 p-3 border rounded">
                                <input type="text" name="details[0][name]" class="form-control mb-2"
                                    placeholder="Nama Detail" required>
                                <input type="number" name="details[0][duration]" class="form-control mb-2"
                                    placeholder="Durasi (menit)" required>

                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input stylist-price-toggle" 
                                        name="details[0][has_stylist_price]" value="1" id="has_stylist_price_0">
                                    <label class="form-check-label" for="has_stylist_price_0">Aktifkan Harga Khusus Stylist (Senior/Junior)</label>
                                </div>

                                <div class="normal-price-container">
                                    <input type="number" name="details[0][price]" class="form-control mb-2 normal-price-input" 
                                        placeholder="Harga" required>
                                </div>

                                <div class="stylist-price-container" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="number" name="details[0][price_senior]" class="form-control mb-2 stylist-price-input" 
                                                placeholder="Harga Senior">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="number" name="details[0][price_junior]" class="form-control mb-2 stylist-price-input" 
                                                placeholder="Harga Junior">
                                        </div>
                                    </div>
                                </div>

                                <textarea name="details[0][description]" class="form-control mb-2"
                                    placeholder="Deskripsi"></textarea>

                                <button type="button" class="btn btn-danger btn-sm remove-detail mt-2">🗑 Hapus Detail</button>
                            </div>
                        </div>
                        <button type="button" id="add_detail" class="btn btn-secondary mb-3">Tambah Detail</button>

                        <button type="submit" class="btn btn-primary">Simpan Treatment</button>
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
            let html = `<div class="detail_item mb-3 p-3 border rounded">
                <input type="text" name="details[${detail_index}][name]" class="form-control mb-2" placeholder="Nama Detail" required>
                <input type="number" name="details[${detail_index}][duration]" class="form-control mb-2" placeholder="Durasi (menit)" required>
                
                <div class="form-check mb-2">
                    <input type="checkbox" class="form-check-input stylist-price-toggle" 
                        name="details[${detail_index}][has_stylist_price]" value="1" id="has_stylist_price_${detail_index}">
                    <label class="form-check-label" for="has_stylist_price_${detail_index}">Aktifkan Harga Khusus Stylist (Senior/Junior)</label>
                </div>

                <div class="normal-price-container">
                    <input type="number" name="details[${detail_index}][price]" class="form-control mb-2 normal-price-input" placeholder="Harga" required>
                </div>

                <div class="stylist-price-container" style="display: none;">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="number" name="details[${detail_index}][price_senior]" class="form-control mb-2 stylist-price-input" placeholder="Harga Senior">
                        </div>
                        <div class="col-md-6">
                            <input type="number" name="details[${detail_index}][price_junior]" class="form-control mb-2 stylist-price-input" placeholder="Harga Junior">
                        </div>
                    </div>
                </div>

                <textarea name="details[${detail_index}][description]" class="form-control mb-2" placeholder="Deskripsi"></textarea>
                
                <button type="button" class="btn btn-danger btn-sm remove-detail mt-2">🗑 Hapus Detail</button>
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