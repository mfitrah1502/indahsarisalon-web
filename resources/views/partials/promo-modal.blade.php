<!-- resources/views/partials/promo-modal.blade.php -->
@if(session('show_promo_modal') && isset($promoTreatments) && $promoTreatments->count() > 0)
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
                        @foreach($promoTreatments as $index => $promo)
                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                <div class="row g-0">
                                    <div class="col-md-6">
                                        @php
                                            $imageUrl = $promo->image 
                                                ? config('services.supabase.url') . '/storage/v1/object/public/' . config('services.supabase.bucket') . '/' . $promo->image 
                                                : asset('assets/images/no-image.jpg');
                                        @endphp
                                        <img src="{{ $imageUrl }}" class="img-fluid h-100" style="object-fit: cover; min-height: 400px;" alt="{{ $promo->name }}">
                                    </div>
                                    <div class="col-md-6 p-4 d-flex flex-column justify-content-center">
                                        <div class="mb-2">
                                            <span class="badge bg-danger px-3 py-2 rounded-pill mb-2">PROMO {{ $promo->promo_type == 'fixed' ? 'Rp '.number_format($promo->promo_value, 0) : $promo->promo_value.'%' }}</span>
                                            <span class="badge bg-light-primary text-primary px-3 py-2 rounded-pill mb-2 ms-1">{{ $promo->category->name ?? '-' }}</span>
                                        </div>
                                        <h2 class="fw-bold text-dark mb-3">{{ $promo->name }}</h2>
                                        <p class="text-muted mb-4">Nikmati layanan unggulan kami dengan harga spesial. Jangan lewatkan kesempatan terbatas ini!</p>
                                        
                                        <div class="mb-4">
                                            @foreach($promo->details as $detail)
                                                <div class="d-flex justify-content-between border-bottom py-2">
                                                    <span class="small text-dark">{{ $detail->name }}</span>
                                                    <span class="fw-bold text-primary">Rp {{ number_format($detail->price, 0, ',', '.') }}</span>
                                                </div>
                                            @endforeach
                                        </div>

                                        <a href="{{ route('booking.select', $promo->id) }}" class="btn btn-primary btn-lg rounded-pill shadow-sm">
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
        var promoModal = new bootstrap.Modal(document.getElementById('promoModal'));
        promoModal.show();
    });
</script>
@endpush
@endif
