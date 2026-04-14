@forelse($treatments as $treatment)
    <tr class="treatment-row" 
        data-name="{{ $treatment->name }}"
        data-category="{{ $treatment->category->name ?? '-' }}"
        data-promo="{{ $treatment->is_promo ? ($treatment->promo_type == 'percent' ? $treatment->promo_value.'%' : 'Rp '.number_format($treatment->promo_value)) : '-' }}"
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
            <span class="category-badge">{{ $treatment->category->name ?? '-' }}</span>
        </td>
        <td>
            <span class="fw-bold text-dark">Rp {{ number_format($treatment->details->min('price') ?? 0, 0, ',', '.') }}</span>
        </td>
        <td>
            @if($treatment->is_promo)
                <span class="promo-tag">
                    <i class="ti ti-discount-2 me-1"></i>
                    {{ $treatment->promo_type == 'percent' ? $treatment->promo_value.'%' : 'Rp '.number_format($treatment->promo_value) }}
                </span>
            @else
                <span class="text-muted small">-</span>
            @endif
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