@extends('layouts.app')

@section('content')
<div class="space-y-6 page-fade-up">
    <!-- Top Header Row -->
    <div class="bg-white/90 backdrop-blur-md p-6 sm:p-8 rounded-3xl shadow-xl border border-slate-200/80 flex flex-col md:flex-row md:items-center justify-between gap-6 shiny-card">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold gradient-text tracking-tight">Task Management Center</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-500 mt-1.5">Review pending approvals, delegated tasks, completed items, and SLA overdue tasks.</p>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex space-x-3 border-b border-slate-200 overflow-x-auto pb-3">
        <a href="{{ route('tasks.index', ['status' => 'pending']) }}" class="px-5 py-2.5 rounded-2xl text-xs font-bold transition uppercase tracking-wider whitespace-nowrap {{ $status === 'pending' ? 'bg-purpleSecondary text-white shadow-md font-bold' : 'bg-white text-slate-700 hover:bg-creamBase' }}">
            Pending Approvals
        </a>
        <a href="{{ route('tasks.index', ['status' => 'overdue']) }}" class="px-5 py-2.5 rounded-2xl text-xs font-bold transition uppercase tracking-wider whitespace-nowrap {{ $status === 'overdue' ? 'bg-rose-600 text-white shadow-md font-bold' : 'bg-white text-slate-700 hover:bg-creamBase' }}">
            SLA Overdue Tasks
        </a>
        <a href="{{ route('tasks.index', ['status' => 'delegated']) }}" class="px-5 py-2.5 rounded-2xl text-xs font-bold transition uppercase tracking-wider whitespace-nowrap {{ $status === 'delegated' ? 'bg-purpleSecondary text-white shadow-md font-bold' : 'bg-white text-slate-700 hover:bg-creamBase' }}">
            Delegated Tasks
        </a>
        <a href="{{ route('tasks.index', ['status' => 'completed']) }}" class="px-5 py-2.5 rounded-2xl text-xs font-bold transition uppercase tracking-wider whitespace-nowrap {{ $status === 'completed' ? 'bg-purpleSecondary text-white shadow-md font-bold' : 'bg-white text-slate-700 hover:bg-creamBase' }}">
            Completed History
        </a>
    </div>

    <!-- Tasks List Table / Empty State -->
    @if($tasks->isEmpty())
        <div class="p-12 text-center bg-white/90 backdrop-blur-md rounded-3xl border border-slate-200/80 shadow-xl">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-3xl bg-purpleSecondary/10 text-purpleSecondary mb-4 shadow-sm text-2xl">
                📥
            </div>
            <h3 class="text-lg font-extrabold text-purpleSecondary">No Tasks Found</h3>
            <p class="text-xs font-semibold text-slate-500 mt-1 max-w-md mx-auto">There are currently no tasks listed under status '<span class="font-extrabold text-slate-900 capitalize">{{ str_replace('_', ' ', $status) }}</span>'.</p>
        </div>
    @else
        <!-- Desktop Table View (>=768px) -->
        <div class="hidden md:block bg-white/90 backdrop-blur-md rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden shiny-card">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-creamBase/90 text-purpleSecondary font-black uppercase tracking-wider border-b border-slate-200/80 text-[11px]">
                            <th class="py-4 px-6">Workflow Process</th>
                            <th class="py-4 px-6">Step Name</th>
                            <th class="py-4 px-6">Requester</th>
                            <th class="py-4 px-6">SLA Due Date</th>
                            <th class="py-4 px-6">Status</th>
                            <th class="py-4 px-6 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($tasks as $t)
                            <tr class="hover:bg-creamBase/60 transition duration-150 font-medium">
                                <td class="py-4 px-6 font-bold text-purpleSecondary">
                                    {{ $t->workflowInstance->definition->name }}
                                    <div class="text-xs text-slate-400 font-mono font-normal">#{{ $t->workflowInstance->uuid }}</div>
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-900">
                                    {{ $t->step->name }}
                                </td>
                                <td class="py-4 px-6 text-xs font-semibold text-slate-700">
                                    {{ $t->workflowInstance->requester->name }}
                                </td>
                                <td class="py-4 px-6 text-xs font-normal">
                                    @if($t->due_at)
                                        <span class="{{ $t->due_at->isPast() && $t->status === 'pending' ? 'text-rose-600 font-bold' : 'text-slate-600' }}">
                                            {{ $t->due_at->format('M d, H:i') }} ({{ $t->due_at->diffForHumans() }})
                                        </span>
                                    @else
                                        <span class="text-slate-400">N/A</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider
                                        @if($t->status === 'completed') bg-emerald-100 text-emerald-900 border border-emerald-300
                                        @elseif($t->status === 'delegated') bg-purple-100 text-purpleSecondary border border-purple-300
                                        @elseif($t->due_at && $t->due_at->isPast()) bg-rose-100 text-rose-900 border border-rose-300 pulse-glow-red
                                        @else bg-sky-100 text-purpleSecondary border border-sky-300 @endif">
                                        {{ $t->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <a href="{{ route('tasks.show', $t->id) }}" class="shine-sweep inline-block bg-skyPrimary hover:bg-skyHover text-purpleSecondary font-bold text-xs px-4 py-2.5 rounded-xl transition shadow hover:scale-105 min-h-[38px] uppercase tracking-wider">
                                        Review & Action
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Stacked Card View (<768px) -->
        <div class="block md:hidden space-y-4">
            @foreach($tasks as $t)
                <div class="bg-white/90 backdrop-blur-md p-5 rounded-3xl border border-slate-200/80 shadow-lg space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                        <span class="text-xs font-black text-purpleSecondary">{{ $t->workflowInstance->definition->name }}</span>
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider
                            @if($t->status === 'completed') bg-emerald-100 text-emerald-900 border border-emerald-300
                            @elseif($t->status === 'delegated') bg-purple-100 text-purpleSecondary border border-purple-300
                            @elseif($t->due_at && $t->due_at->isPast()) bg-rose-100 text-rose-900 border border-rose-300 pulse-glow-red
                            @else bg-sky-100 text-purpleSecondary border border-sky-300 @endif">
                            {{ $t->status }}
                        </span>
                    </div>

                    <div class="space-y-1.5 text-xs">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Step Name</span>
                            <span class="font-extrabold text-slate-900">{{ $t->step->name }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Requester</span>
                            <span class="font-bold text-slate-700">{{ $t->workflowInstance->requester->name }}</span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[11px] font-mono text-slate-400">#{{ $t->workflowInstance->uuid }}</span>
                        <a href="{{ route('tasks.show', $t->id) }}" class="shine-sweep bg-skyPrimary hover:bg-skyHover text-purpleSecondary font-bold text-xs px-4 py-2 rounded-xl transition shadow min-h-[38px] flex items-center justify-center uppercase tracking-wider">
                            Review & Action &rarr;
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $tasks->links() }}
        </div>
    @endif
</div>
@endsection
