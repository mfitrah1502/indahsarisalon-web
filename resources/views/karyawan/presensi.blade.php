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
                                <th>Status Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($presensi as $index => $p)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d F Y') }}</td>
                                    <td>
                                        @if($p->status == 'hadir')
                                            <span class="badge bg-light-success text-success px-3 rounded-pill">Presence</span>
                                        @elseif($p->status == 'off')
                                            <span class="badge bg-light-danger text-danger px-3 rounded-pill">Off Work</span>
                                        @else
                                            <span class="badge bg-light-secondary text-secondary px-3 rounded-pill">{{ ucfirst($p->status ?? '-') }}</span>
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