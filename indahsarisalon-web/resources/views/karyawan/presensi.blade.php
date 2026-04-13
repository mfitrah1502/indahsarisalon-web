@extends('layout.dashboard')

@section('title', 'Riwayat Presensi ' . $karyawan->name)

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Riwayat Presensi {{ $karyawan->name }}</h4>
                    <a href="{{ route('karyawan.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Jam Keluar</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($presensi as $index => $p)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $p->tanggal }}</td>
                                    <td>{{ $p->jam_masuk ?? '-' }}</td>
                                    <td>{{ $p->jam_keluar ?? '-' }}</td>
                                    <td>
                                        @if(!$p->jam_masuk && !$p->jam_keluar)
                                            Tidak Absen
                                        @elseif(!$p->jam_masuk)
                                            Tidak Masuk
                                        @elseif(!$p->jam_keluar)
                                            Tidak Keluar
                                        @else
                                            Hadir
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection