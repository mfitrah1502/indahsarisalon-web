<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard | Indah Sarisalon</title>
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
    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}" />
    <style>
        :root, [data-pc-preset=preset-1] {
            --pc-sidebar-active-color: #EA8290 !important;
            --bs-secondary: #EA8290 !important;
            --bs-secondary-rgb: 234, 130, 144 !important;
            --bs-secondary-light: #fce4e7 !important;
        }
        
        /* Header & Icon Colors */
        .pc-header {
            z-index: 1050 !important; /* Ensure header is above content */
        }
        .pc-header .pc-head-link.head-link-secondary {
            background: #fce4e7 !important;
            color: #EA8290 !important;
        }
        .pc-header .pc-head-link.head-link-secondary:hover {
            background: #EA8290 !important;
            color: #fff !important;
        }
        
        /* Ensure dropdown is visible */
        .dropdown-user-profile.show {
            display: block !important;
            z-index: 9999 !important;
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
    @include('partials.sidebar-pelanggan')

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
</body>

</html>