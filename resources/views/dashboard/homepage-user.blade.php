@extends('layout.dashboard')
@section('title', 'Dashboard Pelanggan')


@section('content')
    <div class="row g-4">
        <!-- WELCOME HERO -->
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden"
                style="background: linear-gradient(135deg, #EA8290 0%, #D96A79 100%);">
                <div class="card-body p-4 p-md-5 position-relative">
                    <div class="row align-items-center">
                        <div class="col-md-7 text-white">
                             <div class="d-flex align-items-center gap-3 mb-2">
                                 <h2 class="text-white fw-bold mb-0">Halo, {{ Auth::user()->name }}! ✨</h2>
                                 @php
                                     $tier = Auth::user()->tier;
                                     $badgeClass = match($tier) {
                                         'Platinum' => 'bg-info text-white',
                                         'Gold' => 'bg-warning text-dark',
                                         'Silver' => 'bg-secondary text-white',
                                         default => 'bg-light text-muted',
                                     };
                                 @endphp
                                 <span class="badge {{ $badgeClass }} px-3 py-2 rounded-pill shadow-sm animate__animated animate__fadeInDown">
                                     <i class="ti ti-crown me-1"></i> {{ $tier }} Member
                                 </span>
                                 @if(Auth::user()->is_colour_circle_member)
                                 <span class="badge bg-pink text-white px-3 py-2 rounded-pill shadow-sm animate__animated animate__fadeInDown">
                                     <i class="ti ti-sparkles me-1"></i> Colour Circle Member
                                 </span>
                                 @endif
                             </div>
                             <p class="opacity-75 mb-4">Selamat datang kembali di Indah Sari Salon. Siap untuk tampil lebih menawan hari ini?</p>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('booking.index') }}" class="btn btn-light text-primary fw-bold px-4 py-2">
                                    <i class="ti ti-calendar-plus me-2"></i>Buat Janji Temu
                                </a>
                                <a href="https://chat.whatsapp.com/Klzg8cq9767Iolv1Dl7d5T?mode=gi_t" target="_blank" class="btn btn-success fw-bold px-4 py-2 shadow-sm">
                                    <i class="ti ti-brand-whatsapp me-2"></i>Gabung Komunitas
                                </a>
                            </div>
                        </div>
                        <div class="col-md-5 text-end d-none d-md-block">
                            <img src="{{ Auth::user()->avatar_url }}" alt="User Profile"
                                class="rounded-circle border border-white border-4 shadow"
                                style="width: 120px; height: 120px; object-fit: cover;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- LATEST BOOKING STATUS -->
        @if($latestBooking)
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-dark"><i class="ti ti-bell-ringing me-2 text-warning"></i>Status Booking Terakhir
                        </h5>
                        <a href="{{ route('booking.history') }}" class="btn btn-link btn-sm p-0">Lihat Riwayat <i
                                class="ti ti-chevron-right"></i></a>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center mb-3 mb-md-0">
                                    <div class="avtar avtar-lg bg-light-primary text-primary me-3">
                                        <i class="ti ti-scissors"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-bold text-dark">{{ $latestBooking->treatment->name }}</h6>
                                        <div class="d-flex flex-wrap gap-2 mb-1">
                                            <span class="badge bg-light-warning text-dark small"><i
                                                    class="ti ti-calendar me-1"></i>{{ \Carbon\Carbon::parse($latestBooking->reservation_datetime)->format('d M Y') }}</span>
                                            <span class="badge bg-light-info text-info small"><i
                                                    class="ti ti-clock me-1"></i>{{ \Carbon\Carbon::parse($latestBooking->reservation_datetime)->format('H:i') }}</span>
                                        </div>
                                        <small class="text-muted"><i class="ti ti-user-check me-1"></i>Stylist:
                                            {{ $latestBooking->stylist->name ?? 'Belum Ditentukan' }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <div class="mb-2">
                                    @if($latestBooking->status === 'proses')
                                        <span class="badge bg-danger rounded-pill px-3 py-2">Sedang Diproses (Pending)</span>
                                    @elseif($latestBooking->status === 'berhasil')
                                        <span class="badge bg-success rounded-pill px-3 py-2">Selesai ✅</span>
                                    @else
                                        <span
                                            class="badge bg-secondary rounded-pill px-3 py-2">{{ ucfirst($latestBooking->status) }}</span>
                                    @endif
                                </div>
                                <small class="text-muted d-block">Simpan kode booking:
                                    <strong>#BOOK-{{ $latestBooking->id }}</strong></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
 
        <!-- MEMBERSHIP PROGRESS CARD -->
        @php $nextTier = Auth::user()->next_tier_info; @endphp
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <h6 class="text-muted small fw-bold text-uppercase mb-3">Status Loyalitas Pelanggan</h6>
                            <div class="d-flex align-items-end gap-2 mb-2">
                                <h3 class="fw-bold mb-0">Rp {{ number_format(Auth::user()->total_spending, 0, ',', '.') }}</h3>
                                <span class="text-muted small pb-1">Total Transaksi</span>
                            </div>
                            @if($nextTier['next'])
                                <p class="text-muted small mb-0">
                                    Butuh <span class="fw-bold text-primary">Rp {{ number_format($nextTier['needed'], 0, ',', '.') }}</span> lagi untuk menjadi member <span class="badge bg-light-primary text-primary">{{ $nextTier['next'] }}</span>
                                </p>
                            @else
                                <p class="text-success small mb-0 fw-bold">🎉 Selamat! Anda telah mencapai level tertinggi (Platinum)</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small fw-bold">Progres Level</span>
                                <span class="small fw-bold text-primary">{{ round($nextTier['percent']) }}%</span>
                            </div>
                            <div class="progress rounded-pill" style="height: 12px; background-color: #f1f5f9;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" 
                                     style="width: {{ $nextTier['percent'] }}%" aria-valuenow="{{ $nextTier['percent'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            @if(Auth::user()->is_colour_circle_member)
                                <div class="mt-3 p-2 bg-light-danger rounded-3 d-flex align-items-center animate__animated animate__pulse animate__infinite">
                                    <i class="ti ti-sparkles text-danger me-2 fs-5"></i>
                                    <span class="small fw-bold text-danger">Anda memiliki Loyalty Coloring (Diskon 35% Aktif!)</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
 
        <!-- PROMO SECTION -->
        @if(isset($promoTreatments) && $promoTreatments->count() > 0)
            <div class="col-12 mt-2">
                <div class="d-flex justify-content-between align-items-end mb-3">
                    <div>
                        <h4 class="mb-1 fw-bold text-dark"><i class="ti ti-discount-2 text-danger me-2"></i>Promo Spesial Hari Ini 🔥</h4>
                        <p class="text-muted small mb-0">Penawaran terbatas untuk layanan kecantikan favorit Anda</p>
                    </div>
                </div>
                <div class="row g-3">
                    @foreach($promoTreatments as $promo)
                        <div class="col-xl-4 col-md-6 col-sm-12">
                            <div class="card treatment-card border-0 shadow-sm h-100 overflow-hidden border-top border-danger border-4">
                                <div class="position-relative">
                                    @php
                                        if (!$promo->image) {
                                            $imageUrl = asset('assets/images/no-image.jpg');
                                        } elseif (strpos($promo->image, 'http') === 0) {
                                            $imageUrl = $promo->image;
                                        } else {
                                            $bucket = ($promo->is_promo && env('SUPABASE_PROMO_BUCKET')) 
                                                ? env('SUPABASE_PROMO_BUCKET') 
                                                : env('SUPABASE_BUCKET');
                                            $imageUrl = env('SUPABASE_URL') . '/storage/v1/object/public/' . $bucket . '/' . $promo->image;
                                        }
                                    @endphp
                                    <img src="{{ $imageUrl }}" class="card-img-top" alt="{{ $promo->name }}"
                                        style="height: 180px; object-fit: cover;">
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <span class="badge bg-danger animate__animated animate__pulse animate__infinite px-3 py-2 rounded-pill shadow">
                                            PROMO
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title fw-bold text-dark mb-1">{{ $promo->name }}</h5>
                                    <p class="text-muted small mb-3 text-truncate-2">
                                        Kesempatan terbaik untuk mencoba {{ $promo->name }} dengan harga spesial.
                                    </p>
                                    <div class="mt-auto">
                                        <div class="mb-3">
                                            <small class="text-muted d-block mb-1"><i class="ti ti-calendar-time me-1"></i>Periode Promo:</small>
                                            @if($promo->promo_start_date || $promo->promo_end_date)
                                                <span class="badge bg-light-danger text-danger border border-danger border-opacity-10 w-100 py-2">
                                                    {{ $promo->promo_start_date ? \Carbon\Carbon::parse($promo->promo_start_date)->format('d M') : 'Mulai Sekarang' }} 
                                                    - 
                                                    {{ $promo->promo_end_date ? \Carbon\Carbon::parse($promo->promo_end_date)->format('d M Y') : 'Selesai' }}
                                                </span>
                                            @else
                                                <span class="badge bg-light-secondary text-secondary w-100 py-2">Selama Persediaan Ada</span>
                                            @endif
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <small class="text-muted d-block">Harga Promo</small>
                                                <span class="fw-bold text-danger h5 mb-0">Rp
                                                    {{ number_format($promo->details->min('price') ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                            <a href="{{ route('booking.select', $promo->id) }}"
                                                class="btn btn-danger rounded-pill px-4 shadow-sm">
                                                Ambil Promo
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- TREATMENT CATALOG GRID -->
        <div class="col-12 mt-2">
            <div class="d-flex justify-content-between align-items-end mb-3">
                <div>
                    <h4 class="mb-1 fw-bold text-dark">Layanan Unggulan Kami</h4>
                    <p class="text-muted small mb-0">Pilih treatment terbaik untuk perawatan Anda</p>
                </div>
                <a href="{{ route('booking.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">Lihat
                    Semua</a>
            </div>

            <div class="row g-3">
                @php $count = 0; @endphp
                @foreach($categories as $category)
                    @foreach($category->treatments as $treatment)
                        @if($count < 6)
                            <div class="col-xl-4 col-md-6 col-sm-12">
                                <div class="card treatment-card border-0 shadow-sm h-100 overflow-hidden">
                                    <div class="position-relative">
                                        @php
                                            if (!$treatment->image) {
                                                $imageUrl = asset('assets/images/no-image.jpg');
                                            } elseif (strpos($treatment->image, 'http') === 0) {
                                                $imageUrl = $treatment->image;
                                            } else {
                                                $bucket = ($treatment->is_promo && env('SUPABASE_PROMO_BUCKET')) 
                                                    ? env('SUPABASE_PROMO_BUCKET') 
                                                    : env('SUPABASE_BUCKET');
                                                $imageUrl = env('SUPABASE_URL') . '/storage/v1/object/public/' . $bucket . '/' . $treatment->image;
                                            }
                                        @endphp
                                        <img src="{{ $imageUrl }}" class="card-img-top" alt="{{ $treatment->name }}"
                                            style="height: 200px; object-fit: cover;">
                                        <div class="position-absolute top-0 start-0 m-3">
                                            <span class="badge bg-blur text-white px-3 py-2 rounded-pill shadow-sm"
                                                style="background: rgba(255,255,255,0.2); backdrop-filter: blur(8px);">
                                                {{ $category->name }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title fw-bold text-dark mb-1">{{ $treatment->name }}</h5>
                                        <p class="text-muted small mb-3 text-truncate-2"
                                            style="height: 40px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                            Nikmati layanan {{ $treatment->name }} profesional dari stylist berpengalaman kami.
                                        </p>
                                        <div class="mt-auto d-flex justify-content-between align-items-center">
                                            <div>
                                                <small class="text-muted d-block">Mulai dari</small>
                                                <span class="fw-bold text-primary h5 mb-0">Rp
                                                    {{ number_format($treatment->details->min('price') ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                            <a href="{{ route('booking.select', $treatment->id) }}"
                                                class="btn btn-primary rounded-pill px-3">
                                                Booking
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @php $count++; @endphp
                        @endif
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>

    <style>
        .treatment-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .treatment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .bg-blur {
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
@endsection

@push('scripts')
@endpush