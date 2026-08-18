@extends('layouts.app')

@section('content')
<div class="space-y-8 page-fade-up">
    <!-- Welcome Header Banner -->
    <div class="bg-gradient-to-r from-purpleSecondary via-purpleHover to-purpleSecondary rounded-3xl p-8 text-white shadow-2xl border border-skyPrimary/30 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative overflow-hidden shiny-card">
        <div class="relative z-10">
            <h1 class="text-3xl font-extrabold tracking-tight">
                Welcome back, <span class="text-skyPrimary">{{ auth()->user()->name }}</span>
            </h1>
            <p class="text-sm text-skyPrimary/90 mt-2 font-medium tracking-wide">
                Department: {{ auth()->user()->department?->name ?? 'Global Corporate' }} | Country: {{ auth()->user()->country_code ?? 'US' }} | Role: {{ auth()->user()->roles->first()?->name }}
            </p>
        </div>
        <a href="{{ route('workflows.index') }}" class="shine-sweep relative z-10 bg-skyPrimary hover:bg-skyHover text-purpleSecondary font-bold px-7 py-4 rounded-2xl text-sm transition-all transform hover:scale-105 active:scale-95 shadow-xl whitespace-nowrap uppercase tracking-wider">
            + Start New Workflow Request
        </a>
    </div>

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Running Workflows -->
        <div class="bg-white/90 backdrop-blur-md rounded-3xl p-7 shadow-xl border border-slate-200/80 shiny-card">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Running Workflows</span>
                <span class="p-3 bg-skyPrimary/20 text-purpleSecondary rounded-2xl shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </span>
            </div>
            <div class="text-4xl font-extrabold text-purpleSecondary mt-4 tracking-tight stat-countup">{{ $kpis['in_progress'] }}</div>
            <div class="text-xs font-normal text-slate-500 mt-1">Active instances across enterprise</div>
        </div>

        <!-- Overdue SLA Tasks -->
        <div class="bg-white/90 backdrop-blur-md rounded-3xl p-7 shadow-xl border border-slate-200/80 shiny-card {{ $kpis['overdue_tasks'] > 0 ? 'pulse-glow-red' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Overdue SLA Tasks</span>
                <span class="p-3 bg-rose-100 text-rose-600 rounded-2xl shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
            </div>
            <div class="text-4xl font-extrabold text-rose-600 mt-4 tracking-tight stat-countup">{{ $kpis['overdue_tasks'] }}</div>
            <div class="text-xs font-normal text-slate-500 mt-1">Tasks breaching SLA threshold</div>
        </div>

        <!-- SLA Compliance Rate -->
        <div class="bg-white/90 backdrop-blur-md rounded-3xl p-7 shadow-xl border border-slate-200/80 shiny-card">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-widest">SLA Compliance</span>
                <span class="p-3 bg-emerald-100 text-emerald-600 rounded-2xl shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
            </div>
            <div class="text-4xl font-extrabold text-emerald-600 mt-4 tracking-tight stat-countup">{{ $kpis['sla_compliance_rate'] }}%</div>
            <div class="text-xs font-normal text-slate-500 mt-1">On-time step completions</div>
        </div>

        <!-- Completed Workflows -->
        <div class="bg-white/90 backdrop-blur-md rounded-3xl p-7 shadow-xl border border-slate-200/80 shiny-card">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Total Approved</span>
                <span class="p-3 bg-purple-100 text-purpleSecondary rounded-2xl shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </span>
            </div>
            <div class="text-4xl font-extrabold text-purpleSecondary mt-4 tracking-tight stat-countup">{{ $kpis['approved'] }}</div>
            <div class="text-xs font-normal text-slate-500 mt-1">Successfully completed requests</div>
        </div>
    </div>

    <!-- Main Dashboard Panels -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- My Pending Approvals -->
        <div class="lg:col-span-2 bg-white/90 backdrop-blur-md rounded-3xl p-8 shadow-xl border border-slate-200/80">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                <h2 class="text-xl font-bold gradient-text flex items-center gap-3">
                    <svg class="w-6 h-6 text-skyPrimary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    My Action Items (Pending Tasks)
                </h2>
                <a href="{{ route('tasks.index') }}" class="text-xs font-semibold text-purpleSecondary hover:text-purpleHover underline transition">View All &rarr;</a>
            </div>

            @if($myPendingTasks->isEmpty())
                <div class="p-10 text-center bg-white rounded-3xl border border-slate-200 shadow-sm">
                    <h3 class="text-base font-extrabold text-purpleSecondary">No Pending Approvals</h3>
                    <p class="text-sm font-medium text-slate-600 mt-1">You have no pending task approvals right now.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($myPendingTasks as $task)
                        <div class="p-5 rounded-3xl border border-slate-200/80 bg-creamBase/60 hover:border-skyPrimary transition-all duration-300 shiny-card flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center space-x-2">
                                    <span class="badge-sky text-xs font-semibold">{{ $task->workflowInstance->definition->name }}</span>
                                    @if($task->due_at && $task->due_at->isPast())
                                        <span class="bg-rose-100 text-rose-800 text-xs font-bold px-3 py-0.5 rounded-full border border-rose-300 pulse-glow-red">OVERDUE</span>
                                    @endif
                                </div>
                                <h3 class="font-bold text-slate-900 mt-2 text-base">Step: {{ $task->step->name }}</h3>
                                <p class="text-xs font-medium text-slate-500 mt-1">Requested by {{ $task->workflowInstance->requester->name }} | {{ $task->created_at->diffForHumans() }}</p>
                            </div>
                            <a href="{{ route('tasks.show', $task->id) }}" class="shine-sweep bg-purpleSecondary hover:bg-purpleHover text-white text-xs font-bold px-6 py-3 rounded-2xl transition shadow-lg hover:scale-105 self-start sm:self-center uppercase tracking-wider">
                                Review & Action &rarr;
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Recent Audit Log Feed -->
        <div class="bg-white/90 backdrop-blur-md rounded-3xl p-8 shadow-xl border border-slate-200/80">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                <h2 class="text-xl font-bold gradient-text flex items-center gap-3">
                    <svg class="w-6 h-6 text-skyPrimary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Live Audit Activity
                </h2>
                <a href="{{ route('audit.index') }}" class="text-xs font-semibold text-purpleSecondary hover:text-purpleHover underline transition">View Log &rarr;</a>
            </div>

            <div class="space-y-4">
                @foreach($recentAudits as $log)
                    <div class="p-4 rounded-2xl bg-creamBase/80 text-xs border border-slate-200/80 hover:border-skyPrimary transition shiny-card">
                        <div class="flex items-center justify-between font-bold text-purpleSecondary text-xs">
                            <span>{{ $log->action }}</span>
                            <span class="text-slate-400 font-normal">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-slate-700 font-medium mt-1">User: {{ $log->user?->name ?? 'System' }} (IP: {{ $log->ip_address ?? '127.0.0.1' }})</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
