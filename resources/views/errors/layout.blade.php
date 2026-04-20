<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Indahsari Salon</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
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
                            50: '#fdf2f8', 100: '#fce7f3', 200: '#fbcfe8',
                            300: '#f9a8d4', 400: '#f472b6', 500: '#ec4899',
                            600: '#db2777', 700: '#be185d', 800: '#9d174d', 900: '#831843',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .blob {
            position: absolute;
            width: 500px;
            height: 500px;
            background: linear-gradient(180deg, rgba(236, 72, 153, 0.2) 0%, rgba(236, 72, 153, 0.05) 100%);
            filter: blur(80px);
            border-radius: 50%;
            z-index: -1;
            animation: move 20s infinite alternate;
        }
        @keyframes move {
            from { transform: translate(-10%, -10%); }
            to { transform: translate(10%, 10%); }
        }
    </style>
</head>
<body class="bg-pink-50 min-h-screen flex items-center justify-center p-6 overflow-hidden relative">
    <!-- Background Blobs -->
    <div class="blob" style="top: -100px; left: -100px;"></div>
    <div class="blob" style="bottom: -100px; right: -100px; animation-delay: -5s;"></div>

    <div class="max-w-lg w-full text-center relative z-10">
        <div class="glass p-10 md:p-16 rounded-[2.5rem] shadow-2xl shadow-pink-200/50">
            <div class="mb-8 flex justify-center">
                <div class="w-24 h-24 bg-pink-100 rounded-3xl flex items-center justify-center text-pink-600 shadow-inner">
                    @yield('icon')
                </div>
            </div>
            
            <h1 class="text-6xl font-bold text-gray-900 mb-4 tracking-tight">@yield('code')</h1>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">@yield('message')</h2>
            <p class="text-gray-600 mb-10 leading-relaxed">
                @yield('description')
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" class="bg-pink-600 text-white px-8 py-4 rounded-full font-semibold hover:bg-pink-700 transition-all shadow-lg shadow-pink-600/30 hover:shadow-pink-600/50 hover:-translate-y-1">
                    Kembali ke Beranda
                </a>
                <button onclick="window.location.reload()" class="bg-white text-gray-700 border border-gray-200 px-8 py-4 rounded-full font-semibold hover:bg-gray-50 transition-all shadow-sm">
                    Muat Ulang
                </button>
            </div>
        </div>
        
        <p class="mt-8 text-gray-400 text-sm">
            &copy; {{ date('Y') }} Indahsari Salon. Premium Beauty Experience.
        </p>
    </div>
</body>
</html>
