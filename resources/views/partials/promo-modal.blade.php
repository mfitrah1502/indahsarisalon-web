<!-- resources/views/partials/promo-modal.blade.php -->
@if(session('show_promo_modal') && Auth::check() && strtolower(Auth::user()->role) === 'pelanggan' && isset($promoTreatments) && $promoTreatments->count() > 0)
<div class="modal fade" id="promoModal" tabindex="-1" aria-labelledby="promoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold" id="promoModalLabel">
                    <i class="ti ti-gift me-2"></i> Penawaran Spesial Hari Ini!
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="promoCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($promoTreatments as $promo)
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                <div class="row g-0">
                                    <div class="col-md-6">
                                        @php
                                            $imageUrl = $promo->main_image_url;
                                        @endphp
                                        <img src="{{ $imageUrl }}" class="img-fluid h-100" style="object-fit: cover; min-height: 400px;" alt="{{ $promo->name }}">
                                    </div>
                                    <div class="col-md-6 p-4 d-flex flex-column justify-content-center">
                                        <div class="mb-2">
                                            <span class="badge bg-danger px-3 py-2 rounded-pill mb-2">PROMO</span>
                                        </div>
                                        <h2 class="fw-bold text-dark mb-3">{{ $promo->name }}</h2>
                                        <p class="text-muted mb-4">Nikmati layanan unggulan kami dengan harga spesial. Jangan lewatkan kesempatan terbatas ini!</p>
                                        
                                        <div class="mb-4">
                                            @foreach($promo->details as $detail)
                                                @php
                                                    $originalPrice = $detail->price;
                                                    $discountedPrice = $originalPrice;
                                                    $pType = strtolower($promo->promo_type);
                                                    if (in_array($pType, ['percentage', 'percent', 'persen'])) {
                                                        $discountedPrice = $originalPrice - ($originalPrice * $promo->promo_value / 100);
                                                    } else {
                                                        $discountedPrice = (float) $promo->promo_value;
                                                    }
                                                @endphp
                                                <div class="d-flex justify-content-between border-bottom py-2">
                                                    <span class="small text-dark">{{ $detail->name }}</span>
                                                    <div>
                                                        <span class="fw-bold text-primary">Rp {{ number_format(max(0, $discountedPrice), 0, ',', '.') }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <a href="{{ route('booking.index') }}?treatment_id={{ $promo->id }}" class="btn text-white btn-lg rounded-pill shadow-sm" style="background-color: #EA8290; border-color: #EA8290;">
                                            Booking Sekarang <i class="ti ti-arrow-right ms-2"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($promoTreatments->count() > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#promoCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon bg-dark rounded-circle" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#promoCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon bg-dark rounded-circle" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Promo Modal Check:', {
            hasPromo: {{ isset($promoTreatments) && $promoTreatments->count() > 0 ? 'true' : 'false' }},
            showModal: {{ session('show_promo_modal') ? 'true' : 'false' }}
        });
        
        var modalEl = document.getElementById('promoModal');
        if (modalEl) {
            var promoModal = new bootstrap.Modal(modalEl);
            promoModal.show();
        }
    });
</script>
@endpush
@php session()->forget('show_promo_modal'); @endphp
@endif
