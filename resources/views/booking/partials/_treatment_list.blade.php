@foreach($treatments as $treatment)
    <div class="col-md-4 mb-4">
        <div class="card treatment-card h-100 border-0 shadow-sm">
            @php
                if (!$treatment->image) {
                    $imageUrl = asset('assets/images/no-image.jpg');
                } elseif (strpos($treatment->image, 'http') === 0) {
                    $imageUrl = $treatment->image;
                } else {
                    $bucket = ($treatment->is_promo && env('SUPABASE_PROMO_BUCKET')) ? env('SUPABASE_PROMO_BUCKET') : env('SUPABASE_BUCKET');
                    $imageUrl = env('SUPABASE_URL') . '/storage/v1/object/public/' . $bucket . '/' . $treatment->image;
                }
            @endphp
            <img src="{{ $imageUrl }}" class="card-img-top" alt="{{ $treatment->name }}">
            <div class="card-body">
                <h5 class="card-title fw-bold text-dark">
                    {{ $treatment->name }}
                    @if($treatment->is_promo)
                        <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">PROMO</span>
                    @endif
                    @if(Auth::check())
                        @php
                            $isColoring = $treatment->category && stripos($treatment->category->name, 'Coloring') !== false;
                            $showLoyaltyBadge = false;
                            if ($isColoring && Auth::user()->has_coloring_loyalty) {
                                $showLoyaltyBadge = true;
                            }
                        @endphp
                        @if($showLoyaltyBadge)
                            <span class="badge bg-info ms-1 animate__animated animate__pulse animate__infinite" style="font-size: 0.65rem;">LOYALTY 35%</span>
                        @endif
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

                                // Base Price Calculation
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

                                    // Apply ONLY Coloring Loyalty Preview (35%)
                                    if (Auth::check()) {
                                        $isColoring = $treatment->category && stripos($treatment->category->name, 'Coloring') !== false;
                                        if ($isColoring && Auth::user()->has_coloring_loyalty) {
                                            $minPrice -= ($minPrice * 35 / 100);
                                            $maxPrice -= ($maxPrice * 35 / 100);
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

                                    // Apply ONLY Coloring Loyalty Preview (35%)
                                    if (Auth::check()) {
                                        $isColoring = $treatment->category && stripos($treatment->category->name, 'Coloring') !== false;
                                        if ($isColoring && Auth::user()->has_coloring_loyalty) {
                                            $price -= ($price * 35 / 100);
                                        }
                                    }
                                }
                            @endphp
                            <div class="d-flex justify-content-between border-bottom py-1">
                                <span>- {{ $detail->name }}</span>
                                <span class="fw-bold text-primary">
                                    @if($detail->has_stylist_price)
                                        @if($minPrice != $maxPrice)
                                            Rp {{ number_format(max(0, $minPrice), 0, ',', '.') }} - {{ number_format(max(0, $maxPrice), 0, ',', '.') }}
                                        @else
                                            Rp {{ number_format(max(0, $minPrice), 0, ',', '.') }}
                                        @endif
                                    @else
                                        Rp {{ number_format(max(0, $price), 0, ',', '.') }}
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
                </p>
                <div class="mt-auto">
                    @if($treatment->is_promo && ($treatment->promo_start_date || $treatment->promo_end_date))
                        <div class="mb-2 p-2 bg-light-danger rounded-3 text-center">
                            <small class="text-danger fw-bold d-block" style="font-size: 0.7rem;">
                                <i class="ti ti-calendar-event me-1"></i>Valid: 
                                {{ $treatment->promo_start_date ? \Carbon\Carbon::parse($treatment->promo_start_date)->format('d/m') : '' }}
                                - 
                                {{ $treatment->promo_end_date ? \Carbon\Carbon::parse($treatment->promo_end_date)->format('d/m/y') : '' }}
                            </small>
                        </div>
                    @endif
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
