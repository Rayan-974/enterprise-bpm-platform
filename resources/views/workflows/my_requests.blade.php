@extends('layouts.app')

@section('content')
<div class="space-y-6 page-fade-up">
    <!-- Top Header Row -->
    <div class="bg-white/90 backdrop-blur-md p-6 sm:p-8 rounded-3xl shadow-xl border border-slate-200/80 flex flex-col md:flex-row md:items-center justify-between gap-6 shiny-card">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold gradient-text tracking-tight">My Workflow Requests & Live Tracking</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-500 mt-1.5">View real-time progress steppers, approval decision histories, and form reports for all your submitted requests.</p>
        </div>
        <a href="{{ route('workflows.index') }}" class="shine-sweep bg-skyPrimary hover:bg-skyHover text-purpleSecondary font-bold px-6 py-3.5 rounded-2xl text-xs sm:text-sm transition shadow-lg hover:scale-105 active:scale-95 self-start md:self-center whitespace-nowrap uppercase tracking-wider min-h-[44px] flex items-center justify-center">
            + Initiate New Request
        </a>
    </div>

    <!-- Status Tabs -->
    <div class="flex space-x-3 border-b border-slate-200 overflow-x-auto pb-3">
        <a href="{{ route('workflows.myRequests') }}" class="px-5 py-2.5 rounded-2xl text-xs font-bold transition uppercase tracking-wider whitespace-nowrap {{ !$status ? 'bg-purpleSecondary text-white shadow-md font-bold' : 'bg-white text-slate-700 hover:bg-creamBase' }}">
            All My Requests
        </a>
        <a href="{{ route('workflows.myRequests', ['status' => 'in_progress']) }}" class="px-5 py-2.5 rounded-2xl text-xs font-bold transition uppercase tracking-wider whitespace-nowrap {{ $status === 'in_progress' ? 'bg-purpleSecondary text-white shadow-md font-bold' : 'bg-white text-slate-700 hover:bg-creamBase' }}">
            In Progress
        </a>
        <a href="{{ route('workflows.myRequests', ['status' => 'approved']) }}" class="px-5 py-2.5 rounded-2xl text-xs font-bold transition uppercase tracking-wider whitespace-nowrap {{ $status === 'approved' ? 'bg-emerald-600 text-white shadow-md font-bold' : 'bg-white text-slate-700 hover:bg-creamBase' }}">
            Approved
        </a>
        <a href="{{ route('workflows.myRequests', ['status' => 'rejected']) }}" class="px-5 py-2.5 rounded-2xl text-xs font-bold transition uppercase tracking-wider whitespace-nowrap {{ $status === 'rejected' ? 'bg-rose-600 text-white shadow-md font-bold' : 'bg-white text-slate-700 hover:bg-creamBase' }}">
            Rejected
        </a>
    </div>

    <!-- My Requests List / Empty State -->
    @if($instances->isEmpty())
        <div class="p-12 text-center bg-white/90 backdrop-blur-md rounded-3xl border border-slate-200/80 shadow-xl">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-3xl bg-purpleSecondary/10 text-purpleSecondary mb-4 shadow-sm text-2xl">
                📄
            </div>
            <h3 class="text-lg font-extrabold text-purpleSecondary">No Request Records Found</h3>
            <p class="text-xs font-semibold text-slate-500 mt-1 max-w-md mx-auto">You have not submitted any workflow requests under status '<span class="font-extrabold text-slate-900 capitalize">{{ str_replace('_', ' ', $status ?? 'All') }}</span>'.</p>
        </div>
    @else
        <!-- Desktop Table View (>=768px) -->
        <div class="hidden md:block bg-white/90 backdrop-blur-md rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden shiny-card">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-creamBase/90 text-purpleSecondary font-black uppercase tracking-wider border-b border-slate-200/80 text-[11px]">
                            <th class="py-4 px-6">Workflow Process</th>
                            <th class="py-4 px-6">Submission Date</th>
                            <th class="py-4 px-6">SLA Due Date</th>
                            <th class="py-4 px-6">Current Status</th>
                            <th class="py-4 px-6 text-right">Action Report</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($instances as $inst)
                            <tr class="hover:bg-creamBase/60 transition duration-150 font-medium">
                                <td class="py-4 px-6 font-bold text-purpleSecondary">
                                    {{ $inst->definition->name }}
                                    <div class="text-xs text-slate-400 font-mono font-normal">UUID: {{ $inst->uuid }}</div>
                                </td>
                                <td class="py-4 px-6 text-slate-600 font-semibold text-xs">
                                    {{ $inst->created_at->format('M d, Y @ H:i') }}
                                </td>
                                <td class="py-4 px-6 text-xs font-normal">
                                    @if($inst->due_at)
                                        <span class="{{ $inst->due_at->isPast() && $inst->status === 'in_progress' ? 'text-rose-600 font-bold' : 'text-slate-600' }}">
                                            {{ $inst->due_at->format('M d, H:i') }} ({{ $inst->due_at->diffForHumans() }})
                                        </span>
                                    @else
                                        <span class="text-slate-400">N/A</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 align-middle">
                                    <span class="inline-block whitespace-nowrap text-xs font-black px-4 py-1.5 rounded-full uppercase tracking-wider shadow-sm
                                        @if($inst->status === 'approved') bg-emerald-100 text-emerald-900 border border-emerald-300
                                        @elseif($inst->status === 'rejected') bg-rose-100 text-rose-900 border border-rose-300
                                        @else bg-sky-100 text-purpleSecondary border border-sky-300 @endif">
                                        {{ str_replace('_', ' ', $inst->status) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right align-middle">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('workflows.track', $inst->uuid) }}" class="shine-sweep inline-flex items-center justify-center whitespace-nowrap bg-purpleSecondary hover:bg-purpleHover text-white font-black text-xs px-3.5 py-2 rounded-xl transition shadow hover:scale-105 uppercase tracking-wider min-h-[38px]">
                                            Report &rarr;
                                        </a>
                                        <a href="{{ route('workflows.editRequest', $inst->uuid) }}" class="inline-flex items-center justify-center bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 font-bold px-3 py-2 rounded-xl text-xs transition shadow-sm min-h-[38px]">
                                            ✏️ Edit
                                        </a>
                                        <form method="POST" action="{{ route('workflows.destroyRequest', $inst->uuid) }}" onsubmit="return confirm('Are you sure you want to delete/cancel request #{{ $inst->uuid }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-300 font-bold px-3 py-2 rounded-xl text-xs transition shadow-sm min-h-[38px]">
                                                🗑️ Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Stacked Card View (<768px) -->
        <div class="block md:hidden space-y-4">
            @foreach($instances as $inst)
                <div class="bg-white/90 backdrop-blur-md p-5 rounded-3xl border border-slate-200/80 shadow-lg space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                        <span class="text-xs font-black text-purpleSecondary">{{ $inst->definition->name }}</span>
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider
                            @if($inst->status === 'approved') bg-emerald-100 text-emerald-900 border border-emerald-300
                            @elseif($inst->status === 'rejected') bg-rose-100 text-rose-900 border border-rose-300
                            @else bg-sky-100 text-purpleSecondary border border-sky-300 @endif">
                            {{ str_replace('_', ' ', $inst->status) }}
                        </span>
                    </div>

                    <div class="space-y-1.5 text-xs">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Submitted On</span>
                            <span class="font-bold text-slate-700">{{ $inst->created_at->format('M d, Y @ H:i') }}</span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2">
                        <span class="text-[10px] font-mono text-slate-400 truncate max-w-[120px]">#{{ $inst->uuid }}</span>
                        <div class="flex items-center space-x-1.5">
                            <a href="{{ route('workflows.track', $inst->uuid) }}" class="shine-sweep bg-purpleSecondary hover:bg-purpleHover text-white font-bold text-xs px-3 py-1.5 rounded-xl transition shadow min-h-[36px] flex items-center justify-center uppercase tracking-wider">
                                Report &rarr;
                            </a>
                            <a href="{{ route('workflows.editRequest', $inst->uuid) }}" class="bg-amber-50 text-amber-900 border border-amber-300 font-bold px-2.5 py-1.5 rounded-xl text-xs transition shadow-sm min-h-[36px] flex items-center justify-center">
                                ✏️
                            </a>
                            <form method="POST" action="{{ route('workflows.destroyRequest', $inst->uuid) }}" onsubmit="return confirm('Are you sure you want to delete/cancel request #{{ $inst->uuid }}?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-rose-50 text-rose-800 border border-rose-300 font-bold px-2.5 py-1.5 rounded-xl text-xs transition shadow-sm min-h-[36px] flex items-center justify-center">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $instances->links() }}
        </div>
    @endif
</div>
@endsection
