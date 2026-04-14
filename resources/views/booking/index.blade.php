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
</style>
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Book an Appointment</h4>
                </div>

                <div class="card-body">
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
                
                // Show a subtle loading state
                $('#treatmentList').css('opacity', '0.5');

                $.ajax({
                    url: "{{ route('booking.index') }}",
                    type: 'GET',
                    data: { 
                        category: category, 
                        search: search,
                        is_ajax: 1 // Helper for backend detection
                    },
                    success: function (html) {
                        $('#treatmentList').html(html).css('opacity', '1');
                    },
                    error: function (err) {
                        console.error('AJAX Error:', err);
                        $('#treatmentList').css('opacity', '1');
                        // Optional: trigger a small alert or toast
                    }
                });
            }

            // Use debounce for search to reduce server requests (500ms)
            $('#searchInput').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(loadTreatments, 500);
            });

            $('#categoryFilter').on('change', loadTreatments);
        });
    </script>
@endpush
@endsection