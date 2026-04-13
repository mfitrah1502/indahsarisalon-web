<header class="pc-header">
    <div class="header-wrapper"><!-- [Mobile Media Block] start -->
        <div class="me-auto pc-mob-drp">
            <ul class="list-unstyled">
                <li class="pc-h-item header-mobile-collapse">
                    <a href="#" class="pc-head-link head-link-secondary ms-0" id="sidebar-hide">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
                <li class="pc-h-item pc-sidebar-popup">
                    <a href="#" class="pc-head-link head-link-secondary ms-0" id="mobile-collapse">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
            </ul>
        </div>
        <!-- [Mobile Media Block end] -->
        <div class="ms-auto">
            <ul class="list-unstyled">

                <li class="dropdown pc-h-item header-user-profile">
                    <button class="pc-head-link head-link-primary dropdown-toggle arrow-none me-0 d-flex align-items-center border-0 bg-transparent" 
                       id="profile-dropdown-btn"
                       data-bs-toggle="dropdown" type="button" 
                       aria-haspopup="false" aria-expanded="false" style="padding: 6px 15px; border-radius: 50px;">
                        <img src="{{ Auth::user()->avatar_url }}"
                            alt="avatar" class="user-avtar" style="width: 35px; height: 35px; object-fit: cover; border-radius: 50%;" />
                        <span class="ms-2">
                            <i class="ti ti-settings fs-4"></i>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown" id="profile-dropdown-menu">
                        <div class="dropdown-header">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <img src="{{ Auth::user()->avatar_url }}"
                                        alt="user-image" class="user-avtar rounded-circle" style="width: 45px; height: 45px; object-fit: cover;" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    @php
                                        $hour = date('H');
                                        if ($hour >= 5 && $hour < 11) {
                                            $greeting = 'Selamat Pagi';
                                        } elseif ($hour >= 11 && $hour < 15) {
                                            $greeting = 'Selamat Siang';
                                        } elseif ($hour >= 15 && $hour < 18) {
                                            $greeting = 'Selamat Sore';
                                        } else {
                                            $greeting = 'Selamat Malam';
                                        }
                                    @endphp
                                    <h5 class="mb-0">{{ $greeting }},</h5>
                                    <p class="mb-0 fw-bold">{{ Auth::user()->name }}</p>
                                    <small class="text-muted">{{ ucfirst(Auth::user()->role ?? 'User') }}</small>
                                </div>
                            </div>
                            <hr />
                            <div class="profile-notification-scroll position-relative" style="max-height: calc(100vh - 280px)">
                                <a href="{{ route('profile') }}" class="dropdown-item">
                                    <i class="ti ti-user"></i>
                                    <span>My Profile</span>
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                                <a href="#" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="ti ti-logout"></i>
                                    <span>Logout</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</header>
<!-- [ Header ] end -->