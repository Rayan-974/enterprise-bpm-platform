@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 page-fade-up">
    <!-- Header -->
    <div class="bg-white/90 backdrop-blur-md rounded-3xl p-8 shadow-xl border border-slate-200/80 flex flex-col md:flex-row md:items-center justify-between gap-6 shiny-card">
        <div>
            <div class="flex items-center space-x-3 mb-3">
                <span class="badge-sky text-xs font-black uppercase tracking-widest">{{ $instance->definition->category }}</span>
                <span class="text-xs font-mono font-bold text-slate-400">UUID: {{ $instance->uuid }}</span>
            </div>
            <h1 class="text-3xl font-black text-purpleSecondary tracking-tight">{{ $instance->definition->name }}</h1>
            <p class="text-xs font-bold text-slate-500 mt-2">Requested by <strong class="text-slate-900 font-black">{{ $instance->requester->name }}</strong> on {{ $instance->created_at->format('M d, Y @ H:i') }}</p>
        </div>

        <div class="text-right">
            <span class="inline-block px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg
                @if($instance->status === 'approved') bg-emerald-100 text-emerald-900 border border-emerald-300 glow-ring-sky
                @elseif($instance->status === 'rejected') bg-rose-100 text-rose-900 border border-rose-300 pulse-glow-red
                @else bg-skyPrimary/30 text-purpleSecondary border border-skyPrimary glow-ring-purple @endif">
                Status: {{ str_replace('_', ' ', $instance->status) }}
            </span>
            @if($instance->due_at)
                <p class="text-xs font-extrabold text-slate-500 mt-2">Overall SLA Due: {{ $instance->due_at->diffForHumans() }}</p>
            @endif

            @if(auth()->check() && (auth()->id() === $instance->requester_id || auth()->user()->hasRole(['Super Admin', 'Department Admin'])))
                <div class="flex items-center justify-end space-x-2 pt-2">
                    <a href="{{ route('workflows.editRequest', $instance->uuid) }}" class="bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 font-bold py-1.5 px-3 rounded-xl text-xs transition shadow-sm">
                        ✏️ Edit Request
                    </a>
                    <form method="POST" action="{{ route('workflows.destroyRequest', $instance->uuid) }}" onsubmit="return confirm('Are you sure you want to delete/cancel request #{{ $instance->uuid }}?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-300 font-bold py-1.5 px-3 rounded-xl text-xs transition shadow-sm">
                            🗑️ Delete Request
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <!-- Visual Stepper Progress Bar -->
    <div class="bg-white/90 backdrop-blur-md rounded-3xl p-8 shadow-xl border border-slate-200/80">
        <h2 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-8">Workflow Progress Stepper</h2>
        
        <div class="relative flex items-center justify-between">
            <div class="absolute left-0 top-1/2 transform -translate-y-1/2 w-full h-2 bg-slate-200 rounded-full -z-0"></div>

            @foreach($instance->definition->steps as $index => $step)
                @php
                    $isCompleted = false;
                    $isCurrent = $instance->current_step_id === $step->id && $instance->status === 'in_progress';
                    
                    $stepApproval = $instance->approvals->firstWhere('step_id', $step->id);
                    if ($stepApproval || ($instance->status === 'approved' && $index < $instance->definition->steps->count())) {
                        $isCompleted = true;
                    }
                @endphp

                <div class="relative z-10 flex flex-col items-center group">
                    <div class="w-14 h-14 rounded-full flex items-center justify-center font-black text-lg transition-all duration-300 shadow-xl
                        @if($isCompleted) bg-emerald-500 text-white ring-4 ring-emerald-100 glow-ring-sky
                        @elseif($isCurrent) bg-skyPrimary text-purpleSecondary ring-4 ring-sky-200 animate-pulse scale-110 glow-ring-purple
                        @else bg-slate-200 text-slate-600 @endif">
                        @if($isCompleted)
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        @else
                            {{ $step->step_order }}
                        @endif
                    </div>

                    <span class="text-xs font-black mt-3 text-center max-w-[130px] line-clamp-2 {{ $isCurrent ? 'text-purpleSecondary font-black text-sm' : 'text-slate-700' }}">
                        {{ $step->name }}
                    </span>
                    <span class="text-[11px] font-bold text-slate-400 capitalize mt-0.5">{{ $step->assignee_type }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Submitted Payload Details & History -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-white/90 backdrop-blur-md rounded-3xl p-8 shadow-xl border border-slate-200/80">
            <h2 class="text-sm font-black text-purpleSecondary uppercase tracking-widest mb-6 border-b border-slate-100 pb-3">Submitted Form Payload</h2>
            @if(is_array($instance->payload) && count($instance->payload) > 0)
                <div class="space-y-4">
                    @foreach($instance->payload as $key => $val)
                        <div class="flex flex-col bg-creamBase/80 p-4 rounded-2xl border border-slate-200/80">
                            <span class="text-xs font-black text-slate-500 uppercase tracking-widest">{{ str_replace('_', ' ', $key) }}</span>
                            @if(is_array($val) && isset($val['url'], $val['name']))
                                <a href="{{ $val['url'] }}" target="_blank" download class="inline-flex items-center space-x-2 mt-2 bg-sky-50 text-purpleSecondary hover:bg-sky-100 border border-sky-300 font-bold px-3 py-1.5 rounded-xl text-xs transition shadow-sm w-fit">
                                    <span>📎 {{ $val['name'] }} ({{ $val['size'] ?? 'Download' }})</span>
                                </a>
                            @else
                                <span class="text-base font-extrabold text-slate-900 mt-1">{{ is_array($val) ? implode(', ', $val) : $val }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs font-semibold text-slate-500 italic">No payload submitted.</p>
            @endif
        </div>

        <!-- Approval History -->
        <div class="bg-white/90 backdrop-blur-md rounded-3xl p-8 shadow-xl border border-slate-200/80">
            <h2 class="text-sm font-black text-purpleSecondary uppercase tracking-widest mb-6 border-b border-slate-100 pb-3">Approval Decision History</h2>
            @if($instance->approvals->isEmpty())
                <div class="p-8 text-center bg-creamBase/80 rounded-2xl border border-dashed border-slate-300">
                    <p class="text-xs font-bold text-slate-500">Pending review from assigned step approvers...</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($instance->approvals as $appr)
                        <div class="p-5 rounded-2xl border border-slate-200/80 bg-creamBase/80 shiny-card">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-black text-purpleSecondary">{{ $appr->approver->name }}</span>
                                <span class="text-xs font-black px-3.5 py-1 rounded-full uppercase tracking-wider
                                    @if($appr->decision === 'approved') bg-emerald-100 text-emerald-900 border border-emerald-300
                                    @else bg-rose-100 text-rose-900 border border-rose-300 @endif">
                                    {{ $appr->decision }}
                                </span>
                            </div>
                            <p class="text-xs font-bold text-slate-700 mt-2">Step: {{ $appr->step->name }}</p>
                            @if($appr->comments)
                                <p class="text-xs font-bold text-slate-600 italic mt-2 bg-white p-3 rounded-xl border border-slate-200">"{{ $appr->comments }}"</p>
                            @endif
                            <div class="text-[11px] font-bold text-slate-400 mt-3 flex justify-between">
                                <span>IP: {{ $appr->ip_address }}</span>
                                <span>{{ $appr->created_at->format('M d, H:i') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@if($instance->status === 'approved')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof celebrateSuccess === 'function') {
            setTimeout(celebrateSuccess, 300);
        }
    });
</script>
@endif
@endsection
