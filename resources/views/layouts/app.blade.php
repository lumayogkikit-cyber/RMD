<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'RMD CORP - Wood Scaler & Invoicing System')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        amber: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                            950: '#451a03',
                        },
                        emerald: {
                            600: '#059669',
                            700: '#047857',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
        }
        .glass-panel {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .wood-gradient {
            background: linear-gradient(135deg, #b45309 0%, #78350f 50%, #451a03 100%);
        }
        .badge-wood {
            background: linear-gradient(90deg, #d97706 0%, #b45309 100%);
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
            }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col antialiased selection:bg-amber-500 selection:text-white">

    <!-- Header Navigation -->
    <header class="no-print sticky top-0 z-50 glass-panel border-b border-slate-800 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo & Brand -->
                <a href="{{ route('scaling.index') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('images/logo.png') }}" alt="Rolly & May Loggers Logo" class="w-12 h-12 rounded-full border-2 border-amber-500/60 shadow-lg object-cover group-hover:scale-105 transition-transform">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-lg font-black tracking-tight text-white group-hover:text-amber-400 transition-colors">
                                RMD CORP
                            </span>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 font-semibold border border-amber-500/30">
                                RMD
                            </span>
                        </div>
                        <p class="text-[10px] text-amber-400 font-medium tracking-wider uppercase">ROLLY & MAY LOGGERS - DERIAL</p>
                    </div>
                </a>

                <!-- Navigation Action Links -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('scaling.index') }}" class="px-3.5 py-2 rounded-lg text-xs font-semibold transition-all duration-200 flex items-center gap-2 {{ request()->routeIs('scaling.index') ? 'bg-slate-800 text-amber-400 border border-amber-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                        <i class="fa-solid fa-list-check"></i>
                        <span>Scale Sheets</span>
                    </a>
                    
                    @auth
                        @if(Auth::user()->isSuperAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-2 rounded-lg text-xs font-bold transition-all duration-200 flex items-center gap-2 {{ request()->routeIs('admin.*') ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'text-amber-400 hover:bg-amber-500/10' }}">
                                <i class="fa-solid fa-crown text-amber-400"></i>
                                <span>Super Admin</span>
                            </a>
                        @endif
                    @endauth

                    <a href="{{ route('scaling.create') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-white wood-gradient hover:brightness-110 shadow-lg shadow-amber-900/30 transition-all duration-200 flex items-center gap-2 border border-amber-500/40">
                        <i class="fa-solid fa-plus-circle text-amber-200"></i>
                        <span>New Scale Sheet</span>
                    </a>

                    @auth
                        <div class="h-6 w-px bg-slate-800"></div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold text-slate-300 hidden md:inline">{{ Auth::user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition-colors" title="Sign Out">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                </button>
                            </form>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Body Container -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Global Flash Notifications -->
        @if(session('success'))
            <div class="no-print mb-6 p-4 rounded-xl bg-emerald-950/80 border border-emerald-500/40 text-emerald-200 flex items-center justify-between shadow-lg shadow-emerald-950/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-800/50 flex items-center justify-center text-emerald-300">
                        <i class="fa-solid fa-circle-check text-lg"></i>
                    </div>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-200">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="no-print mb-6 p-4 rounded-xl bg-rose-950/80 border border-rose-500/40 text-rose-200 flex items-center justify-between shadow-lg shadow-rose-950/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-rose-800/50 flex items-center justify-center text-rose-300">
                        <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                    </div>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-200">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="no-print border-t border-slate-800/80 glass-panel py-6 mt-12 text-slate-400 text-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-tree text-amber-500"></i>
                <span class="font-semibold text-slate-300">RMD Corporation</span>
                <span class="text-xs text-slate-500">| Brereton Scale Standard System</span>
            </div>
            <div class="text-xs text-slate-500">
                &copy; {{ date('Y') }} All Rights Reserved. Operational Scaling & Financial Module.
            </div>
        </div>
    </footer>
</footer>

    @stack('scripts')

    <!-- Alpine.js for lightweight reactivity (used by report filters) -->
    <script defer src="https://unpkg.com/alpinejs@3.12.0/dist/cdn.min.js"></script>
</html>
