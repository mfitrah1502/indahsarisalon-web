@foreach($treatments as $treatment)
    <div class="col-md-4 mb-4">
        <div class="card treatment-card h-100 border-0 shadow-sm">
            @php
                $images = $treatment->all_images;
            @endphp
            
            @if(count($images) > 1)
                <div id="carouselTreatment{{ $treatment->id }}" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        @foreach($images as $idx => $img)
                            <button type="button" data-bs-target="#carouselTreatment{{ $treatment->id }}" data-bs-slide-to="{{ $idx }}" class="{{ $idx == 0 ? 'active' : '' }}"></button>
                        @endforeach
                    </div>
                    <div class="carousel-inner">
                        @foreach($images as $idx => $img)
                            <div class="carousel-item {{ $idx == 0 ? 'active' : '' }}">
                                <img src="{{ $img }}" class="card-img-top w-100 d-block" alt="{{ $treatment->name }}" style="height: 250px; object-fit: cover;">
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselTreatment{{ $treatment->id }}" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselTreatment{{ $treatment->id }}" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            @else
                <img src="{{ $images[0] ?? asset('assets/images/no-image.jpg') }}" class="card-img-top w-100 d-block" alt="{{ $treatment->name }}" style="height: 250px; object-fit: cover;">
            @endif
            <div class="card-body">
                <h5 class="card-title fw-bold text-dark">
                    {{ $treatment->name }}
                    @if($treatment->is_promo)
                        <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">PROMO</span>
                    @endif
                    @if(Auth::check() && Auth::user()->has_coloring_loyalty && $treatment->category && stripos($treatment->category->name, 'Coloring') !== false)
                        <span class="badge bg-info ms-1 animate__animated animate__pulse animate__infinite" style="font-size: 0.65rem;">LOYALTY 35%</span>
                    @endif
                </h5>
                <p class="card-text text-muted mb-3">
                    <span class="badge bg-light-primary text-primary mb-2">{{ $treatment->category->name ?? '-' }}</span><br>
                    <div class="small">
                        @foreach($treatment->details as $detail)
                            @php
                                $prices = $detail->getMinMaxCalculatedPrice();
                            @endphp
                            <div class="d-flex justify-content-between border-bottom py-1">
                                <span>- {{ $detail->name }}</span>
                                <span class="fw-bold text-primary">
                                    @if($detail->has_stylist_price && $prices['min'] != $prices['max'])
                                        Rp {{ number_format($prices['min'], 0, ',', '.') }} - {{ number_format($prices['max'], 0, ',', '.') }}
                                    @else
                                        Rp {{ number_format($prices['min'], 0, ',', '.') }}
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
                                {{ $treatment->promo_start_date ? $treatment->promo_start_date->format('d/m') : '' }}
                                - 
                                {{ $treatment->promo_end_date ? $treatment->promo_end_date->format('d/m/y') : '' }}
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
