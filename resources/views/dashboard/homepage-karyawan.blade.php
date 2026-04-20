@extends('layout.dashboard')

@section('title', 'Dashboard Karyawan | Indah Sari Salon')


@section('content')
    <div class="row g-4">
        <!-- WELCOME HEADER -->
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden"
                style="background: linear-gradient(135deg, #EA8290 0%, #D96A79 100%);">
                <div class="card-body p-4 position-relative">
                    <div class="row align-items-center">
                        <div class="col-md-8 text-white">
                            <h2 class="text-white fw-bold mb-1">Selamat
                                {{ \Carbon\Carbon::now()->format('H') < 12 ? 'Pagi' : (\Carbon\Carbon::now()->format('H') < 15 ? 'Siang' : (\Carbon\Carbon::now()->format('H') < 18 ? 'Sore' : 'Malam')) }},
                                {{ Auth::user()->name }}! 👋
                            </h2>
                            <p class="opacity-75 mb-0">Senang melihatmu kembali. Mari berikan layanan terbaik untuk
                                pelanggan hari ini.</p>
                        </div>
                        <div class="col-md-4 text-end d-none d-md-block">
                            <img src="{{ Auth::user()->avatar_url }}" alt="user-image"
                                class="rounded-circle border border-white border-4 shadow-sm"
                                style="width: 80px; height: 80px; object-fit: cover;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ATTENDANCE WIDGET -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 text-dark"><i class="ti ti-clock me-2 text-primary"></i>Presensi Kehadiran</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h1 id="live-clock" class="fw-bold text-dark display-5 mb-0">00:00:00</h1>
                        <p class="text-muted small">{{ date('l, d F Y') }}</p>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <button id="btn-absen-masuk"
                                class="btn btn-primary w-100 d-flex flex-column align-items-center py-3 {{ $absensi && $absensi->jam_masuk ? 'disabled' : '' }}">
                                <i class="ti ti-login fs-2 mb-2"></i>
                                <span>Presensi Hadir</span>
                            </button>
                        </div>
                        <div class="col-6">
                            <button id="btn-absen-keluar"
                                class="btn btn-danger w-100 d-flex flex-column align-items-center py-3 {{ !$absensi || $absensi->jam_keluar ? 'disabled' : '' }}">
                                <i class="ti ti-logout fs-2 mb-2"></i>
                                <span>Presensi Keluar</span>
                            </button>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-light rounded-3 border border-dashed">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Jam Masuk</span>
                            <span class="badge bg-light-success text-success"
                                id="status-masuk">{{ $absensi && $absensi->jam_masuk ? \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i') : '--:--' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Jam Keluar</span>
                            <span class="badge bg-light-danger text-danger"
                                id="status-keluar">{{ $absensi && $absensi->jam_keluar ? \Carbon\Carbon::parse($absensi->jam_keluar)->format('H:i') : '--:--' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BOOKING FOCUS WIDGET -->
        <div class="col-md-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-dark"><i class="ti ti-calendar-event me-2 text-warning"></i>Dashboard Antrean Salon
                    </h5>
                    <a href="{{ route('karyawan.bookings.index') }}" class="btn btn-link btn-sm text-primary p-0">Lihat
                        Semua <i class="ti ti-arrow-right"></i></a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr style="font-size: 0.75rem;">
                                    <th class="ps-4">PELANGGAN</th>
                                    <th>LAYANAN</th>
                                    <th>WAKTU</th>
                                    <th class="text-end pe-4">STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($todayBookings as $booking)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-light-primary text-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                                    style="width: 35px; height: 35px;">
                                                    {{ substr($booking->customer_name, 0, 1) }}
                                                </div>
                                                <span class="fw-bold text-dark small text-truncate"
                                                    style="max-width: 100px;">{{ $booking->customer_name }}</span>
                                            </div>
                                        </td>
                                        <td><span class="small text-muted">{{ $booking->treatment->name }}</span></td>
                                        <td><span class="badge bg-light-warning text-dark small"><i
                                                    class="ti ti-clock me-1"></i>{{ \Carbon\Carbon::parse($booking->reservation_datetime)->format('H:i') }}</span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <span class="badge bg-danger rounded-pill px-2 py-1"
                                                style="font-size: 0.65rem;">Perlu Diproses</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <p class="text-muted small">Tidak ada antrean mendesak untuk saat ini.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Live Clock Logic
            function updateClock() {
                const now = new Date();
                const h = String(now.getHours()).padStart(2, '0');
                const m = String(now.getMinutes()).padStart(2, '0');
                const s = String(now.getSeconds()).padStart(2, '0');
                document.getElementById('live-clock').textContent = `${h}:${m}:${s}`;
            }
            setInterval(updateClock, 1000);
            updateClock();

            // Attendance Logic
            document.getElementById('btn-absen-masuk')?.addEventListener('click', function () {
                handleAttendance("{{ route('absensi.masuk') }}", 'masuk');
            });

            document.getElementById('btn-absen-keluar')?.addEventListener('click', function () {
                handleAttendance("{{ route('absensi.keluar') }}", 'keluar');
            });

            function handleAttendance(url, type) {
                Swal.fire({
                    title: 'Konfirmasi Presensi',
                    text: `Apakah Anda ingin mencatat presensi ${type} sekarang?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Catat!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(url, {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Accept": "application/json",
                            },
                        })
                            .then(res => res.json())
                            .then(res => {
                                if (res.success) {
                                    Swal.fire('Berhasil!', res.message, 'success').then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('Gagal!', res.message, 'error');
                                }
                            })
                            .catch(err => {
                                Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
                            });
                    }
                });
            }
        </script>
    @endpush
@endsection