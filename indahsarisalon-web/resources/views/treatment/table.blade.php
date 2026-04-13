@forelse($treatments as $index => $treatment)
    <tr class="treatment-row" data-name="{{ $treatment->name }}"
        data-category="{{ $treatment->category ? $treatment->category->name : 'Empty'}}"
        data-promo="{{ $treatment->is_promo ? $treatment->promo_type . ' ' . $treatment->promo_value : 'Tidak ada' }}"
        data-details='@json($treatment->details)'
        data-image="{{ $treatment->image ?? ''}}">

        <td class="ps-3">{{ $index + 1 }}</td>
        <td>
            <div class="d-flex align-items-center">
                <div class="avatar bg-light-primary text-primary rounded me-2 d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                    <i class="ti ti-massage"></i>
                </div>
                <span class="fw-medium text-dark">{{ $treatment->name }}</span>
            </div>
        </td>
        <td class="text-muted">{{ $treatment->category ? $treatment->category->name : 'Empty' }}</td>

        <td class="text-start">
            <span class="fw-medium text-dark">Rp {{ number_format($treatment->details->min('price') ?? 0, 0, ',', '.') }}</span>
        </td>

        <td class="text-center">
            @if($treatment->is_promo)
                <span class="badge bg-light-warning text-warning rounded-pill px-3">{{ $treatment->promo_type . ' ' . $treatment->promo_value }}</span>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>

        <td class="pe-3 text-center">
            <div class="d-flex justify-content-center gap-2">
                <button class="btn btn-icon btn-light-info rounded-circle shadow-none view-detail" data-bs-toggle="tooltip" title="Lihat">
                    <i class="ti ti-eye"></i>
                </button>

                <a href="{{ route('treatment.edit', $treatment->id) }}" class="btn btn-icon btn-light-warning rounded-circle shadow-none" data-bs-toggle="tooltip" title="Edit">
                    <i class="ti ti-edit"></i>
                </a>

                <form action="{{ route('treatment.destroy', $treatment->id) }}" method="POST" style="display:inline-block;" class="delete-treatment-form">
                    @csrf
                    @method('DELETE')

                    <button type="button" class="btn btn-icon btn-light-danger rounded-circle shadow-none btn-delete-treatment" data-bs-toggle="tooltip" title="Hapus">
                        <i class="ti ti-trash"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center">
            Tidak ada data yang ditemukan
        </td>
    </tr>
@endforelse