@extends('layouts.app')

@section('content')
<div class="space-y-8 page-fade-up">
    <!-- Header -->
    <div class="bg-white/90 backdrop-blur-md p-8 rounded-3xl shadow-xl border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-6 shiny-card">
        <div>
            <h1 class="text-3xl font-extrabold gradient-text tracking-tight">Task Management Center</h1>
            <p class="text-sm font-medium text-slate-500 mt-2">Review pending approvals, delegated tasks, completed items, and SLA overdue tasks.</p>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex space-x-3 border-b border-slate-200 overflow-x-auto pb-3">
        <a href="{{ route('tasks.index', ['status' => 'pending']) }}" class="px-5 py-2.5 rounded-2xl text-xs font-semibold transition uppercase tracking-wider {{ $status === 'pending' ? 'bg-purpleSecondary text-white shadow-md font-bold' : 'bg-white text-slate-700 hover:bg-creamBase' }}">
            Pending Approvals
        </a>
        <a href="{{ route('tasks.index', ['status' => 'overdue']) }}" class="px-5 py-2.5 rounded-2xl text-xs font-semibold transition uppercase tracking-wider {{ $status === 'overdue' ? 'bg-rose-600 text-white shadow-md font-bold' : 'bg-white text-slate-700 hover:bg-creamBase' }}">
            SLA Overdue Tasks
        </a>
        <a href="{{ route('tasks.index', ['status' => 'delegated']) }}" class="px-5 py-2.5 rounded-2xl text-xs font-semibold transition uppercase tracking-wider {{ $status === 'delegated' ? 'bg-purpleSecondary text-white shadow-md font-bold' : 'bg-white text-slate-700 hover:bg-creamBase' }}">
            Delegated Tasks
        </a>
        <a href="{{ route('tasks.index', ['status' => 'completed']) }}" class="px-5 py-2.5 rounded-2xl text-xs font-semibold transition uppercase tracking-wider {{ $status === 'completed' ? 'bg-purpleSecondary text-white shadow-md font-bold' : 'bg-white text-slate-700 hover:bg-creamBase' }}">
            Completed History
        </a>
    </div>

    <!-- Tasks List Table / Empty State -->
    <div class="bg-white/95 backdrop-blur-md rounded-3xl shadow-xl border border-slate-200/90 overflow-hidden">
        @if($tasks->isEmpty())
            <div class="p-14 text-center bg-white rounded-3xl border border-slate-200/80 shadow-md">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-purpleSecondary/10 text-purpleSecondary mb-4 shadow-sm">
                    <svg class="w-8 h-8 text-purpleSecondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
                <h3 class="text-xl font-black text-purpleSecondary">No Tasks Found</h3>
                <p class="text-sm font-semibold text-slate-600 mt-1 max-w-md mx-auto">There are currently no tasks listed under status '<span class="font-extrabold text-slate-900 capitalize">{{ str_replace('_', ' ', $status) }}</span>'.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-purpleSecondary text-white text-xs font-bold uppercase tracking-widest">
                            <th class="py-4 px-6">Workflow Process</th>
                            <th class="py-4 px-6">Step Name</th>
                            <th class="py-4 px-6">Requester</th>
                            <th class="py-4 px-6">SLA Due Date</th>
                            <th class="py-4 px-6">Status</th>
                            <th class="py-4 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($tasks as $t)
                            <tr class="hover:bg-creamBase/80 transition duration-150 font-medium">
                                <td class="py-4 px-6 font-bold text-purpleSecondary">
                                    {{ $t->workflowInstance->definition->name }}
                                    <div class="text-xs text-slate-400 font-mono font-normal">#{{ $t->workflowInstance->uuid }}</div>
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-900">
                                    {{ $t->step->name }}
                                </td>
                                <td class="py-4 px-6 text-xs font-medium text-slate-700">
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
                                    <span class="text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wider
                                        @if($t->status === 'completed') bg-emerald-100 text-emerald-900 border border-emerald-300
                                        @elseif($t->status === 'delegated') bg-purple-100 text-purpleSecondary border border-purple-300
                                        @elseif($t->due_at && $t->due_at->isPast()) bg-rose-100 text-rose-900 border border-rose-300 pulse-glow-red
                                        @else bg-skyPrimary/30 text-purpleSecondary border border-skyPrimary @endif">
                                        {{ $t->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right space-x-2">
                                    <a href="{{ route('tasks.show', $t->id) }}" class="shine-sweep inline-block bg-skyPrimary hover:bg-skyHover text-purpleSecondary font-bold text-xs px-4 py-2 rounded-xl transition shadow hover:scale-105">
                                        Review & Action
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-5 border-t border-slate-100">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
