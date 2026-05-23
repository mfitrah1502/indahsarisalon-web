@extends('layout.dashboard')

@section('title', 'Laporan Pengeluaran')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Laporan Pengeluaran</h3>
                    <p class="text-muted mb-0">Kelola dan pantau biaya operasional salon.</p>
                </div>
                <div class="d-flex gap-3 align-items-center">
                    <div class="card bg-danger text-white border-0 shadow-sm rounded-4 mb-0">
                        <div class="card-body py-2 px-4">
                            <small class="opacity-75" id="total-summary-label">Total Terfilter</small>
                            <h4 class="mb-0 fw-bold" id="total-summary-amount">Rp
                                {{ number_format($totalPengeluaran, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-danger rounded-pill px-4 shadow-sm" id="btnResetPengeluaran">
                        <i class="ti ti-trash me-1"></i> Reset Pengeluaran
                    </button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal"
                        data-bs-target="#modalTambahPengeluaran">
                        <i class="ti ti-plus me-1"></i> Tambah Pengeluaran
                    </button>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="ti ti-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-transparent border-0 pt-4 px-4">
                            <h5 class="mb-0 fw-bold">{{ $chartTitle }}</h5>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div id="expense-chart" style="min-height: 250px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <form action="{{ route('keuangan.pengeluaran') }}" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control border-0 bg-light"
                                value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Tanggal Selesai</label>
                            <input type="date" name="end_date" class="form-control border-0 bg-light"
                                value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary rounded-pill px-4 flex-grow-1">
                                    <i class="ti ti-filter me-1"></i> Filter
                                </button>
                                <button type="button" class="btn btn-outline-success rounded-pill px-4"
                                    data-bs-toggle="modal" data-bs-target="#modalProfit">
                                    <i class="ti ti-chart-pie me-1"></i> Lihat Profit
                                </button>
                                <a href="{{ route('keuangan.pengeluaran') }}" class="btn btn-light rounded-pill px-4">
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
                                <div
                                    class="list-group-item border-0 d-flex justify-content-between align-items-center px-0 py-3">
                                    <div>
                                        <h6 class="mb-0 fw-bold">Total Pemasukan</h6>
                                        <small class="text-muted">Seluruh pendapatan salon</small>
                                    </div>
                                    <span class="text-success fw-bold fs-5">Rp
                                        {{ number_format($grandTotalPemasukan, 0, ',', '.') }}</span>
                                </div>
                                <div
                                    class="list-group-item border-0 d-flex justify-content-between align-items-center px-0 py-3">
                                    <div>
                                        <h6 class="mb-0 fw-bold">Total Pengeluaran</h6>
                                        <small class="text-muted">Seluruh biaya operasional</small>
                                    </div>
                                    <span class="text-danger fw-bold fs-5">Rp
                                        {{ number_format($grandTotalPengeluaran, 0, ',', '.') }}</span>
                                </div>
                                <div
                                    class="list-group-item border-0 d-flex justify-content-between align-items-center px-0 py-4 mt-2 bg-light rounded-3 px-3">
                                    <div>
                                        <h5 class="mb-0 fw-bold text-primary">Profit Bersih</h5>
                                        <small class="text-muted">Keuntungan bersih saat ini</small>
                                    </div>
                                    <span class="text-primary fw-bold fs-4">Rp
                                        {{ number_format($grandProfit, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="{{ route('keuangan.profit.export') }}" class="btn btn-primary rounded-pill py-2">
                                    <i class="ti ti-download me-1"></i> Download Laporan (PDF)
                                </a>
                                <button type="button" class="btn btn-light rounded-pill py-2"
                                    data-bs-dismiss="modal">Tutup</button>
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
                                    <th class="border-0 py-3 text-muted small fw-bold">KATEGORI</th>
                                    <th class="border-0 py-3 text-muted small fw-bold">KETERANGAN</th>
                                    <th class="border-0 px-4 py-3 text-muted small fw-bold text-end">JUMLAH</th>
                                    <th class="border-0 px-4 py-3 text-muted small fw-bold text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pengeluaran as $item)
                                                        <tr>
                                                            <td class="px-4 py-3">
                                                                <div class="fw-medium text-dark">
                                                                    {{ \Carbon\Carbon::parse($item->expense_date)->format('d M Y') }}</div>
                                                            </td>
                                                            <td>
                                                                <span class="badge rounded-pill {{ 
                                                                        $item->category == 'employee_salary' ? 'bg-light-primary text-primary' :
                                    ($item->category == 'maintenance' ? 'bg-light-warning text-warning' : 'bg-light-secondary text-secondary') 
                                                                    }}">
                                                                    {{ $item->category }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $item->description ?? '-' }}</td>
                                                            <td class="px-4 text-end fw-bold text-danger">
                                                                Rp {{ number_format($item->amount, 0, ',', '.') }}
                                                            </td>
                                                            <td class="px-4 text-center">
                                                                <button type="button" class="btn btn-light btn-sm text-danger btn-delete-expense" data-id="{{ $item->id }}" title="Hapus">
                                                                    <i class="ti ti-trash fs-5"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="ti ti-receipt-off fs-1 d-block mb-2"></i>
                                            Belum ada data pengeluaran.
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

    <!-- Modal Tambah Pengeluaran -->
    <div class="modal fade" id="modalTambahPengeluaran" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4" style="background-color: #FCEBED;">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="fw-bold mb-0">Tambah Pengeluaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('keuangan.pengeluaran.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <div class="form-floating mb-3">
                                <select name="category" class="form-select border-0 shadow-sm" id="categorySelect"
                                    style="border-radius: 12px;" required>
                                    <option value="employee_salary">Gaji Karyawan</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="others">Others</option>
                                </select>
                                <label for="categorySelect" class="text-muted small">Kategori</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-group mb-3">
                                <input type="number" name="amount" class="form-control border-0 shadow-sm py-3"
                                    placeholder="Jumlah (Rp)" style="border-radius: 12px;" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-group mb-3">
                                <input type="date" name="expense_date" class="form-control border-0 shadow-sm py-3"
                                    value="{{ date('Y-m-d') }}" style="border-radius: 12px;" required>
                            </div>
                        </div>
                        <div class="mb-0">
                            <div class="form-group mb-0">
                                <textarea name="description" class="form-control border-0 shadow-sm"
                                    placeholder="Keterangan (Opsional)" style="border-radius: 12px;" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0 d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-link text-primary text-decoration-none px-0"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm"
                            style="background-color: #D96A79; border-color: #D96A79;">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Hidden Forms for Expense Management -->
    <form id="delete-expense-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <form id="reset-expenses-form" action="{{ route('keuangan.pengeluaran.reset') }}" method="POST" style="display: none;">
        @csrf
    </form>
@endsection

@push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handle Single Delete
            $(document).on('click', '.btn-delete-expense', function () {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus Pengeluaran?',
                    text: 'Apakah Anda yakin ingin menghapus data pengeluaran ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#D96A79',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'rounded-4 border-0 shadow-lg'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('delete-expense-form');
                        form.action = "{{ route('keuangan.pengeluaran.destroy', ':id') }}".replace(':id', id);
                        form.submit();
                    }
                });
            });

            // Handle Reset All
            const btnReset = document.getElementById('btnResetPengeluaran');
            if (btnReset) {
                btnReset.addEventListener('click', function () {
                    Swal.fire({
                        title: 'Reset Semua Pengeluaran?',
                        text: 'Apakah Anda yakin ingin menghapus SEMUA data pengeluaran? Tindakan ini tidak dapat dibatalkan!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Reset Semua',
                        cancelButtonText: 'Batal',
                        customClass: {
                            popup: 'rounded-4 border-0 shadow-lg'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('reset-expenses-form').submit();
                        }
                    });
                });
            }

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
                colors: ['#F44336'],
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

            var chart = new ApexCharts(document.querySelector("#expense-chart"), options);
            chart.render();
        });
    </script>
@endpush