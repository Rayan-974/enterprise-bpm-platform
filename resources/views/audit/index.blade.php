@extends('layouts.app')

@section('content')
<div class="space-y-6 page-fade-up">
    <!-- Top Header Row -->
    <div class="bg-white/90 backdrop-blur-md p-6 sm:p-8 rounded-3xl shadow-xl border border-slate-200/80 flex flex-col md:flex-row md:items-center justify-between gap-6 shiny-card">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold gradient-text tracking-tight">Enterprise Audit Trail Inspector</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-500 mt-1.5">Complete immutable audit logging capturing user activity, entity mutations, IP address, and timestamps.</p>
        </div>

        <!-- Action Cluster (Right Aligned) -->
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('audit.exportCsv') }}" class="shine-sweep bg-purpleSecondary hover:bg-purpleHover text-white font-bold text-xs px-5 py-3 rounded-2xl shadow-lg transition hover:scale-105 uppercase tracking-wider flex items-center gap-2 min-h-[44px]">
                <span>📥 Export to CSV Report</span>
            </a>

            <!-- Controls: Rows per page filter -->
            <form method="GET" action="{{ route('audit.index') }}" class="flex items-center space-x-2">
                @foreach($filters as $k => $v)
                    @if($v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endif
                @endforeach
                <label for="per_page" class="text-xs font-bold text-slate-600 uppercase tracking-wider whitespace-nowrap hidden sm:inline">Display:</label>
                <select id="per_page" name="per_page" onchange="this.form.submit()" class="bg-creamBase border border-slate-300 text-purpleSecondary font-bold text-xs rounded-2xl px-4 py-3 focus:ring-2 focus:ring-skyPrimary focus:border-skyPrimary transition shadow-sm cursor-pointer min-h-[44px]">
                    <option value="all" {{ $perPage === 'all' ? 'selected' : '' }}>Show All Logs</option>
                    <option value="50" {{ $perPage == '50' ? 'selected' : '' }}>50 Per Page</option>
                    <option value="100" {{ $perPage == '100' ? 'selected' : '' }}>100 Per Page</option>
                    <option value="250" {{ $perPage == '250' ? 'selected' : '' }}>250 Per Page</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Meta Status Row -->
    <div class="flex items-center justify-between px-2">
        <div class="flex items-center space-x-3">
            <span class="bg-skyPrimary/20 text-purpleSecondary border border-skyPrimary/50 text-xs font-extrabold px-4 py-1.5 rounded-full shadow-inner">
                Showing {{ $logs->count() }} of {{ $logs->total() }} Records
            </span>
        </div>
    </div>

    <!-- Audit Logs Main Container -->
    @if($logs->isEmpty())
        <div class="bg-white/90 backdrop-blur-md rounded-3xl p-12 text-center border border-slate-200/80 shadow-xl">
            <div class="w-16 h-16 bg-creamBase text-purpleSecondary rounded-3xl flex items-center justify-center mx-auto mb-4 text-2xl shadow-inner">
                📋
            </div>
            <h3 class="text-lg font-extrabold text-purpleSecondary">No Audit Trail Logs Recorded</h3>
            <p class="text-xs font-medium text-slate-500 mt-1">There are no audit events matching the selected filter criteria.</p>
        </div>
    @else
        <!-- Desktop & Tablet Data Table (>=768px) -->
        <div class="hidden md:block bg-white/90 backdrop-blur-md rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden shiny-card">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-creamBase/90 text-purpleSecondary font-black uppercase tracking-wider border-b border-slate-200/80 text-[11px]">
                            <th class="py-4 px-5">Timestamp</th>
                            <th class="py-4 px-5">Action Event</th>
                            <th class="py-4 px-5">Actor / User</th>
                            <th class="py-4 px-5">Target Entity</th>
                            <th class="py-4 px-5">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($logs as $log)
                            <tr class="hover:bg-creamBase/60 transition font-medium">
                                <td class="py-4 px-5 text-slate-500 font-mono text-xs whitespace-nowrap">
                                    {{ $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : 'N/A' }}
                                </td>
                                <td class="py-4 px-5">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                                        @if(str_contains($log->action, 'create') || str_contains($log->action, 'store') || str_contains($log->action, 'approve')) bg-emerald-100 text-emerald-900 border border-emerald-300
                                        @elseif(str_contains($log->action, 'delete') || str_contains($log->action, 'reject')) bg-rose-100 text-rose-900 border border-rose-300
                                        @else bg-sky-100 text-purpleSecondary border border-sky-300 @endif">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="py-4 px-5 font-bold text-slate-900">
                                    {{ $log->user ? $log->user->name : 'System / Automated' }}
                                    @if($log->user)
                                        <span class="block text-[11px] font-semibold text-slate-400 font-mono">{{ $log->user->email }}</span>
                                    @endif
                                </td>
                                <td class="py-4 px-5 font-bold text-slate-700">
                                    {{ $log->auditable_type ? class_basename($log->auditable_type) : 'N/A' }}
                                    @if($log->auditable_id)
                                        <span class="text-xs font-mono text-slate-400">#{{ $log->auditable_id }}</span>
                                    @endif
                                </td>
                                <td class="py-4 px-5 text-slate-500 font-mono text-xs">
                                    {{ $log->ip_address ?? '127.0.0.1' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Stacked Cards Layout (<768px) -->
        <div class="block md:hidden space-y-4">
            @foreach($logs as $log)
                <div class="bg-white/90 backdrop-blur-md p-5 rounded-3xl border border-slate-200/80 shadow-lg space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                            @if(str_contains($log->action, 'create') || str_contains($log->action, 'store') || str_contains($log->action, 'approve')) bg-emerald-100 text-emerald-900 border border-emerald-300
                            @elseif(str_contains($log->action, 'delete') || str_contains($log->action, 'reject')) bg-rose-100 text-rose-900 border border-rose-300
                            @else bg-sky-100 text-purpleSecondary border border-sky-300 @endif">
                            {{ $log->action }}
                        </span>
                        <span class="text-[11px] font-mono text-slate-400">{{ $log->created_at ? $log->created_at->format('M d, H:i') : '' }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Actor / User</span>
                            <span class="font-extrabold text-slate-900">{{ $log->user ? $log->user->name : 'System' }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Target Entity</span>
                            <span class="font-bold text-slate-700">{{ $log->auditable_type ? class_basename($log->auditable_type) : 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">IP Address</span>
                            <span class="font-mono text-slate-500">{{ $log->ip_address ?? '127.0.0.1' }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($perPage !== 'all' && method_exists($logs, 'links'))
            <div class="pt-4">
                {{ $logs->appends(request()->query())->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
