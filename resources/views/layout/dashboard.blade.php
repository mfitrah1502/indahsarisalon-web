<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard') | Indah Sarisalon</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <base href="{{ url('/') }}/">
    <!-- [Favicon] icon -->
    <link rel="icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon" />
    
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
            // Theme Config (Safe Check)
            if (typeof layout_change === 'function') {
                layout_change('light');
                font_change('Roboto');
                change_box_container('false');
                layout_caption_change('true');
                layout_rtl_change('false');
                preset_change('preset-1');
            }
        });
    </script>
    @stack('scripts')
    @include('partials.promo-modal')
    @include('partials.offline-overlay')
</body>

</html>