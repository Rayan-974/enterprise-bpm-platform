<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Enterprise BPM Platform</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
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
            <div class="inline-flex items-center justify-center w-16 h-16 bg-skyPrimary text-purpleSecondary rounded-2xl font-black text-2xl mb-4 shadow-lg">
                EB
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight">Create BPM Account</h1>
            <p class="text-xs font-bold text-skyPrimary mt-2 uppercase tracking-widest">Self-service registration for enterprise users</p>
        </div>

        <!-- Registration Form -->
        <div class="p-8">
            @if($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-rose-50 text-rose-900 text-xs font-bold border border-rose-300 space-y-1">
                    @foreach($errors->all() as $error)
                        <p>&bull; {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-widest mb-2">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-creamBase border border-slate-300 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary focus:border-skyPrimary transition shadow-inner" placeholder="e.g. Sarah Jenkins">
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-widest mb-2">Work Email Address *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-creamBase border border-slate-300 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary focus:border-skyPrimary transition shadow-inner" placeholder="sarah.jenkins@enterprise.com">
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-widest mb-2">Department</label>
                    <select name="department_id" class="w-full bg-creamBase border border-slate-300 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary focus:border-skyPrimary transition shadow-inner">
                        <option value="">-- Select Department --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }} ({{ $dept->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-widest mb-2">Password *</label>
                    <input type="password" name="password" required class="w-full bg-creamBase border border-slate-300 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary focus:border-skyPrimary transition shadow-inner" placeholder="••••••••">
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-widest mb-2">Confirm Password *</label>
                    <input type="password" name="password_confirmation" required class="w-full bg-creamBase border border-slate-300 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary focus:border-skyPrimary transition shadow-inner" placeholder="••••••••">
                </div>

                <button type="submit" class="w-full bg-skyPrimary hover:bg-skyHover text-purpleSecondary font-black py-4 px-6 rounded-2xl transition duration-200 shadow-lg text-sm uppercase tracking-wider hover:scale-[1.02] active:scale-[0.98]">
                    Register & Start Workspace &rarr;
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                <p class="text-xs font-bold text-slate-600">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-purpleSecondary font-extrabold hover:text-purpleHover underline transition ml-1">Sign In Here &rarr;</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
