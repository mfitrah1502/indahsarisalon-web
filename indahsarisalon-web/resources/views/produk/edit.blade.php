@extends('layout.dashboard')

@section('title', 'Edit Produk')
<!-- [Favicon] icon -->
<link rel="icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon" />
<!-- [Google Font] Family -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap"
    id="main-font-link" />
<!-- [phosphor Icons] https://phosphoricons.com/ -->
<link rel="stylesheet" href="{{ asset('assets/fonts/phosphor/duotone/style.css') }}" />
<!-- [Tabler Icons] https://tablericons.com -->
<link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}" />
<!-- [Feather Icons] https://feathericons.com -->
<link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}" />
<!-- [Font Awesome Icons] https://fontawesome.com/icons -->
<link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}" />
<!-- [Material Icons] https://fonts.google.com/icons -->
<link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}" />
<!-- [Template CSS Files] -->
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link" />
<link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}" />


@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-header">
                    <h4>Edit Produk</h4>
                </div>

                <div class="card-body">

                    {{-- Error Validation --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('produk.update', $produk->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label>Nama Produk</label>
                            <input type="text" name="nama" class="form-control" value="{{ old('nama', $produk->nama) }}">
                        </div>

                        <div class="mb-3">
                            <label>Harga</label>
                            <input type="number" name="harga" class="form-control"
                                value="{{ old('harga', $produk->harga) }}">
                        </div>

                        <div class="mb-3">
                            <label>Stok</label>
                            <input type="number" name="stok" class="form-control" value="{{ old('stok', $produk->stok) }}">
                        </div>

                        <button type="submit" class="btn btn-warning">
                            Update
                        </button>
                        <a href="{{ route('produk.index') }}" class="btn btn-secondary">
                            Kembali
                        </a>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection