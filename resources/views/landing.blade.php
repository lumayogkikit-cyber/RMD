<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RMD Corporation - Rolly & May Loggers Derial</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
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
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
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
            background-color: #0b1329;
            color: #f8fafc;
        }
        .glass-hero {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(245, 158, 11, 0.2);
        }
        .wood-gradient {
            background: linear-gradient(135deg, #d97706 0%, #b45309 50%, #78350f 100%);
        }
        .glow-amber {
            box-shadow: 0 0 40px -10px rgba(245, 158, 11, 0.3);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between antialiased selection:bg-amber-500 selection:text-white">

    <!-- Top Navigation Header -->
    <header class="sticky top-0 z-50 glass-hero border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand Logo & Name -->
                <div class="flex items-center gap-4">
                    <img src="{{ asset('images/logo.png') }}" alt="Rolly & May Loggers Derial Logo" class="w-14 h-14 rounded-full border-2 border-amber-500/60 shadow-lg shadow-amber-950/50 object-cover">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xl font-black tracking-tight text-white">RMD CORPORATION</span>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 font-bold border border-amber-500/30">RMD LOGGERS</span>
                        </div>
                        <p class="text-xs text-amber-400/90 font-semibold tracking-wider uppercase">ROLLY & MAY LOGGERS - DERIAL DIVISION</p>
                    </div>
                </div>

                <!-- Navigation Portal Link -->
                <div>
                    @auth
                        @if(Auth::user()->isSuperAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white wood-gradient hover:brightness-110 shadow-lg transition-all border border-amber-500/40 flex items-center gap-2">
                                <i class="fa-solid fa-crown text-amber-200"></i> Master Control Panel
                            </a>
                        @else
                            <a href="{{ route('scaling.index') }}" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white wood-gradient hover:brightness-110 shadow-lg transition-all border border-amber-500/40 flex items-center gap-2">
                                <i class="fa-solid fa-gauge-high"></i> Go to Scaler Dashboard
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white wood-gradient hover:brightness-110 shadow-lg transition-all border border-amber-500/40 flex items-center gap-2">
                            <i class="fa-solid fa-right-to-bracket text-amber-200"></i> Login to System
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Hero Section -->
    <main class="flex-grow flex flex-col justify-center items-center px-4 py-16 text-center max-w-7xl mx-auto w-full">
        
        <div class="relative mb-6">
            <div class="absolute -inset-1 rounded-full bg-amber-500/20 blur-xl"></div>
            <img src="{{ asset('images/logo.png') }}" alt="Rolly & May Loggers Logo" class="relative w-36 h-36 rounded-full border-4 border-amber-500 shadow-2xl object-cover">
        </div>

        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-bold uppercase tracking-widest mb-6">
            <i class="fa-solid fa-tree"></i> Industrial Wood Scaling & Financial Accounting
        </div>

        <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight text-white max-w-4xl leading-tight">
            Precision Brereton Volume Scaling & <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 via-amber-200 to-amber-500">Automated Financial Invoicing</span>
        </h1>

        <p class="mt-6 text-lg sm:text-xl text-slate-300 max-w-2xl font-normal leading-relaxed">
            Empowering <strong class="text-amber-400">Rolly & May Loggers (Derial)</strong> and RMD Corporation with automated Brereton volume calculations, split log classification, and instant supplier payouts.
        </p>

        <!-- CTA Buttons -->
        <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4 w-full sm:w-auto">
            @auth
                <a href="{{ route('scaling.create') }}" class="w-full sm:w-auto px-8 py-4 rounded-2xl text-base font-extrabold text-white wood-gradient hover:scale-105 transition-all shadow-xl shadow-amber-950/60 border border-amber-400/40 flex items-center justify-center gap-3">
                    <i class="fa-solid fa-plus-circle text-amber-200 text-lg"></i> Create New Scale Sheet
                </a>
                <a href="{{ route('scaling.index') }}" class="w-full sm:w-auto px-8 py-4 rounded-2xl text-base font-bold bg-slate-800/80 hover:bg-slate-700 text-slate-200 transition-all border border-slate-700 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-table-list text-amber-400"></i> View All Scale Sheets
                </a>
            @else
                <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 rounded-2xl text-base font-extrabold text-white wood-gradient hover:scale-105 transition-all shadow-xl shadow-amber-950/60 border border-amber-400/40 flex items-center justify-center gap-3">
                    <i class="fa-solid fa-lock text-amber-200 text-lg"></i> Login to Scaler Portal
                </a>
            @endauth
        </div>

        <!-- Live Metrics Counter Section -->
        <div class="mt-16 w-full grid grid-cols-1 sm:grid-cols-3 gap-6">
            
            <div class="glass-hero p-6 rounded-2xl border border-slate-800 text-left hover:border-amber-500/40 transition-all">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs uppercase font-bold tracking-wider text-slate-400">Volume Scaled This Month</span>
                    <div class="w-10 h-10 rounded-xl bg-amber-500/20 flex items-center justify-center text-amber-400">
                        <i class="fa-solid fa-cubes text-xl"></i>
                    </div>
                </div>
                <div class="text-3xl font-black text-white font-mono flex items-baseline gap-1">
                    {{ number_format($totalVolumeMonth, 3) }} <span class="text-sm font-normal text-amber-400">m³</span>
                </div>
                <p class="text-xs text-slate-400 mt-2 font-mono"><i class="fa-solid fa-tree text-emerald-400"></i> Total Logs Scaled: {{ number_format($totalLogsAll) }} pcs</p>
            </div>

            <div class="glass-hero p-6 rounded-2xl border border-slate-800 text-left hover:border-amber-500/40 transition-all">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs uppercase font-bold tracking-wider text-slate-400">Trucks Unloaded Today</span>
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-400">
                        <i class="fa-solid fa-truck-front text-xl"></i>
                    </div>
                </div>
                <div class="text-3xl font-black text-emerald-400 font-mono flex items-baseline gap-2">
                    {{ number_format($activeTrucksToday) }} <span class="text-sm font-normal text-slate-400">truck loads</span>
                </div>
                <p class="text-xs text-slate-400 mt-2">Active receiving queue at Taguibo Facility</p>
            </div>

            <div class="glass-hero p-6 rounded-2xl border border-slate-800 text-left hover:border-amber-500/40 transition-all">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs uppercase font-bold tracking-wider text-slate-400">Active Registered Suppliers</span>
                    <div class="w-10 h-10 rounded-xl bg-sky-500/20 flex items-center justify-center text-sky-400">
                        <i class="fa-solid fa-users text-xl"></i>
                    </div>
                </div>
                <div class="text-3xl font-black text-sky-400 font-mono flex items-baseline gap-2">
                    {{ number_format($totalSuppliers) }} <span class="text-sm font-normal text-slate-400">partners</span>
                </div>
                <p class="text-xs text-slate-400 mt-2">Agusan del Norte & Sur Timber Partners</p>
            </div>

        </div>

    </main>

    <!-- Public Footer -->
    <footer class="border-t border-slate-800 bg-slate-950/80 py-8 text-center text-slate-500 text-xs">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-8 h-8 rounded-full border border-amber-500/40">
                <span class="font-bold text-slate-300">Rolly & May Loggers Derial - RMD Corporation</span>
            </div>
            <div>
                &copy; {{ date('Y') }} Official Wood Scaling & Financial Management System. Taguibo, Butuan City.
            </div>
        </div>
    </footer>

</body>
</html>
