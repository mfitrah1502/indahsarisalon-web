=@extends('layout.dashboard')
@section('title', 'About & Contact')
<!-- [Favicon] icon -->
<link rel="icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon" />
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
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link" />
<link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}" />
@section('content')
    <div class="pc-container">
        <div class="pc-content" id="main-content">
            <div class="row">
                <!-- About Us Section -->
                <div class="col-12 mb-5">
                    <div class="card p-4">
                        <h2>About Us</h2>
                        <p>
                            Welcome to Indah Sari Salon! We provide top-notch beauty services including hair care, facial
                            treatments, and relaxation therapies.
                            Our goal is to make you look and feel your best.
                        </p>
                        <p>
                            Established in 2010, we have been dedicated to delivering quality treatments with professional
                            staff and premium products.
                        </p>
                    </div>
                </div>

                <!-- Contact Section -->
                <div class="col-12" id="contact">
                    <div class="card p-4">
                        <h2>Contact Us</h2>
                        <p>
                            <strong>Email:</strong>
                            <a href="mailto:info@indahsarisalon.com">info@indahsarisalon.com</a>
                        </p>
                        <p>
                            <strong>WhatsApp:</strong>
                            <a href="https://wa.me/6281234567890" target="_blank">+62 812-3456-7890</a>
                        </p>
                        <p>
                            <strong>Address:</strong> Jl. Beauty No. 123, Jakarta
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/fonts/custom-font.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/js/theme.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
    <script>
        layout_change('light');
        font_change('Roboto');
        change_box_container('false');
        layout_caption_change('true');
        layout_rtl_change('false');
        preset_change('preset-1');
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Smooth scroll jika ada hash di URL
            if (window.location.hash) {
                const target = document.querySelector(window.location.hash);
                if (target) {
                    target.scrollIntoView({ behavior: "smooth", block: "start" });
                }
            }

            // Optional: kirim form via AJAX
            const form = document.getElementById('contact-form');
            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    alert('Pesan berhasil dikirim (simulasi).');
                });
            }
        });
    </script>
@endsection