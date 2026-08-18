@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-purpleSecondary via-purpleHover to-purpleSecondary rounded-3xl p-8 text-white shadow-xl border border-skyPrimary/30 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="flex items-center space-x-6">
            <div class="w-18 h-18 rounded-2xl bg-skyPrimary text-purpleSecondary font-black text-3xl flex items-center justify-center shadow-lg">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight">{{ $user->name }}</h1>
                <p class="text-sm font-semibold text-skyPrimary mt-1">
                    Role: <span class="bg-purpleHover px-3 py-1 rounded-full text-white font-extrabold border border-skyPrimary/30">{{ $user->roles->first()?->name ?? 'Employee' }}</span>
                    | Department: {{ $user->department?->name ?? 'Global Corporate' }}
                </p>
                <p class="text-xs font-mono text-slate-300 mt-2">Email: {{ $user->email }} | Employee ID: {{ $user->employee_id ?? 'EMP-' . $user->id }}</p>
            </div>
        </div>
    </div>

    <!-- Edit Profile & Security Details -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Account Details Form -->
        <div class="bg-white rounded-3xl p-8 shadow-md border border-slate-200/80">
            <h2 class="text-xl font-extrabold text-purpleSecondary mb-6 flex items-center gap-3 border-b border-slate-100 pb-3">
                <svg class="w-6 h-6 text-skyPrimary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Personal Profile Details
            </h2>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-widest mb-2">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full bg-creamBase border border-slate-300 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary focus:border-skyPrimary transition shadow-inner">
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-widest mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full bg-creamBase border border-slate-300 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary focus:border-skyPrimary transition shadow-inner">
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-widest mb-2">Department Assignment</label>
                    <select name="department_id" class="w-full bg-creamBase border border-slate-300 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary focus:border-skyPrimary transition shadow-inner">
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $user->department_id == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }} ({{ $dept->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <h3 class="text-xs font-extrabold text-purpleSecondary uppercase tracking-widest mb-3">Change Security Password</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Current Password</label>
                            <input type="password" name="current_password" class="w-full bg-creamBase border border-slate-300 rounded-2xl px-4 py-3 text-sm font-semibold" placeholder="••••••••">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">New Password</label>
                            <input type="password" name="new_password" class="w-full bg-creamBase border border-slate-300 rounded-2xl px-4 py-3 text-sm font-semibold" placeholder="••••••••">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation" class="w-full bg-creamBase border border-slate-300 rounded-2xl px-4 py-3 text-sm font-semibold" placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-skyPrimary hover:bg-skyHover text-purpleSecondary font-black py-4 px-6 rounded-2xl transition shadow-lg text-sm uppercase tracking-wider hover:scale-[1.02]">
                    Save Profile Changes &rarr;
                </button>
            </form>
        </div>

        <!-- Digital Signature Ledger & Activity -->
        <div class="bg-white rounded-3xl p-8 shadow-md border border-slate-200/80 space-y-6">
            <h2 class="text-xl font-extrabold text-purpleSecondary flex items-center gap-3 border-b border-slate-100 pb-3">
                <svg class="w-6 h-6 text-skyPrimary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                Digital Signature Cryptographic Ledger
            </h2>

            @if($digitalSignatures->isEmpty())
                <div class="p-10 text-center bg-creamBase rounded-2xl border border-dashed border-slate-300">
                    <p class="text-sm font-bold text-slate-500">No cryptographic signatures recorded yet.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($digitalSignatures as $sig)
                        <div class="p-4 rounded-2xl bg-creamBase border border-slate-200/80 space-y-1.5 text-xs">
                            <div class="flex items-center justify-between font-extrabold text-purpleSecondary">
                                <span>{{ $sig->workflowInstance->definition->name }}</span>
                                <span class="text-slate-400 font-mono text-[10px]">{{ $sig->signed_at->format('M d, Y @ H:i') }}</span>
                            </div>
                            <div class="font-mono text-[11px] text-slate-600 truncate">
                                SHA-256: {{ $sig->signature_hash }}
                            </div>
                            <div class="text-slate-400 font-semibold text-[10px]">
                                IP Address: {{ $sig->ip_address }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
