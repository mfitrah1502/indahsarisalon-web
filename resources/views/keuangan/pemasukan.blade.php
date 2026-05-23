@extends('layout.dashboard')

@section('title', 'Laporan Pemasukan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Laporan Pemasukan</h3>
                <p class="text-muted mb-0">Riwayat pendapatan dari layanan salon.</p>
            </div>
            <div class="card bg-primary text-white border-0 shadow-sm rounded-4">
                <div class="card-body py-2 px-4">
                    <small class="opacity-75" id="total-summary-label">Total Terfilter</small>
                    <h4 class="mb-0 fw-bold" id="total-summary-amount">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h5 class="mb-0 fw-bold">{{ $chartTitle }}</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div id="income-chart" style="min-height: 250px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <form action="{{ route('keuangan.pemasukan') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control border-0 bg-light" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Tanggal Selesai</label>
                        <input type="date" name="end_date" class="form-control border-0 bg-light" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 flex-grow-1">
                                <i class="ti ti-filter me-1"></i> Filter
                            </button>
                            <button type="button" class="btn btn-outline-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalProfit">
                                <i class="ti ti-chart-pie me-1"></i> Lihat Profit
                            </button>
                            <a href="{{ route('keuangan.pemasukan') }}" class="btn btn-light rounded-pill px-4">
                                <i class="ti ti-refresh me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Profit -->
        <div class="modal fade" id="modalProfit" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow rounded-4">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Ringkasan Profit Keseluruhan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="text-center mb-4">
                            <p class="text-muted mb-0 small">Statistik seluruh data tanpa filter</p>
                        </div>
                        <div class="list-group list-group-flush mb-4">
                            <div class="list-group-item border-0 d-flex justify-content-between align-items-center px-0 py-3">
                                <div>
                                    <h6 class="mb-0 fw-bold">Total Pemasukan</h6>
                                    <small class="text-muted">Seluruh pendapatan salon</small>
                                </div>
                                <span class="text-success fw-bold fs-5">Rp {{ number_format($grandTotalPemasukan, 0, ',', '.') }}</span>
                            </div>
                            <div class="list-group-item border-0 d-flex justify-content-between align-items-center px-0 py-3">
                                <div>
                                    <h6 class="mb-0 fw-bold">Total Pengeluaran</h6>
                                    <small class="text-muted">Seluruh biaya operasional</small>
                                </div>
                                <span class="text-danger fw-bold fs-5">Rp {{ number_format($grandTotalPengeluaran, 0, ',', '.') }}</span>
                            </div>
                            <div class="list-group-item border-0 d-flex justify-content-between align-items-center px-0 py-4 mt-2 bg-light rounded-3 px-3">
                                <div>
                                    <h5 class="mb-0 fw-bold text-primary">Profit Bersih</h5>
                                    <small class="text-muted">Keuntungan bersih saat ini</small>
                                </div>
                                <span class="text-primary fw-bold fs-4">Rp {{ number_format($grandProfit, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="{{ route('keuangan.profit.export') }}" class="btn btn-primary rounded-pill py-2">
                                <i class="ti ti-download me-1"></i> Download Laporan (PDF)
                            </a>
                            <button type="button" class="btn btn-light rounded-pill py-2" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 px-4 py-3 text-muted small fw-bold">TANGGAL</th>
                                <th class="border-0 py-3 text-muted small fw-bold">KODE BOOKING</th>
                                <th class="border-0 py-3 text-muted small fw-bold">PELANGGAN</th>
                                <th class="border-0 py-3 text-muted small fw-bold">LAYANAN</th>
                                <th class="border-0 px-4 py-3 text-muted small fw-bold text-end">JUMLAH</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pemasukan as $item)
                                <tr>
                                    <td class="px-4 py-3" style="min-width: 160px;">
                                        <div class="d-flex flex-column gap-2">
                                            <div class="small" title="Tanggal Reservasi">
                                                <span class="badge bg-light-primary text-primary px-2 py-0.5" style="font-size: 0.65rem;"><i class="ti ti-calendar-event me-1"></i>Reservasi:</span>
                                                <div class="fw-bold text-dark mt-0.5">{{ \Carbon\Carbon::parse($item->reservation_datetime)->format('d M Y') }} - {{ \Carbon\Carbon::parse($item->reservation_datetime)->format('H:i') }}</div>
                                            </div>
                                            <div class="small" title="Tanggal Transaksi">
                                                <span class="badge bg-light-secondary text-secondary px-2 py-0.5" style="font-size: 0.65rem;"><i class="ti ti-receipt me-1"></i>Transaksi:</span>
                                                <div class="text-muted mt-0.5">{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }} - {{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light-primary text-primary">#{{ $item->id }}</span></td>
                                    <td>
                                        <div class="fw-bold">{{ $item->user->name ?? 'Walk-in' }}</div>
                                        <small class="text-muted">{{ $item->user->phone ?? '-' }}</small>
                                    </td>
                                    <td>{{ $item->treatment->name ?? 'Layanan' }}</td>
                                    <td class="px-4 text-end fw-bold text-success">
                                        Rp {{ number_format($item->total_price, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="ti ti-receipt-off fs-1 d-block mb-2"></i>
                                        Belum ada data pemasukan.
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
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var options = {
            series: [{
                name: 'Total Akumulasi',
                data: {!! json_encode($chartData) !!}
            }],
            chart: {
                type: 'area',
                height: 300,
                toolbar: { show: false },
                fontFamily: "'Inter', sans-serif"
            },
            colors: ['#4CAF50'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            xaxis: {
                categories: {!! json_encode($chartLabels) !!},
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return "Rp " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "Rp " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    }
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.1,
                    stops: [0, 90, 100]
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#income-chart"), options);
        chart.render();
    });
</script>
@endpush
