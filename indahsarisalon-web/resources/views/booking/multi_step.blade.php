@extends('layout.dashboard')

@section('title', 'Booking Appointment')

<link rel="icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" />
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Book an Appointment</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('booking.store') }}" method="POST" id="bookingForm">
                        @csrf

                        {{-- Step 2: Pilih Stylist --}}
                        <h5>Step 2: Pilih Stylist</h5>
                        <div class="mb-3">
                            <select name="stylist_id" class="form-select" required>
                                <option value="">-- Pilih Stylist --</option>
                                @foreach($stylists as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Step 3: Tanggal & Waktu --}}
                        <h5>Step 3: Waktu Reservasi</h5>
                        <div class="mb-3">
                            <input type="date" name="reservation_date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <input type="time" name="reservation_time" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Lanjut ke Ringkasan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script>
        layout_change('light');
        font_change('Roboto');
    </script>
@endsection