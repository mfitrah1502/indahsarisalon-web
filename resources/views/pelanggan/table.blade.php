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
                    <h6 class="mb-0 fw-bold">{{ $pelanggan->name }}</h6>
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
            @php
                $statusClass = $pelanggan->status === 'aktif' ? 'bg-light-success text-success' : 'bg-light-secondary text-secondary';
            @endphp
            <span class="badge {{ $statusClass }} rounded-pill px-3">{{ ucfirst($pelanggan->status) }}</span>
        </td>
        <td class="text-end px-3">
            <div class="d-flex justify-content-end gap-2">
                <button class="btn btn-light action-btn view-detail text-info" 
                        data-name="{{ $pelanggan->name }}"
                        data-username="{{ $pelanggan->username }}"
                        data-email="{{ $pelanggan->email }}"
                        data-phone="{{ $pelanggan->phone }}"
                        data-status="{{ $pelanggan->status }}"
                        title="Lihat Detail">
                    <i class="ti ti-eye fs-5"></i>
                </button>
                <a href="{{ route('pelanggan.edit', $pelanggan->id) }}" class="btn btn-light action-btn text-warning" title="Edit">
                    <i class="ti ti-edit fs-5"></i>
                </a>
                <form action="{{ route('pelanggan.destroy', $pelanggan->id) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-light action-btn text-danger" title="Hapus" onclick="return confirm('Hapus pelanggan ini?')">
                        <i class="ti ti-trash fs-5"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="text-center py-5">
            <div class="py-4">
                <i class="ti ti-users fs-1 text-muted mb-3 d-block"></i>
                <h5 class="text-muted">Tidak ada pelanggan yang ditemukan</h5>
                <p class="small text-muted">Coba ubah filter atau kata kunci pencarian Anda.</p>
            </div>
        </td>
    </tr>
@endforelse
