@forelse($pelanggans as $pelanggan)
    <tr class="customer-row">
        <td class="px-3">
            <div class="d-flex align-items-center">
                <div class="customer-avatar me-3">
                    @if($pelanggan->avatar)
                        <img src="{{ $pelanggan->avatar_url }}" 
                             class="rounded-circle shadow-sm" width="45" height="45" style="object-fit:cover;">
                    @else
                        <div class="bg-light-info rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="ti ti-user text-info fs-4"></i>
                        </div>
                    @endif
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h6 class="mb-0 fw-bold">{{ $pelanggan->name }}</h6>
                        @php
                            $tier = $pelanggan->tier;
                            $tierStyle = match($tier) {
                                'Platinum' => 'border: 1px solid #6c757d; color: #6c757d; background: transparent; padding: 0.15rem 0.4rem; font-size: 0.65rem;',
                                'Gold' => 'border: 1px solid #ffc107; color: #ffc107; background: transparent; padding: 0.15rem 0.4rem; font-size: 0.65rem;',
                                'Silver' => 'border: 1px solid #adb5bd; color: #adb5bd; background: transparent; padding: 0.15rem 0.4rem; font-size: 0.65rem;',
                                default => 'display: none;',
                            };
                        @endphp
                        <span class="badge rounded-pill" style="{{ $tierStyle }}">{{ $tier }}</span>
                        @if($pelanggan->is_colour_circle_member)
                            <span class="badge rounded-pill" style="border: 1px solid #e83e8c; color: #e83e8c; background: transparent; padding: 0.15rem 0.4rem; font-size: 0.65rem;">Colour Circle</span>
                        @endif
                    </div>
                    <small class="text-muted">@<span></span>{{ $pelanggan->username }}</small>
                </div>
            </div>
        </td>
        <td>
            <div class="d-flex flex-column">
                <span class="text-dark fw-medium small"><i class="ti ti-mail me-1 text-muted"></i>{{ $pelanggan->email }}</span>
                <span class="text-muted small"><i class="ti ti-phone me-1"></i>{{ $pelanggan->phone ?? '-' }}</span>
            </div>
        </td>
        <td>
            <div class="d-flex flex-column">
                <span class="text-uppercase text-muted" style="font-size: 0.65rem; font-weight: 700; letter-spacing: 0.5px;">Lifetime Spend</span>
                <span class="fw-bold" style="color: #e83e8c; font-size: 0.9rem;">Rp {{ number_format($pelanggan->total_spending, 0, ',', '.') }}</span>
            </div>
        </td>
        <td>
            @php
                $statusClass = $pelanggan->status === 'aktif' ? 'bg-light-success text-success' : 'bg-light-secondary text-secondary';
            @endphp
            <span class="badge {{ $statusClass }} rounded-pill px-3">{{ ucfirst($pelanggan->status) }}</span>
        </td>
        <td class="text-end px-3">
            <div class="d-flex justify-content-end gap-2">
                <button class="btn btn-light action-btn view-detail text-info" 
                        data-id="{{ $pelanggan->id }}"
                        data-name="{{ $pelanggan->name }}"
                        data-username="{{ $pelanggan->username }}"
                        data-email="{{ $pelanggan->email }}"
                        data-phone="{{ $pelanggan->phone }}"
                        data-status="{{ $pelanggan->status }}"
                        data-tier="{{ $pelanggan->tier ?? '-' }}"
                        data-has-cc="{{ $pelanggan->is_colour_circle_member ? 'true' : 'false' }}"
                        data-spending="{{ $pelanggan->total_spending ?? 0 }}"
                        data-lasttrx="{{ $pelanggan->last_transaction_at ? date('d M Y, H:i', strtotime($pelanggan->last_transaction_at)) : '-' }}"
                        title="Lihat Detail">
                    <i class="ti ti-eye fs-5"></i>
                </button>
                @if($pelanggan->status !== 'guest')
                <a href="{{ route('pelanggan.edit', $pelanggan->id) }}" class="btn btn-light action-btn text-warning" title="Edit">
                    <i class="ti ti-edit fs-5"></i>
                </a>
                <form action="{{ route('pelanggan.destroy', $pelanggan->id) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-light action-btn text-danger" title="Hapus" onclick="return confirm('Hapus pelanggan ini?')">
                        <i class="ti ti-trash fs-5"></i>
                    </button>
                </form>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center py-5">
            <div class="py-4">
                <i class="ti ti-users fs-1 text-muted mb-3 d-block"></i>
                <h5 class="text-muted">Tidak ada pelanggan yang ditemukan</h5>
                <p class="small text-muted">Coba ubah filter atau kata kunci pencarian Anda.</p>
            </div>
        </td>
    </tr>
@endforelse
