@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-8">
    <div class="bg-white rounded-3xl p-8 shadow-md border border-slate-200/80">
        <div class="flex items-center justify-between border-b border-slate-100 pb-5 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-purpleSecondary tracking-tight">Notification Center</h1>
                <p class="text-sm font-semibold text-slate-500 mt-2">Multi-channel task alerts, approval updates, and SLA warning notifications.</p>
            </div>
        </div>

        @if($notifications->isEmpty())
            <div class="p-16 text-center bg-creamBase rounded-2xl border border-dashed border-slate-300">
                <p class="text-sm font-bold text-slate-500">No notifications found.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($notifications as $n)
                    <div class="p-5 rounded-2xl border {{ $n->read_at ? 'border-slate-200 bg-white' : 'border-skyPrimary bg-sky-50/40 shadow-sm' }} flex items-start justify-between gap-6 shiny-hover">
                        <div>
                            <div class="flex items-center space-x-3">
                                <span class="badge-sky text-xs font-extrabold uppercase">{{ $n->type }}</span>
                                <span class="text-xs font-semibold text-slate-400">{{ $n->created_at->diffForHumans() }}</span>
                            </div>
                            <h3 class="font-extrabold text-slate-900 text-base mt-2">{{ $n->title }}</h3>
                            <p class="text-xs font-semibold text-slate-600 mt-1 leading-relaxed">{{ $n->message }}</p>
                        </div>
                        @if(!$n->read_at)
                            <form method="POST" action="{{ route('notifications.read', $n->id) }}">
                                @csrf
                                <button type="submit" class="text-xs font-extrabold text-purpleSecondary hover:text-purpleHover underline transition">Mark Read</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
