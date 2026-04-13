<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('dashboard') }}" class="b-brand text-primary">
                <img src="{{ asset('assets/images/favicon.svg') }}" alt="logo" class="logo logo-lg" />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">
                <li class="pc-item pc-caption">
                    <label>Home</label>
                    <i class="ti ti-dashboard"></i>
                </li>
                <li class="pc-item">
                    <a href="{{ route('dashboard') }}" class="pc-link"><span class="pc-micon"><i
                                 class="ti ti-dashboard"></i></span><span class="pc-mtext">Dashboard</span></a>
                </li>

                <li class="pc-item pc-caption">
                    <label>Layanan</label>
                    <i class="ti ti-scissors"></i>
                </li>

                <li class="pc-item pc-hasmenu {{ request()->is('booking*') ? 'active pc-trigger' : '' }}">
                    <a class="pc-link">
                        <span class="pc-micon"><i class="ti ti-calendar"></i></span>
                        <span class="pc-mtext">Booking</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item"><a class="pc-link {{ request()->is('booking') ? 'active' : '' }}"
                                href="{{ route('booking.index') }}">Book An Appointment</a></li>

                        <li class="pc-item"><a class="pc-link {{ request()->is('booking/history') ? 'active' : '' }}"
                                href="{{ route('booking.history') }}">Riwayat Pemesanan</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>