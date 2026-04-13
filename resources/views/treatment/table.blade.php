@forelse($treatments as $index => $treatment)
    <tr data-name="{{ $treatment->name }}"
        data-category="{{ $treatment->category ? $treatment->category->name : 'Kosong' }}"
        data-promo="{{ $treatment->is_promo ? $treatment->promo_type . ' ' . $treatment->promo_value : 'Tidak ada' }}"
        data-details='@json($treatment->details)'>

        <td>{{ $index + 1 }}</td>
        <td>{{ $treatment->name }}</td>
        <td>{{ $treatment->category ? $treatment->category->name : 'Kosong' }}</td>
        <td>
            Rp {{ number_format($treatment->details->min('price') ?? 0, 0, ',', '.') }}
        </td>
        <td>
            {{ $treatment->is_promo ? $treatment->promo_type . ' ' . $treatment->promo_value : '-' }}
        </td>
        <td>
            <button class="btn btn-sm btn-info view-detail">Lihat</button>
            <a href="{{ route('treatment.edit', $treatment->id) }}" class="btn btn-sm btn-warning">Edit</a>
            <form action="{{ route('treatment.destroy', $treatment->id) }}" method="POST" style="display:inline-block;">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus treatment ini?')">
                    Hapus
                </button>
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" style="text-align:center;">
            Tidak ada data yang ditemukan
        </td>
    </tr>
@endforelse