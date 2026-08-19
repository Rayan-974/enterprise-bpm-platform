<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Enterprise BPM Platform' }}</title>
    <!-- Tailwind CSS Engine -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif'],
                    },
                    colors: {
                        skyPrimary: '#87CEEB',
                        skyHover: '#70B8D8',
                        purpleSecondary: '#4B2E83',
                        purpleHover: '#3B2468',
                        creamBase: '#FAF7EF',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #FAF7EF;
            background-image: 
                radial-gradient(at 0% 0%, rgba(135, 206, 235, 0.18) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(75, 46, 131, 0.12) 0px, transparent 50%);
            background-attachment: fixed;
            color: #1e1b4b;
        }
        .glass-nav {
            background: rgba(75, 46, 131, 0.96);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 2px solid rgba(135, 206, 235, 0.4);
        }
        .shiny-card { transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease; will-change: transform; }
        .shiny-card:hover { transform: translateY(-4px); box-shadow: 0 16px 32px -8px rgba(75, 46, 131, 0.18), 0 0 20px rgba(135, 206, 235, 0.35); border-color: #87CEEB; }
        .shine-sweep { position: relative; overflow: hidden; }
        .shine-sweep::after {
            content: ''; position: absolute; top: -50%; left: -150%; width: 200%; height: 200%;
            background: linear-gradient(60deg, transparent 20%, rgba(255, 255, 255, 0.4) 50%, transparent 80%);
            transform: rotate(25deg); transition: none;
        }
        .shine-sweep:hover::after { animation: shineSweepAnim 0.75s ease; }
        @keyframes shineSweepAnim { 0% { left: -150%; } 100% { left: 150%; } }
        .gradient-text {
            background: linear-gradient(135deg, #4B2E83 0%, #3B2468 40%, #0284c7 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .pulse-glow-red { animation: redGlowPulse 2s ease-in-out infinite; }
        @keyframes redGlowPulse {
            0%, 100% { box-shadow: 0 0 10px rgba(225, 29, 72, 0.4); }
            50% { box-shadow: 0 0 20px rgba(225, 29, 72, 0.8); }
        }
        .page-fade-up { animation: fadeUp 0.35s ease-out forwards; }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .bell-bounce { animation: bellBounce 4s ease infinite; }
        @keyframes bellBounce { 0%, 92%, 100% { transform: rotate(0deg); } 94% { transform: rotate(10deg); } 96% { transform: rotate(-10deg); } 98% { transform: rotate(6deg); } }
        
        /* Layout Structure Helper Rules */
        .page-header-container {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            border-radius: 1.5rem;
            padding: 1.75rem 2rem;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }
        @media (min-width: 768px) {
            .page-header-container {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }
        @media (prefers-reduced-motion: reduce) {
            *, ::before, ::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
            .shine-sweep::after { display: none; }
        }
    </style>
    @livewireStyles
</head>
<body class="bg-creamBase min-h-screen text-slate-800 flex flex-col antialiased">
    
    <!-- Top Navigation Bar -->
    <header class="bg-purpleSecondary text-white shadow-xl sticky top-0 z-40 border-b-2 border-skyPrimary/40 backdrop-blur-md">
        <div class="max-w-[1600px] w-full mx-auto px-4 sm:px-6 lg:px-10 py-3.5 sm:py-4 flex items-center justify-between">
            <div class="flex items-center space-x-4 sm:space-x-6">
                <!-- Hamburger Drawer Trigger -->
                <button id="mobile-menu-btn" onclick="toggleMobileDrawer(true)" aria-label="Toggle Navigation Menu" class="md:hidden text-white hover:text-skyPrimary focus:outline-none p-2 rounded-xl hover:bg-purpleHover transition min-h-[44px] min-w-[44px] flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 font-bold text-xl sm:text-2xl lg:text-3xl tracking-tight group">
                    <span class="bg-skyPrimary text-purpleSecondary w-10 h-10 sm:w-11 sm:h-11 rounded-2xl flex items-center justify-center font-extrabold text-lg sm:text-xl shadow-xl group-hover:scale-105 transition-all">EB</span>
                    <span class="text-white group-hover:text-skyPrimary transition-colors truncate">Enterprise BPM</span>
                </a>
                <span class="hidden lg:inline-block text-xs bg-purpleHover text-skyPrimary px-3.5 py-1.5 rounded-full font-semibold border border-skyPrimary/40 shadow-inner tracking-wide">
                    Enterprise Platform
                </span>
            </div>

            <!-- Top User Profile & Actions -->
            <div class="flex items-center space-x-3 sm:space-x-6">
                <!-- Notifications Bell -->
                <a href="{{ route('notifications.index') }}" aria-label="Notifications" class="relative text-white hover:text-skyPrimary p-2.5 transition rounded-2xl hover:bg-purpleHover group bell-bounce min-h-[44px] min-w-[44px] flex items-center justify-center">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    @if(auth()->check() && auth()->user()->workflowNotifications()->whereNull('read_at')->count() > 0)
                        <span class="absolute top-1.5 right-1.5 w-3 h-3 bg-skyPrimary rounded-full ring-2 ring-purpleSecondary"></span>
                    @endif
                </a>

                <!-- User Account Switcher & Profile Link -->
                @auth
                <div class="flex items-center space-x-3 sm:space-x-5 border-l border-purpleHover pl-3 sm:pl-6">
                    <a href="{{ route('profile.index') }}" class="text-right hidden sm:block group">
                        <div class="text-sm sm:text-base font-semibold text-white tracking-wide leading-tight group-hover:text-skyPrimary transition-colors truncate max-w-[150px] lg:max-w-none">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-skyPrimary font-medium mt-0.5 tracking-wider uppercase">{{ auth()->user()->roles->first()?->name ?? 'Employee' }}</div>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="shine-sweep text-xs bg-skyPrimary hover:bg-skyHover text-purpleSecondary font-bold px-4 sm:px-5 py-2.5 rounded-2xl transition shadow-lg hover:scale-105 active:scale-95 uppercase tracking-wider min-h-[40px]">
                            Logout
                        </button>
                    </form>
                </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Mobile Slide-in Navigation Drawer -->
    <div id="mobile-drawer" class="fixed inset-0 z-50 flex hidden">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="toggleMobileDrawer(false)"></div>
        <div class="relative w-80 max-w-[85vw] bg-white h-full shadow-2xl p-6 flex flex-col justify-between overflow-y-auto z-10">
            <div>
                <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
                    <div class="flex items-center space-x-3">
                        <span class="bg-skyPrimary text-purpleSecondary w-10 h-10 rounded-2xl flex items-center justify-center font-extrabold text-lg shadow-md">EB</span>
                        <span class="font-black text-purpleSecondary text-lg tracking-tight">Navigation Menu</span>
                    </div>
                    <button onclick="toggleMobileDrawer(false)" class="text-slate-400 hover:text-slate-700 p-2 rounded-xl font-black text-xl hover:bg-slate-100 transition min-h-[44px] min-w-[44px] flex items-center justify-center">
                        ✕
                    </button>
                </div>

                <nav class="space-y-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-2xl text-sm font-bold transition {{ request()->routeIs('dashboard') ? 'bg-purpleSecondary text-white shadow-lg' : 'text-slate-700 hover:bg-creamBase' }}">
                        <svg class="w-5 h-5 text-skyPrimary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('tasks.index') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-2xl text-sm font-bold transition {{ request()->routeIs('tasks.*') ? 'bg-purpleSecondary text-white shadow-lg' : 'text-slate-700 hover:bg-creamBase' }}">
                        <svg class="w-5 h-5 text-skyPrimary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <span>My Tasks</span>
                    </a>
                    <a href="{{ route('workflows.index') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-2xl text-sm font-bold transition {{ request()->routeIs('workflows.*') ? 'bg-purpleSecondary text-white shadow-lg' : 'text-slate-700 hover:bg-creamBase' }}">
                        <svg class="w-5 h-5 text-skyPrimary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <span>Workflows Catalog</span>
                    </a>
                    <a href="{{ route('analytics.index') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-2xl text-sm font-bold transition {{ request()->routeIs('analytics.*') ? 'bg-purpleSecondary text-white shadow-lg' : 'text-slate-700 hover:bg-creamBase' }}">
                        <svg class="w-5 h-5 text-skyPrimary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        <span>Workflow Analytics</span>
                    </a>
                    <a href="{{ route('audit.index') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-2xl text-sm font-bold transition {{ request()->routeIs('audit.*') ? 'bg-purpleSecondary text-white shadow-lg' : 'text-slate-700 hover:bg-creamBase' }}">
                        <svg class="w-5 h-5 text-skyPrimary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Audit Trail</span>
                    </a>
                    <a href="{{ route('profile.index') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-2xl text-sm font-bold transition {{ request()->routeIs('profile.*') ? 'bg-purpleSecondary text-white shadow-lg' : 'text-slate-700 hover:bg-creamBase' }}">
                        <svg class="w-5 h-5 text-skyPrimary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span>My Profile</span>
                    </a>
                </nav>
            </div>

            @auth
            <div class="pt-6 border-t border-slate-100">
                <div class="font-bold text-slate-900 text-sm mb-1">{{ auth()->user()->name }}</div>
                <div class="text-xs font-semibold text-purpleSecondary uppercase tracking-wider mb-4">{{ auth()->user()->roles->first()?->name ?? 'Employee' }}</div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-300 font-bold py-3 rounded-2xl text-xs uppercase tracking-wider transition">
                        Logout
                    </button>
                </form>
            </div>
            @endauth
        </div>
    </div>

    <!-- Main Content Layout Grid -->
    <div class="flex-1 max-w-[1600px] w-full mx-auto px-4 sm:px-6 lg:px-10 py-6 sm:py-8 flex flex-col md:flex-row items-start gap-6 lg:gap-8">
        <!-- Desktop Sidebar Navigation -->
        <aside id="sidebar" class="w-full md:w-64 lg:w-72 flex-shrink-0 bg-white/95 backdrop-blur-md rounded-3xl shadow-xl border border-slate-200/80 p-5 hidden md:block sticky top-24 z-30 transition-all">
            <nav class="space-y-1.5">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-2xl text-sm font-semibold transition shiny-card {{ request()->routeIs('dashboard') ? 'bg-purpleSecondary text-white shadow-lg font-bold' : 'text-slate-700 hover:bg-creamBase hover:text-purpleSecondary' }}">
                    <svg class="w-5 h-5 text-skyPrimary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('tasks.index') }}" class="flex items-center justify-between px-4 py-3 rounded-2xl text-sm font-semibold transition shiny-card {{ request()->routeIs('tasks.*') ? 'bg-purpleSecondary text-white shadow-lg font-bold' : 'text-slate-700 hover:bg-creamBase hover:text-purpleSecondary' }}">
                    <div class="flex items-center space-x-3.5">
                        <svg class="w-5 h-5 text-skyPrimary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <span>My Tasks</span>
                    </div>
                </a>
                <a href="{{ route('workflows.index') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-2xl text-sm font-semibold transition shiny-card {{ request()->routeIs('workflows.*') ? 'bg-purpleSecondary text-white shadow-lg font-bold' : 'text-slate-700 hover:bg-creamBase hover:text-purpleSecondary' }}">
                    <svg class="w-5 h-5 text-skyPrimary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span>Workflows Catalog</span>
                </a>
                <a href="{{ route('analytics.index') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-2xl text-sm font-semibold transition shiny-card {{ request()->routeIs('analytics.*') ? 'bg-purpleSecondary text-white shadow-lg font-bold' : 'text-slate-700 hover:bg-creamBase hover:text-purpleSecondary' }}">
                    <svg class="w-5 h-5 text-skyPrimary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <span>Workflow Analytics</span>
                </a>
                <a href="{{ route('audit.index') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-2xl text-sm font-semibold transition shiny-card {{ request()->routeIs('audit.*') ? 'bg-purpleSecondary text-white shadow-lg font-bold' : 'text-slate-700 hover:bg-creamBase hover:text-purpleSecondary' }}">
                    <svg class="w-5 h-5 text-skyPrimary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Audit Trail</span>
                </a>
                <a href="{{ route('profile.index') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-2xl text-sm font-semibold transition shiny-card {{ request()->routeIs('profile.*') ? 'bg-purpleSecondary text-white shadow-lg font-bold' : 'text-slate-700 hover:bg-creamBase hover:text-purpleSecondary' }}">
                    <svg class="w-5 h-5 text-skyPrimary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span>My Profile</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 w-full min-w-0 page-fade-up space-y-6">
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50/90 border border-emerald-300 text-emerald-900 text-sm font-semibold flex items-center justify-between shadow-lg">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-rose-50/90 border border-rose-300 text-rose-900 text-sm font-semibold flex items-center justify-between shadow-lg">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>

    @livewireScripts
    <script>
        function toggleMobileDrawer(show) {
            const drawer = document.getElementById('mobile-drawer');
            if (drawer) {
                if (show) {
                    drawer.classList.remove('hidden');
                    setTimeout(() => drawer.classList.remove('opacity-0'), 10);
                } else {
                    drawer.classList.add('opacity-0');
                    setTimeout(() => drawer.classList.add('hidden'), 300);
                }
            }
        }
    </script>
</body>
</html>
