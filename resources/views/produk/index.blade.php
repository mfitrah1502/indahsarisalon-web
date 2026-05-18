@extends('layout.dashboard')

@section('title', 'Produk')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Daftar Produk</h4>
                    <a href="{{ route('produk.create') }}" class="btn btn-primary">
                        Tambah Produk
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                <div class="card-body">
                    <div class="mb-3">
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari produk..."
                            value="{{ request('search') }}">
                    </div>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($produks as $produk)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $produk->nama }}</td>
                                    <td>{{ $produk->harga }}</td>
                                    <td>{{ $produk->stok }}</td>
                                    <td>
                                        <a href="{{ route('produk.edit', $produk->id) }}"
                                            class="btn btn-warning btn-sm">Edit</a>

                                        <form action="{{ route('produk.destroy', $produk->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm btn-delete-produk"
                                                data-name="{{ $produk->nama }}">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            $('#searchInput').on('keyup', function () {
                let query = $(this).val();
 
                $.ajax({
                    url: "{{ route('produk.index') }}", // route yang sama
                    type: 'GET',
                    data: { search: query },
                    success: function (data) {
                        // Ambil tbody dari response dan ganti tabel
                        let tbody = $(data).find('tbody').html();
                        $('tbody').html(tbody);
                    }
                });
            });

            // SweetAlert2 for Delete Confirmation
            $(document).on('click', '.btn-delete-produk', function (e) {
                e.preventDefault();
                const form = $(this).closest('form');
                const name = $(this).data('name');

                Swal.fire({
                    title: 'Hapus Produk?',
                    text: `Apakah Anda yakin ingin menghapus produk "${name}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'rounded-4 border-0 shadow-lg'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
@endsection