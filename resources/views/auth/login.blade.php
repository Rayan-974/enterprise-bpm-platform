<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Enterprise BPM Platform</title>
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
</head>
<body class="bg-creamBase min-h-screen flex items-center justify-center p-6 font-sans antialiased">
    <div class="max-w-lg w-full bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden transform hover:scale-[1.01] transition-transform duration-300">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purpleSecondary to-purpleHover p-8 text-center text-white border-b border-skyPrimary/30">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-skyPrimary text-purpleSecondary rounded-2xl font-extrabold text-2xl mb-4 shadow-lg">
                EB
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight">Enterprise BPM Sign In</h1>
            <p class="text-xs font-semibold text-skyPrimary mt-2 uppercase tracking-widest">Sign in to your enterprise workflow workspace</p>
        </div>

        <!-- Form -->
        <div class="p-8 space-y-6">
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 text-emerald-900 text-sm font-semibold border border-emerald-300">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 text-rose-900 text-xs font-semibold border border-rose-300 space-y-1">
                    @foreach($errors->all() as $error)
                        <p>&bull; {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- 1. Quick Enterprise Role Account Switcher -->
            <form method="POST" action="{{ route('login.post') }}" class="p-5 bg-creamBase rounded-2xl border border-slate-200/80 space-y-3">
                @csrf
                <label class="block text-xs font-bold text-purpleSecondary uppercase tracking-widest">Quick Demo Account Switcher</label>
                <select name="user_id" class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-skyPrimary focus:border-skyPrimary transition shadow-sm">
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">
                            {{ $u->name }} — [{{ $u->roles->first()?->name ?? 'Employee' }}] ({{ $u->department?->name ?? 'Global' }})
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="w-full bg-purpleSecondary hover:bg-purpleHover text-white font-bold py-3 px-4 rounded-xl text-xs uppercase tracking-wider transition shadow">
                    Switch & Log In &rarr;
                </button>
            </form>

            <div class="flex items-center my-4">
                <div class="flex-1 border-t border-slate-200"></div>
                <span class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-widest">OR Credentials Login</span>
                <div class="flex-1 border-t border-slate-200"></div>
            </div>

            <!-- 2. Standard Email + Password Credentials Login -->
            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-widest mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full bg-creamBase border border-slate-300 rounded-2xl px-5 py-3.5 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-skyPrimary focus:border-skyPrimary transition shadow-inner" placeholder="admin@enterprise.com">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-widest mb-2">Password</label>
                    <input type="password" name="password" class="w-full bg-creamBase border border-slate-300 rounded-2xl px-5 py-3.5 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-skyPrimary focus:border-skyPrimary transition shadow-inner" placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between text-xs font-normal">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-skyPrimary focus:ring-skyPrimary">
                        <span class="text-slate-600">Remember me</span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-skyPrimary hover:bg-skyHover text-purpleSecondary font-bold py-4 px-6 rounded-2xl transition duration-200 shadow-lg text-sm uppercase tracking-wider hover:scale-[1.02] active:scale-[0.98]">
                    Sign In to Workspace &rarr;
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-100 text-center space-y-2">
                <p class="text-xs font-medium text-slate-600">
                    Need an account? 
                    <a href="{{ route('register') }}" class="text-purpleSecondary font-bold hover:text-purpleHover underline transition ml-1">Create Account Here &rarr;</a>
                </p>
                <p class="text-[11px] text-slate-400 font-semibold tracking-wide uppercase">BPM Architecture • 12 Countries • 45 Departments</p>
            </div>
        </div>
    </div>
</body>
</html>
