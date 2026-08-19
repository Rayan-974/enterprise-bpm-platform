@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 page-fade-up">
    <div class="bg-white/90 backdrop-blur-md rounded-3xl p-8 shadow-xl border border-slate-200/80 shiny-card">
        <div class="flex items-center justify-between border-b border-slate-100 pb-5 mb-8">
            <div>
                <span class="badge-sky text-xs font-semibold uppercase tracking-widest mb-3 inline-block">{{ $task->workflowInstance->definition->category }}</span>
                <h1 class="text-3xl font-extrabold gradient-text tracking-tight">Review Task: {{ $task->step->name }}</h1>
                <p class="text-xs font-medium text-slate-500 mt-1.5">Workflow: {{ $task->workflowInstance->definition->name }} | Requester: {{ $task->workflowInstance->requester->name }}</p>
            </div>
            <a href="{{ route('tasks.index') }}" class="text-xs font-semibold text-slate-500 hover:text-purpleSecondary transition">&larr; Back to Tasks</a>
        </div>

        <!-- Form Payload Summary -->
        <div class="mb-8 bg-creamBase/80 p-6 rounded-3xl border border-slate-200/80">
            <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-widest mb-4">Request Form Details</h2>
            @if(is_array($task->workflowInstance->payload) && count($task->workflowInstance->payload) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($task->workflowInstance->payload as $k => $v)
                        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
                            <span class="text-xs font-semibold text-slate-400 uppercase block tracking-wider">{{ str_replace('_', ' ', $k) }}</span>
                            @if(is_array($v) && isset($v['url'], $v['name']))
                                <a href="{{ $v['url'] }}" target="_blank" download class="inline-flex items-center space-x-2 mt-2 bg-sky-50 text-purpleSecondary hover:bg-sky-100 border border-sky-300 font-bold px-3 py-1.5 rounded-xl text-xs transition shadow-sm">
                                    <span>📎 {{ $v['name'] }} ({{ $v['size'] ?? 'Download' }})</span>
                                </a>
                            @else
                                <span class="text-base font-bold text-slate-900 mt-1 block">{{ is_array($v) ? implode(', ', $v) : $v }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        @if($task->status === 'pending')
            <!-- Action Form -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6 border-t border-slate-100">
                <!-- Approve / Reject Decision Form -->
                <div class="space-y-5">
                    <h3 class="text-sm font-bold text-purpleSecondary uppercase tracking-widest">Approval Decision</h3>
                    <form method="POST" action="{{ route('tasks.approve', $task->id) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-widest mb-2">Decision Notes / Justification</label>
                            <textarea name="comments" rows="3" class="w-full bg-creamBase/80 border border-slate-300 rounded-2xl p-4 text-sm font-medium focus:ring-2 focus:ring-skyPrimary" placeholder="Add approval or rejection notes..."></textarea>
                        </div>
                        
                        <div class="flex items-center space-x-3 pt-2">
                            <button type="submit" class="shine-sweep flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 px-6 rounded-2xl text-xs uppercase tracking-wider shadow-lg transition hover:scale-105">
                                Approve Request
                            </button>
                            <button type="submit" formaction="{{ route('tasks.reject', $task->id) }}" class="shine-sweep flex-1 bg-rose-600 hover:bg-rose-700 text-white font-bold py-4 px-6 rounded-2xl text-xs uppercase tracking-wider shadow-lg transition hover:scale-105">
                                Reject Request
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Delegate Action -->
                <div class="space-y-5 border-l border-slate-100 pl-0 md:pl-8">
                    <h3 class="text-sm font-bold text-purpleSecondary uppercase tracking-widest">Delegate Task</h3>
                    <form method="POST" action="{{ route('tasks.delegate', $task->id) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-widest mb-2">Select User</label>
                            <select name="delegate_user_id" required class="w-full bg-creamBase/80 border border-slate-300 rounded-2xl p-4 text-sm font-medium">
                                <option value="">-- Select Delegate User --</option>
                                @foreach($eligibleUsers as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->department?->name }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-widest mb-2">Delegation Reason</label>
                            <textarea name="comments" rows="2" class="w-full bg-creamBase/80 border border-slate-300 rounded-2xl p-4 text-sm font-medium" placeholder="Delegation reason..."></textarea>
                        </div>
                        <button type="submit" class="shine-sweep w-full bg-purpleSecondary hover:bg-purpleHover text-white font-bold py-4 px-6 rounded-2xl text-xs uppercase tracking-wider shadow-lg transition hover:scale-105">
                            Delegate Task &rarr;
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="p-6 rounded-2xl bg-white text-slate-900 text-sm font-bold text-center border border-slate-200 shadow-md">
                This task has already been processed with status: <span class="uppercase font-extrabold text-purpleSecondary">{{ $task->status }}</span>
            </div>
        @endif
    </div>
</div>
@endsection
