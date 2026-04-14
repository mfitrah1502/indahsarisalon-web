<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indahsari Salon - Perawatan Rambut & Kecantikan Premium</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (via Vite setting if applicable, fallback script used safely here for independent view rendering if no Vite is enabled yet, but we will use Vite) -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <!-- Using CDN fallback only if vite isn't compiled. -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Outfit', 'sans-serif'],
                        },
                        colors: {
                            pink: {
                                50: '#fdf2f8',
                                100: '#fce7f3',
                                200: '#fbcfe8',
                                300: '#f9a8d4',
                                400: '#f472b6',
                                500: '#ec4899',
                                600: '#db2777',
                                700: '#be185d',
                                800: '#9d174d',
                                900: '#831843',
                            }
                        }
                    }
                }
            }
        </script>
    @endif
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            scroll-behavior: smooth;
        }
        /* Glassmorphism utility */
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
        .text-gradient {
            background: linear-gradient(to right, #db2777, #f472b6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="bg-pink-50/30 text-gray-800 antialiased selection:bg-pink-200 selection:text-pink-900 overflow-x-hidden">

   <!-- Navigation -->
<nav class="fixed w-full z-50 glass transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">

            <!-- Logo -->
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <img src="{{ asset('assets/images/indahsarisalonimg.jpg') }}"
                     alt="Indah Sari Salon"
                     class="w-12 h-12 object-contain">
                <span class="font-bold text-2xl tracking-tight text-gray-900">
                    Indahsari<span class="text-pink-600">Salon</span>
                </span>
            </a>

            <!-- Right Menu -->
            <div class="flex items-center gap-6">
                <div class="space-x-4 flex items-center">
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-pink-600 transition">
                        Log In
                    </a>
                </div>

                <a href="{{ route('register') }}"
                   class="bg-pink-600 hover:bg-pink-700 text-white px-6 py-2.5 rounded-full font-medium transition-all shadow-lg shadow-pink-600/30 hover:shadow-pink-600/50 hover:-translate-y-0.5 flex items-center gap-2">
                    <span>Sign Up</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

        </div>
    </div>
</nav>

    <!-- Hero Section -->
    <div class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <!-- Decorative background elements -->
            <div class="absolute top-20 left-10 w-72 h-72 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
            <div class="absolute top-40 right-10 w-72 h-72 bg-pink-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-8 left-40 w-72 h-72 bg-rose-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto">
                <span class="inline-block py-1 px-3 rounded-full bg-pink-100 text-pink-600 text-sm font-semibold tracking-wider mb-4 uppercase shadow-sm">Hair Styling & Color</span>
                <h1 class="text-5xl md:text-6xl font-extrabold text-gray-900 tracking-tight leading-tight mb-6">
                    Sempurnakan Keindahan Alami Anda Bersama <br><span class="text-gradient">Indahsari Salon</span>
                </h1>
                <p class="mt-4 text-xl text-gray-600 font-light mb-10 leading-relaxed">
                    Spesialis salon rambut dalam teknik pewarnaan seperti balayage, airtouch, highlight, dan full colour untuk hasil yang natural dan elegan.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="{{ route('booking.index') ?? '#' }}" class="bg-pink-600 text-white px-8 py-4 rounded-full font-semibold text-lg hover:bg-pink-700 transition shadow-xl shadow-pink-600/30 flex items-center justify-center gap-2">
                        <span>Konsultasi & Booking</span>
                    </a>
                    <a href="#promosi" class="bg-white text-gray-700 border border-gray-200 px-8 py-4 rounded-full font-semibold text-lg hover:bg-gray-50 transition shadow-sm flex items-center justify-center gap-2">
                        <span>Lihat Price List</span>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </a>
                </div>
            </div>
            
            <div class="mt-20 mx-auto max-w-5xl rounded-3xl overflow-hidden shadow-2xl relative">
                <!-- Aesthetic Banner Image placeholder -->
                <img src="https://images.unsplash.com/photo-1562322140-8baeececf3df?ixlib=rb-4.0.3&auto=format&fit=crop&w=1400&q=80" alt="Salon Treatment" class="w-full h-[500px] object-cover object-center transform hover:scale-105 transition duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-pink-900/60 to-transparent"></div>
            </div>
        </div>
    </div>

    <!-- Video Introduction Section -->
    <section class="pt-36 pb-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-16 items-center">
                <div>
                    <h2 class="max-w-3xl md:text-4xl font-bold text-gray-900 mb-6">Rasakan Sentuhan Kecantikan Premium di Indahsari Salon ✨</h2>
                    <p class="text-gray-600 text-lg mb-6 leading-relaxed">
                        Kami menyediakan rangkaian perawatan lengkap mulai dari hair ritual eksklusif, hair colouring profesional, hingga facial dan nail treatment dengan produk berkualitas tinggi untuk hasil yang sehat, berkilau, dan mempesona.
                    </p>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-pink-100 flex items-center justify-center text-pink-600">✓</div>
                            <span class="text-gray-700">Hair Spa, Hair Mask & Scalp Detox Treatment</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-pink-100 flex items-center justify-center text-pink-600">✓</div>
                            <span class="text-gray-700">Hair Coloring (Non Bleaching, Bleaching, Highlight & Balayage)</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-pink-100 flex items-center justify-center text-pink-600">✓</div>
                            <span class="text-gray-700">Facial Treatment & Skin Care (Glow Facial, Peeling, Brow Lift, Lash Care)</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-pink-100 flex items-center justify-center text-pink-600">✓</div>
                            <span class="text-gray-700">Manicure, Pedicure & Hand/Foot Treatment</span>
                        </li>
                        <li class="flex items-center gap-3">    
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Promosi Perawatan Section -->
    <section id="promosi" class="py-20 bg-pink-50/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Price List Treatment</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Manjakan diri Anda dengan pilihan perawatan terbaik kami yang dirancang untuk meningkatkan keindahan alami Anda.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Promo Item 1 -->
                @php
                    $promos = [
                        ['title' => 'Hair Spa & Hair Mask', 'price' => 'Rp 120.000-Rp 750.000', 'desc' => 'Perawatan rambut dan kulit kepala untuk menjaga kesehatan, mengurangi kerontokan, dan menghadirkan kilau alami rambut.', 'img' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=600&q=80'],
                        ['title' => 'Hair Coloring (Non Bleaching)', 'price' => 'Rp 850.000-Rp 1.550.000', 'desc' => 'Pewarnaan rambut tanpa bleaching untuk hasil warna natural yang tetap menjaga kesehatan rambut.', 'img' => 'https://images.unsplash.com/photo-1522337660859-02fbefca4702?auto=format&fit=crop&w=600&q=80'],
                        ['title' => 'Hair Coloring (Bleaching)', 'price' => 'Rp 650.000-Rp 2.350.000', 'desc' => 'Proses pewarnaan dengan bleaching untuk menghasilkan warna yang lebih terang, bold, dan maksimal.', 'img' => 'https://images.unsplash.com/photo-1522337660859-02fbefca4702?auto=format&fit=crop&w=600&q=80'],
                        ['title' => 'Hair Coloring (Highlight/Balayage)', 'price' => 'Rp 1.450.000-Rp 2.350.000', 'desc' => 'Teknik pewarnaan modern untuk menciptakan dimensi warna rambut yang natural, halus, dan elegan.', 'img' => 'https://images.unsplash.com/photo-1522337660859-02fbefca4702?auto=format&fit=crop&w=600&q=80'],
                        ['title' => 'Facial Treatment & Skin Care', 'price' => 'Rp 30.000-Rp 225.000', 'desc' => 'Perawatan wajah untuk membersihkan, merawat, dan mencerahkan kulit agar tampak sehat dan bercahaya.', 'img' => asset('assets/images/facial-tratement.svg')],
                        ['title' => 'Nail & Hand/Foot Treatment', 'price' => 'Rp 125.000-Rp 150.000', 'desc' => 'Perawatan kuku dan kulit tangan serta kaki untuk menjaga kebersihan, kelembutan, dan tampilan yang lebih rapi dan elegan.', 'img' => 'https://images.unsplash.com/photo-1522337660859-02fbefca4702?auto=format&fit=crop&w=600&q=80'],
                    ];
                @endphp
                
                @foreach ($promos as $promo)
                <div class="bg-white rounded-2xl overflow-hidden shadow-xl shadow-pink-100/50 border border-gray-100 hover:shadow-2xl hover:shadow-pink-200/50 hover:-translate-y-1 transition duration-300">
                    <div class="relative h-56">
                        <img src="{{ $promo['img'] }}" alt="Promosi" class="w-full h-full object-cover">
                    </div>
                    <div class="p-6 relative">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $promo['title'] }}</h3>
                        <p class="text-gray-500 mb-4 text-sm">{{ $promo['desc'] }}</p>
                        <div class="flex justify-between items-center mt-6">
                            <span class="text-lg font-semibold text-pink-600">{{ $promo['price'] }}</span>
                            <a href="{{ route('booking.index') ?? '#' }}" class="bg-pink-100 hover:bg-pink-200 text-pink-700 px-4 py-2 rounded-lg font-medium transition text-sm">Pilih</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="mt-12 text-center">
                <a href="{{ route('booking.index') ?? '#' }}" class="inline-flex items-center gap-2 text-pink-600 font-semibold hover:text-pink-700 group">
                    Lihat Semua Treatement
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Location & Contact Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 bg-pink-600 rounded-3xl overflow-hidden shadow-2xl">
                <div class="p-10 md:p-16 text-white flex flex-col justify-center">
                    <h2 class="text-3xl md:text-4xl font-bold mb-6">Kunjungi Outlet Kami 📍</h2>
                    <p class="text-pink-100 text-lg mb-8">
                        Kami sangat senang menunggu kedatangan Anda. Berikut adalah detail kontak dan lokasi kami untuk memudahkan Anda berkunjung atau bertanya.
                    </p>
                    
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-pink-500/50 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold">Alamat Salon</h4>
                                <p class="text-pink-100 mt-1">Jl jawa No. 30A, Tegal Boto Lor, Sumbersari, Kec. Sumbersari, Kabupaten Jember, Jawa Timur 68121</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-pink-500/50 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold">Hubungi Kami</h4>
                                <p class="text-pink-100 mt-1">WA: +62 822-6458-5886</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-pink-500/50 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold">Jam Operasional</h4>
                                <p class="text-pink-100 mt-1">Setiap Hari: 09:00 - 18:00 WIB</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Google Maps Embed -->
                <div class="w-full h-[400px] lg:h-full min-h-[400px]">
                                    <iframe 
                                        src="https://www.google.com/maps?q=Jl+jawa+30A+Jember&output=embed"
                                        width="100%" 
                                        height="100%" 
                                        style="border:0;" 
                                        allowfullscreen="" 
                        loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 border-t border-gray-800 text-gray-400 py-12 text-center">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-2xl font-bold text-white mb-2">Indahsari<span class="text-pink-500">Salon</span></h2>
            <p class="mb-6">Kecantikan premium berawal dari sini.</p>
            <div class="flex justify-center space-x-6 mb-8 text-sm">
                <a href="#" class="hover:text-pink-400 transition">Beranda</a>
                <a href="#promosi" class="hover:text-pink-400 transition">Promosi</a>
                <a href="#" class="hover:text-pink-400 transition">Layanan</a>
                <a href="{{ route('login') }}" class="hover:text-pink-400 transition">Masuk Akun</a>
            </div>
            <p class="text-sm">© {{ date('Y') }} Indahsari Salon. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
