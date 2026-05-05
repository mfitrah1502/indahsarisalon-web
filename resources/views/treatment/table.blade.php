@forelse($treatments as $treatment)
    <tr class="treatment-row" 
        data-name="{{ $treatment->name }}"
        data-category="{{ $treatment->category->name ?? '-' }}"
        data-details='@json($treatment->details)'
        data-image="{{ $treatment->image }}">
        <td class="px-3">
            <div class="d-flex align-items-center">
                <div class="treatment-icon me-3">
                    @if($treatment->image)
                        <img src="https://{{ env('SUPABASE_PROJECT_REF') }}.supabase.co/storage/v1/object/public/{{ env('SUPABASE_BUCKET') }}/{{ $treatment->image }}" 
                             class="rounded-3 shadow-sm" width="50" height="50" style="object-fit:cover;">
                    @else
                        <div class="bg-light rounded-3 d-flex align-items-center justify-content-center" width="50" height="50">
                            <i class="ti ti-photo text-muted fs-4"></i>
                        </div>
                    @endif
                </div>
                <div>
                    <h6 class="mb-0 fw-bold">{{ $treatment->name }}</h6>
                    <small class="text-muted">{{ $treatment->details->count() }} Variasi</small>
                </div>
            </div>
        </td>
        <td>
            <div class="d-flex align-items-center gap-2">
                <span class="category-badge">{{ $treatment->category->name ?? '-' }}</span>
                @if($treatment->category && $treatment->category->name == 'Promo')
                    <span class="promo-tag shadow-sm animate__animated animate__pulse animate__infinite">
                        <i class="ti ti-discount-2 me-1"></i>PROMO
                    </span>
                @endif
            </div>
        </td>
        <td>
            @if($treatment->is_active)
                <span class="badge bg-light-success text-success border border-success border-opacity-10 px-3 rounded-pill">
                    <i class="ti ti-circle-check me-1"></i> Aktif
                </span>
            @else
                <span class="badge bg-light-danger text-danger border border-danger border-opacity-10 px-3 rounded-pill">
                    <i class="ti ti-circle-x me-1"></i> Non-aktif
                </span>
            @endif
        </td>
        <td>
            <span class="fw-bold text-dark">
                @php
                    $minPrice = $treatment->details->min('price') ?? 0;
                    $maxPrice = $treatment->details->max('price') ?? 0;
                @endphp
                @if($minPrice != $maxPrice)
                    Rp {{ number_format($minPrice, 0, ',', '.') }} - Rp {{ number_format($maxPrice, 0, ',', '.') }}
                @else
                    Rp {{ number_format($minPrice, 0, ',', '.') }}
                @endif
            </span>
        </td>
        <td class="text-end px-3">
            <div class="d-flex justify-content-end gap-2">
                <button class="btn btn-light action-btn view-detail text-info" title="Lihat Detail">
                    <i class="ti ti-eye fs-5"></i>
                </button>
                <a href="{{ route('treatment.edit', $treatment->id) }}" class="btn btn-light action-btn text-warning" title="Edit">
                    <i class="ti ti-edit fs-5"></i>
                </a>
                <form action="{{ route('treatment.destroy', $treatment->id) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-light action-btn text-danger" title="Hapus" onclick="return confirm('Hapus treatment ini?')">
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
                <i class="ti ti-search fs-1 text-muted mb-3 d-block"></i>
                <h5 class="text-muted">Tidak ada treatment yang ditemukan</h5>
                <p class="small text-muted">Coba ubah filter atau kata kunci pencarian Anda.</p>
            </div>
        </td>
    </tr>
@endforelse