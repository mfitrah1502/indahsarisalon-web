<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('dashboard') }}" class="b-brand text-primary">
                <!-- ========   Change your logo from here   ============ -->
                <img src="{{ asset('assets/images/favicon.svg') }}" alt="logo" class="logo logo-lg" />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">
                <li class="pc-item pc-caption">
                    <label>Dashboard</label>
                    <i class="ti ti-dashboard"></i>
                </li>
                <li class="pc-item">
                    <a href="{{ route('dashboard') }}" class="pc-link"><span class="pc-micon"><i
                                class="ti ti-dashboard"></i></span><span class="pc-mtext">Dashboard</span></a>
                </li>

                <li class="pc-item pc-caption">
                    <label>Menu Manajemen</label>
                    <i class="ti ti-apps"></i>
                </li>

                <!-- MENU KHUSUS ADMIN -->
                <li class="pc-item pc-hasmenu">
                    <a class="pc-link"><span class="pc-micon"><i class="ti ti-users"></i></span><span
                            class="pc-mtext">Manajemen</span><span class="pc-arrow"><i
                                data-feather="chevron-right"></i></span></a>
                    <ul class="pc-submenu">
                        <li class="pc-item"><a href="{{ route('karyawan.index') }}" class="pc-link">Manajemen Karyawan</a></li>
                        <li class="pc-item"><a href="{{ route('pelanggan.index') }}" class="pc-link">Manajemen Pelanggan</a></li>
                        <li class="pc-item"><a href="{{ route('treatment.index') }}" class="pc-link">Manajemen Treatment</a></li>
                    </ul>
                </li>
                <li class="pc-item pc-hasmenu">
                    <a class="pc-link"><span class="pc-micon"><i class="ti ti-wallet"></i></span><span
                            class="pc-mtext">Keuangan</span><span class="pc-arrow"><i
                                data-feather="chevron-right"></i></span></a>
                    <ul class="pc-submenu">
                        <li class="pc-item"><a class="pc-link" href="#!">Pemasukan</a></li>
                        <li class="pc-item"><a class="pc-link" href="#!">Pengeluaran</a></li>
                    </ul>
                </li>

                <!-- MENU BOOKING -->
                <li class="pc-item pc-hasmenu {{ request()->is('booking*') || request()->is('admin/bookings*') ? 'active pc-trigger' : '' }}">
                    <a class="pc-link">
                        <span class="pc-micon"><i class="ti ti-calendar"></i></span>
                        <span class="pc-mtext">Booking</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item"><a class="pc-link {{ request()->is('booking') ? 'active' : '' }}"
                                href="{{ route('booking.index') }}">Book An Appointment</a></li>

                        <li class="pc-item"><a class="pc-link {{ request()->is('admin/bookings*') ? 'active' : '' }}"
                                href="{{ route('admin.bookings.index') }}">Status Pemesanan</a></li>

                        <li class="pc-item"><a class="pc-link {{ request()->is('booking/history') ? 'active' : '' }}"
                                href="{{ route('booking.history') }}">Riwayat Pemesanan</a></li>
                    </ul>
                </li>

                {{-- <li class="pc-item">
                    <a href="../pages/register-v3.html" target="_blank" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-user-plus"></i></span>
                        <span class="pc-mtext">Register</span>
                    </a>
                </li> --}}

            </ul>
            {{-- <div class="pc-navbar-card bg-primary rounded">
                <h4 class="text-white">Explore full code</h4>
                <p class="text-white opacity-75">Buy now to get full access of code files</p>
                <a href="https://codedthemes.com/item/berry-bootstrap-5-admin-template/" target="_blank"
                    class="btn btn-light text-primary">
                    Buy Now
                </a>
            </div>
            <div class="w-100 text-center">
                <div class="badge theme-version badge rounded-pill bg-light text-dark f-12"></div>
            </div> --}}
        </div>
    </div>
</nav>