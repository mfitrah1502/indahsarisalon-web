@foreach($treatments as $treatment)
    <div class="col-md-4 mb-4">
        <div class="card treatment-card h-100 border-0 shadow-sm">
            @php
                $imageUrl = $treatment->image 
                    ? env('SUPABASE_URL') . '/storage/v1/object/public/' . env('SUPABASE_BUCKET') . '/' . $treatment->image 
                    : asset('assets/images/no-image.jpg');
            @endphp
            <img src="{{ $imageUrl }}" class="card-img-top" alt="{{ $treatment->name }}">
            <div class="card-body">
                <h5 class="card-title fw-bold text-dark">
                    {{ $treatment->name }}
                    @if($treatment->is_promo)
                        <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">PROMO</span>
                    @endif
                </h5>
                <p class="card-text text-muted mb-3">
                    <span class="badge bg-light-primary text-primary mb-2">{{ $treatment->category->name ?? '-' }}</span><br>
                    <div class="small">
                        @foreach($treatment->details as $detail)
                            @php
                                $originalPrice = $detail->price;
                                $isPromo = $treatment->is_promo;
                                $promoType = $treatment->promo_type;
                                $promoValue = $treatment->promo_value;

                                if ($detail->has_stylist_price) {
                                    $prices = array_filter([(int)$detail->price_senior, (int)$detail->price_junior]);
                                    $minPrice = count($prices) > 0 ? min($prices) : (int)$detail->price;
                                    $maxPrice = count($prices) > 0 ? max($prices) : (int)$detail->price;

                                    if ($isPromo) {
                                        if ($promoType === 'percentage' || $promoType === 'percent') {
                                            $minPrice -= ($minPrice * $promoValue / 100);
                                            $maxPrice -= ($maxPrice * $promoValue / 100);
                                        } else {
                                            $minPrice -= $promoValue;
                                            $maxPrice -= $promoValue;
                                        }
                                    }
                                } else {
                                    $price = $originalPrice;
                                    if ($isPromo) {
                                        if ($promoType === 'percentage' || $promoType === 'percent') {
                                            $price -= ($price * $promoValue / 100);
                                        } else {
                                            $price -= $promoValue;
                                        }
                                    }
                                }
                            @endphp
                            <div class="d-flex justify-content-between border-bottom py-1">
                                <span>- {{ $detail->name }}</span>
                                <span class="fw-bold text-primary">
                                    @if($detail->has_stylist_price)
                                        @if($minPrice != $maxPrice)
                                            Rp {{ number_format(max(0, $minPrice), 0) }} - {{ number_format(max(0, $maxPrice), 0) }}
                                        @else
                                            Rp {{ number_format(max(0, $minPrice), 0) }}
                                        @endif
                                    @else
                                        Rp {{ number_format(max(0, $price), 0) }}
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
                </p>
                <div class="mt-auto">
                    <a href="{{ route('booking.select', $treatment->id) }}" class="btn btn-primary w-100 rounded-pill">
                        Pilih Treatment <i class="ti ti-chevron-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endforeach

@if($treatments->isEmpty())
    <div class="col-12 text-center py-5">
        <div class="mb-3">
            <i class="ti ti-search text-muted" style="font-size: 4rem;"></i>
        </div>
        <h5 class="text-muted">Tidak ada treatment ditemukan</h5>
        <p class="small text-muted">Coba ubah kategori atau kata kunci pencarian Anda</p>
    </div>
@endif
