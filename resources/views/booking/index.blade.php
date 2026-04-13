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
                        @foreach($treatments as $treatment)
                            <div class="col-md-4 mb-4">
                                <div class="card treatment-card h-100 border-0 shadow-sm">
                                    @php
                                        $imageUrl = $treatment->image 
                                            ? env('SUPABASE_URL') . '/storage/v1/object/public/' . env('SUPABASE_BUCKET') . '/' . $treatment->image 
                                            : asset('assets/images/no-image.jpg');
                                    @endphp
                                    <img src="{{ $imageUrl }}" class="card-img-top" alt="{{ $treatment->name }}">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $treatment->name }}</h5>
                                        <p class="card-text">
                                            Kategori: {{ $treatment->category->name ?? '-' }} <br>
                                            @foreach($treatment->details as $detail)
                                                - {{ $detail->name }}: {{ $detail->duration }} menit, Rp
                                                {{ number_format($detail->price, 0) }} <br>
                                            @endforeach
                                        </p>
                                        <a href="{{ route('booking.select', $treatment->id) }}" class="btn btn-primary">Pilih
                                            Treatment</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Required JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        layout_change('light');
        font_change('Roboto');
        change_box_container('false');
        layout_caption_change('true');
        layout_rtl_change('false');
        preset_change('preset-1');

        $(document).ready(function () {
            function loadTreatments() {
                let category = $('#categoryFilter').val();
                let search = $('#searchInput').val();

                $.ajax({
                    url: "{{ route('booking.index') }}",
                    type: 'GET',
                    data: { category: category, search: search },
                    success: function (data) {
                        let list = $(data).find('#treatmentList').html();
                        $('#treatmentList').html(list);
                    },
                    error: function (err) {
                        console.log('AJAX Error:', err);
                    }
                });
            }

            $('#searchInput').on('keyup', loadTreatments);
            $('#categoryFilter').on('change', loadTreatments);
        });
    </script>
@endsection