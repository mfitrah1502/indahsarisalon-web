<!doctype html>
<html lang="en">

<head>
    <script>
        // Mencegah layar berkedip putih saat mode gelap aktif
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        }
    </script>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard') | Indah Sarisalon</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <base href="{{ url('/') }}/">
    <!-- [Favicon] icon -->
    <link rel="icon" href="{{ asset('assets/images/indahsarisalonimg.jpg') }}" type="image/x-icon" />
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <!-- [Google Font] Family -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap"
        id="main-font-link" />
    <!-- [phosphor Icons] https://phosphoricons.com/ -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/phosphor/duotone/style.css') }}" />
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}" />
    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}" />
    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}" />
    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}" />
    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}" />

    <style>
        :root, [data-pc-preset=preset-1] {
            --pc-sidebar-active-color: #EA8290 !important;
            --bs-primary: #EA8290 !important;
            --bs-primary-rgb: 234, 130, 144 !important;
            --bs-primary-bg-subtle: #fcebed !important;
            --bs-secondary: #EA8290 !important;
            --bs-secondary-rgb: 234, 130, 144 !important;
            --bs-secondary-light: #fce4e7 !important;
        }

        /* Global Button Overrides */
        .btn-primary {
            --bs-btn-bg: #EA8290 !important;
            --bs-btn-border-color: #EA8290 !important;
            --bs-btn-hover-bg: #d6717e !important;
            --bs-btn-hover-border-color: #d6717e !important;
            --bs-btn-active-bg: #d6717e !important;
            --bs-btn-active-border-color: #d6717e !important;
        }

        .btn-light-primary {
            background: #fcebed !important;
            color: #EA8290 !important;
        }

        .btn-light-primary:hover {
            background: #EA8290 !important;
            color: #fff !important;
        }
        
        /* Header & Icon Colors */
        .pc-header {
            z-index: 1050 !important;
        }
        .pc-header .pc-head-link.head-link-secondary,
        .pc-header .pc-head-link.head-link-primary {
            background: #fcebed !important;
            color: #EA8290 !important;
        }
        .pc-header .pc-head-link.head-link-secondary i,
        .pc-header .pc-head-link.head-link-primary i {
            color: #EA8290 !important;
        }
        .pc-header .pc-head-link.head-link-secondary:hover,
        .pc-header .pc-head-link.head-link-primary:hover,
        .pc-header .pc-head-link.head-link-secondary:hover i,
        .pc-header .pc-head-link.head-link-primary:hover i {
            background: #EA8290 !important;
            color: #fff !important;
        }
        
        /* Sidebar Active State icon/text */
        .pc-sidebar .pc-item.active > .pc-link,
        .pc-sidebar .pc-item:hover > .pc-link {
            color: #EA8290 !important;
        }
        .pc-sidebar .pc-item.active > .pc-link .pc-micon i,
        .pc-sidebar .pc-item.active > .pc-link .pc-micon svg {
            color: #EA8290 !important;
        }
        
        /* Secondary Backgrounds */
        .bg-secondary-dark {
            background: #D96A79 !important;
        }

        /* Profile Dropdown Hover Override */
        .dropdown-user-profile .dropdown-item:hover {
            background-color: #fcebed !important;
            color: #EA8290 !important;
        }
        .dropdown-user-profile .dropdown-item:hover i {
            color: #EA8290 !important;
        }

    </style>
    @stack('styles')
    <style>
        /* === THE ULTIMATE DARK MODE HAMMER (ABSOLUTE PRIORITY) === */
        [data-bs-theme="dark"] body,
        [data-bs-theme="dark"] .pc-container,
        [data-bs-theme="dark"] .pc-content {
            background-color: #121212 !important;
            color: #ffffff !important;
        }
        
        [data-bs-theme="dark"] .pc-sidebar,
        [data-bs-theme="dark"] .pc-header {
            background-color: #1e1e1e !important;
            border-color: #333 !important;
        }
        
        /* Overriding Hardcoded White/Light Backgrounds EVERYWHERE */
        [data-bs-theme="dark"] .bg-white,
        [data-bs-theme="dark"] .bg-light,
        [data-bs-theme="dark"] .bg-body {
            background-color: #1e1e1e !important;
            color: #ffffff !important;
        }
        
        /* Cards & Containers (Profile, Bookings, etc) */
        [data-bs-theme="dark"] .card,
        [data-bs-theme="dark"] .card-body,
        [data-bs-theme="dark"] .card-header,
        [data-bs-theme="dark"] .card-footer {
            background-color: #1e1e1e !important;
            border-color: #333 !important;
            color: #ffffff !important;
        }

        /* Modals & Pop-ups */
        [data-bs-theme="dark"] .modal-content,
        [data-bs-theme="dark"] .offcanvas {
            background-color: #1e1e1e !important;
            border-color: #333 !important;
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .modal-header,
        [data-bs-theme="dark"] .modal-footer {
            border-color: #333 !important;
        }
        [data-bs-theme="dark"] .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        /* Lists, Accordions & Tabs */
        [data-bs-theme="dark"] .list-group-item,
        [data-bs-theme="dark"] .accordion-item,
        [data-bs-theme="dark"] .accordion-button {
            background-color: #1e1e1e !important;
            border-color: #333 !important;
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .accordion-button:not(.collapsed) {
            background-color: #2a2a2a !important;
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .nav-tabs {
            border-bottom-color: #333 !important;
            position: relative;
            z-index: 10; /* Bring tabs forward so card-body doesn't cover them */
        }
        [data-bs-theme="dark"] .nav-tabs .nav-link {
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .nav-tabs .nav-link:hover {
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .nav-tabs .nav-link.active {
            background-color: #1e1e1e !important;
            color: #EA8290 !important;
            border-color: #333 #333 #1e1e1e !important;
        }
        
        /* Sidebar Text & Icons */
        [data-bs-theme="dark"] .pc-caption {
            color: #cccccc !important;
        }
        [data-bs-theme="dark"] .pc-sidebar .pc-link {
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .pc-sidebar .pc-link .pc-micon i {
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .pc-sidebar .pc-item.active > .pc-link,
        [data-bs-theme="dark"] .pc-sidebar .pc-item.active > .pc-link .pc-micon i {
            color: #EA8290 !important;
        }

        /* Header Buttons & Dropdowns */
        [data-bs-theme="dark"] .pc-header .pc-head-link {
            background-color: #2a2a2a !important;
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .pc-header .pc-head-link:hover {
            background-color: #EA8290 !important;
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .dropdown-menu {
            background-color: #1e1e1e !important;
            border-color: #333 !important;
        }
        [data-bs-theme="dark"] .dropdown-item {
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .dropdown-item:hover {
            background-color: #333 !important;
        }

        /* General Typography */
        [data-bs-theme="dark"] h1, [data-bs-theme="dark"] h2, 
        [data-bs-theme="dark"] h3, [data-bs-theme="dark"] h4, 
        [data-bs-theme="dark"] h5, [data-bs-theme="dark"] h6,
        [data-bs-theme="dark"] .text-dark,
        [data-bs-theme="dark"] strong,
        [data-bs-theme="dark"] b {
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .text-muted, [data-bs-theme="dark"] p,
        [data-bs-theme="dark"] span:not(.badge),
        [data-bs-theme="dark"] label {
            color: #ffffff !important;
        }
        
        /* Tables, Employee Rows & Forms */
        [data-bs-theme="dark"] .table {
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .employee-row,
        [data-bs-theme="dark"] tr {
            background-color: #2a2a2a !important;
            color: #ffffff !important;
            border-color: #444 !important;
        }
        [data-bs-theme="dark"] td, [data-bs-theme="dark"] th {
            background-color: transparent !important;
            color: #ffffff !important;
            border-color: #444 !important;
        }
        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select,
        [data-bs-theme="dark"] .input-group-text,
        [data-bs-theme="dark"] input,
        [data-bs-theme="dark"] select,
        [data-bs-theme="dark"] textarea {
            background-color: #2a2a2a !important;
            border-color: #444 !important;
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .btn-light {
            background-color: #333 !important;
            border-color: #444 !important;
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .btn-light:hover {
            background-color: #444 !important;
        }
        [data-bs-theme="dark"] .bg-light-primary { background-color: #3a2226 !important; }
        [data-bs-theme="dark"] .bg-light-success { background-color: #1e3a29 !important; }
        [data-bs-theme="dark"] .bg-light-danger { background-color: #3a1e1e !important; }
        [data-bs-theme="dark"] .bg-light-info { background-color: #1e2f3a !important; }
        [data-bs-theme="dark"] .bg-light-warning { background-color: #3a321e !important; }
    </style>
    <script>
        window.onpageshow = function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        };
    </script>
</head>

<body>
    <!-- Sidebar -->
    @if(Auth::user()->role === 'admin')
        @include('partials.sidebar')
    @elseif(Auth::user()->role === 'karyawan')
        @include('partials.sidebar-karyawan')
    @else
        @include('partials.sidebar-pelanggan')
    @endif

    <!-- Header -->
    @include('partials.header')

    <!-- Main Content -->
    <div class="pc-container">
        <div class="pc-content">
            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    @include('partials.footer')

    <!-- Required Js -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/fonts/custom-font.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/js/theme.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Setup Dark Mode Logic
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeIcon = document.getElementById('theme-icon');
            const root = document.documentElement;
            
            // Read initial theme
            let currentTheme = localStorage.getItem('theme') || 'light';
            
            // Synchronize Bootstrap and Berry theme attributes
            function applyTheme(theme) {
                root.setAttribute('data-bs-theme', theme);
                document.body.setAttribute('data-pc-theme', theme);
                if (typeof layout_change === 'function' && document.body.getAttribute('data-pc-theme') !== theme) {
                    layout_change(theme);
                }
            }
            
            if (currentTheme === 'dark' && themeIcon) {
                themeIcon.classList.replace('ti-moon', 'ti-sun');
            }

            // Theme Config (Safe Check)
            if (typeof layout_change === 'function') {
                layout_change(currentTheme);
                font_change('Roboto');
                change_box_container('false');
                layout_caption_change('true');
                layout_rtl_change('false');
                preset_change('preset-1');
            }
            applyTheme(currentTheme);

            // Toggle Click Event
            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    currentTheme = root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
                    localStorage.setItem('theme', currentTheme);
                    applyTheme(currentTheme);
                    
                    if (currentTheme === 'dark') {
                        themeIcon.classList.replace('ti-moon', 'ti-sun');
                    } else {
                        themeIcon.classList.replace('ti-sun', 'ti-moon');
                    }
                });
            }
        });
    </script>
    @include('partials.promo-modal')
    @stack('scripts')
    @include('partials.offline-overlay')
</body>

</html>