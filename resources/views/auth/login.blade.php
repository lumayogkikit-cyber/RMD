<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RMD Corp Wood Scaler System</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        amber: {
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
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
        .wood-gradient {
            background: linear-gradient(135deg, #d97706 0%, #b45309 50%, #78350f 100%);
        }
        .glass-card {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(245, 158, 11, 0.25);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 antialiased selection:bg-amber-500 selection:text-white">

    <div class="w-full max-w-md space-y-6">
        
        <!-- Header Brand Badge -->
        <div class="text-center space-y-3">
            <a href="{{ route('landing') }}" class="inline-block relative">
                <img src="{{ asset('images/logo.png') }}" alt="Rolly & May Loggers Logo" class="w-24 h-24 rounded-full border-4 border-amber-500 shadow-2xl mx-auto object-cover hover:scale-105 transition-transform">
            </a>
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight uppercase">ROLLY & MAY LOGGERS - DERIAL</h1>
                <p class="text-xs text-amber-400 font-semibold uppercase tracking-wider">United Wood Industries - Scaler Portal</p>
            </div>
        </div>

        <!-- Login Card Form -->
        <div class="glass-card p-8 rounded-3xl shadow-2xl space-y-6">
            
            <div class="border-b border-slate-800 pb-3">
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-right-to-bracket text-amber-500"></i> Account Authentication
                </h2>
                <p class="text-xs text-slate-400">Sign in with your Scaler or Management credentials.</p>
            </div>

            <!-- Error Alerts -->
            @if($errors->any())
                <div class="p-4 rounded-xl bg-rose-950/80 border border-rose-500/40 text-rose-200 text-xs space-y-1">
                    <div class="font-bold flex items-center gap-2"><i class="fa-solid fa-circle-exclamation text-rose-400"></i> Authentication Error</div>
                    @foreach($errors->all() as $err)
                        <div>• {{ $err }}</div>
                    @endforeach
                </div>
            @endif

            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-950/80 border border-emerald-500/40 text-emerald-200 text-xs font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-base"></i> {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus placeholder="scaler@rmd.com" class="w-full bg-slate-900 border border-slate-700 text-slate-100 text-sm rounded-xl pl-10 pr-4 py-3 focus:ring-2 focus:ring-amber-500 outline-none">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-key"></i>
                        </div>
                        <input type="password" name="password" id="password" required placeholder="••••••••" class="w-full bg-slate-900 border border-slate-700 text-slate-100 text-sm rounded-xl pl-10 pr-4 py-3 focus:ring-2 focus:ring-amber-500 outline-none">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-400">
                        <input type="checkbox" name="remember" class="rounded bg-slate-900 border-slate-700 text-amber-500 focus:ring-amber-500">
                        <span>Remember Me</span>
                    </label>
                </div>

                <button type="submit" class="w-full wood-gradient hover:brightness-110 text-white font-extrabold py-3.5 px-4 rounded-xl text-sm transition-all shadow-lg shadow-amber-950/60 border border-amber-500/40 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-shield-halved text-amber-200"></i> Secure Sign In
                </button>
            </form>

            <!-- Demo Quick Click Credentials -->
            <div class="border-t border-slate-800 pt-4 space-y-2">
                <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider text-center">Demo Quick Fill Accounts</div>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" onclick="fillCreds('superadmin@rmd.com', 'password')" class="p-2 bg-slate-900 hover:bg-slate-800 border border-amber-500/30 rounded-lg text-left transition-all">
                        <div class="text-xs font-bold text-amber-400 flex items-center gap-1"><i class="fa-solid fa-crown text-[10px]"></i> Super Admin</div>
                        <div class="text-[10px] text-slate-400 font-mono">superadmin@rmd.com</div>
                    </button>
                    <button type="button" onclick="fillCreds('scaler@rmd.com', 'password')" class="p-2 bg-slate-900 hover:bg-slate-800 border border-slate-700 rounded-lg text-left transition-all">
                        <div class="text-xs font-bold text-slate-200 flex items-center gap-1"><i class="fa-solid fa-user text-[10px]"></i> Scaler Staff</div>
                        <div class="text-[10px] text-slate-400 font-mono">scaler@rmd.com</div>
                    </button>
                </div>
            </div>

        </div>

        <div class="text-center text-xs text-slate-500">
            <a href="{{ route('landing') }}" class="hover:text-amber-400 font-semibold"><i class="fa-solid fa-arrow-left"></i> Return to Public Landing Page</a>
        </div>

    </div>

    <script>
        function fillCreds(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>

</body>
</html>
