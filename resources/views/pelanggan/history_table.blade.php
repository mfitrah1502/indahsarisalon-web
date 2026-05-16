@if($bookings->isEmpty())
    <div class="text-center py-4">
        <i class="ti ti-receipt-2 fs-1 text-muted mb-2 d-block"></i>
        <h6 class="text-muted">Belum ada riwayat booking</h6>
    </div>
@else
    <div class="table-responsive">
        <table class="table table-sm table-borderless table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th class="small text-muted fw-bold py-2 rounded-start">Tanggal</th>
                    <th class="small text-muted fw-bold py-2">Treatment</th>
                    <th class="small text-muted fw-bold py-2">Status</th>
                    <th class="small text-muted fw-bold py-2 text-end rounded-end">Total Biaya</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                <tr>
                    <td class="small">{{ \Carbon\Carbon::parse($booking->reservation_datetime)->format('d M Y, H:i') }}</td>
                    <td class="small">
                        @foreach($booking->details as $detail)
                            {{ $detail->treatmentDetail->treatment->name }}@if(!$loop->last), @endif
                        @endforeach
                    </td>
                    <td>
                        @php
                            $statusClass = match($booking->status) {
                                'berhasil' => 'bg-light-success text-success',
                                'pending' => 'bg-light-warning text-warning',
                                'dibatalkan' => 'bg-light-danger text-danger',
                                default => 'bg-light-secondary text-secondary',
                            };
                        @endphp
                        <span class="badge {{ $statusClass }} rounded-pill" style="font-size: 0.7rem;">{{ ucfirst($booking->status) }}</span>
                    </td>
                    <td class="small fw-bold text-end">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-top">
                    <td colspan="3" class="text-end fw-bold pt-3 small">Total Keseluruhan (Berhasil):</td>
                    <td class="text-end fw-bold text-primary pt-3 small">Rp {{ number_format($bookings->where('status', 'berhasil')->sum('total_price'), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
@endif
