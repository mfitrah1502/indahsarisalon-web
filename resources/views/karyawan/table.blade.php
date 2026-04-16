@forelse($karyawans as $karyawan)
    <tr class="employee-row">
        <td class="px-3">
            <div class="d-flex align-items-center">
                <div class="employee-avatar me-3">
                    @if($karyawan->avatar)
                        <img src="{{ $karyawan->avatar_url }}" 
                             class="rounded-circle shadow-sm" width="45" height="45" style="object-fit:cover;">
                    @else
                        <div class="bg-light-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="ti ti-user text-primary fs-4"></i>
                        </div>
                    @endif
                </div>
                <div>
                    <h6 class="mb-0 fw-bold">{{ $karyawan->name }}</h6>
                    <small class="text-muted">@<span></span>{{ $karyawan->username }}</small>
                </div>
            </div>
        </td>
        <td>
            <div class="d-flex flex-column">
                <span class="text-dark fw-medium small"><i class="ti ti-mail me-1 text-muted"></i>{{ $karyawan->email }}</span>
                <span class="text-muted small"><i class="ti ti-phone me-1"></i>{{ $karyawan->phone ?? '-' }}</span>
            </div>
        </td>
        <td>
            @php
                $roleClass = $karyawan->role === 'admin' ? 'bg-light-danger text-danger' : 'bg-light-primary text-primary';
            @endphp
            <span class="badge {{ $roleClass }} rounded-pill px-3">{{ ucfirst($karyawan->role) }}</span>
        </td>
        <td>
            @php
                $statusClass = $karyawan->status === 'aktif' ? 'bg-light-success text-success' : 'bg-light-secondary text-secondary';
            @endphp
            <span class="badge {{ $statusClass }} rounded-pill px-3">{{ ucfirst($karyawan->status) }}</span>
        </td>
        <td class="text-end px-3">
            <div class="d-flex justify-content-end gap-2">
                <button class="btn btn-light action-btn lihat-absensi text-info" data-id="{{ $karyawan->id }}" title="Riwayat Presensi">
                    <i class="ti ti-calendar-event fs-5"></i>
                </button>
                <a href="{{ route('karyawan.edit', $karyawan->id) }}" class="btn btn-light action-btn text-warning" title="Edit">
                    <i class="ti ti-edit fs-5"></i>
                </a>
                <form action="{{ route('karyawan.destroy', $karyawan->id) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-light action-btn text-danger" title="Hapus" onclick="return confirm('Hapus karyawan ini?')">
                        <i class="ti ti-trash fs-5"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center py-5">
            <div class="py-4">
                <i class="ti ti-users fs-1 text-muted mb-3 d-block"></i>
                <h5 class="text-muted">Tidak ada karyawan yang ditemukan</h5>
                <p class="small text-muted">Coba ubah filter atau kata kunci pencarian Anda.</p>
            </div>
        </td>
    </tr>
@endforelse
