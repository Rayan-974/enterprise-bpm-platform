@extends('layouts.app')

@section('content')
<div class="space-y-8 page-fade-up">
    <!-- Header -->
    <div class="bg-white p-8 rounded-3xl shadow-md border border-slate-200/80 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-3xl font-extrabold text-purpleSecondary tracking-tight">Enterprise Audit Trail Inspector</h1>
                <span class="bg-skyPrimary/20 text-purpleSecondary border border-skyPrimary/50 text-xs font-bold px-3.5 py-1 rounded-full shadow-inner">
                    Showing {{ $logs->count() }} of {{ $logs->total() }} Records
                </span>
            </div>
            <p class="text-sm font-semibold text-slate-500 mt-2">Complete immutable audit logging capturing user activity, workflow state changes, IP address, and timestamps.</p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('audit.exportCsv') }}" class="shine-sweep bg-purpleSecondary hover:bg-purpleHover text-white font-bold text-xs px-5 py-3 rounded-2xl shadow-lg transition hover:scale-105 uppercase tracking-wider flex items-center gap-2">
                <span>📥 Export to CSV Report</span>
            </a>

            <!-- Controls: Rows per page filter -->
            <form method="GET" action="{{ route('audit.index') }}" class="flex items-center space-x-3">
            @foreach($filters as $k => $v)
                @if($v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endif
            @endforeach
            <label for="per_page" class="text-xs font-bold text-slate-600 uppercase tracking-wider whitespace-nowrap">View Display:</label>
            <select id="per_page" name="per_page" onchange="this.form.submit()" class="bg-creamBase border border-slate-300 text-purpleSecondary font-bold text-xs rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-skyPrimary focus:border-skyPrimary transition shadow-sm cursor-pointer">
                <option value="all" {{ request('per_page', 'all') == 'all' ? 'selected' : '' }}>Show All Logs</option>
                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 per page</option>
                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 per page</option>
                <option value="250" {{ request('per_page') == '250' ? 'selected' : '' }}>250 per page</option>
            </select>
        </form>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-white rounded-3xl shadow-md border border-slate-200/80 overflow-hidden">
        @if($logs->isEmpty())
            <div class="p-16 text-center bg-creamBase">
                <p class="text-sm font-bold text-slate-500">No audit log entries found.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-purpleSecondary text-white font-extrabold uppercase tracking-widest">
                            <th class="py-4 px-6">Timestamp</th>
                            <th class="py-4 px-6">Action Event</th>
                            <th class="py-4 px-6">User</th>
                            <th class="py-4 px-6">Target Entity</th>
                            <th class="py-4 px-6">IP Address</th>
                            <th class="py-4 px-6">User Agent</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-sans text-sm">
                        @foreach($logs as $log)
                            <tr class="hover:bg-creamBase/80 transition">
                                <td class="py-3.5 px-6 text-slate-500 font-mono font-bold whitespace-nowrap">
                                    {{ $log->created_at->format('Y-m-d H:i:s') }}
                                </td>
                                <td class="py-3.5 px-6 font-extrabold text-purpleSecondary">
                                    <span class="px-3 py-1 rounded-xl bg-purple-50 text-purpleSecondary border border-purple-200 font-extrabold text-xs">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-6 font-bold text-slate-900">
                                    {{ $log->user?->name ?? 'System' }}
                                </td>
                                <td class="py-3.5 px-6 text-slate-600 font-mono font-semibold">
                                    {{ class_basename($log->entity_type) }} #{{ $log->entity_id }}
                                </td>
                                <td class="py-3.5 px-6 text-slate-500 font-mono font-bold">
                                    {{ $log->ip_address ?? '127.0.0.1' }}
                                </td>
                                <td class="py-3.5 px-6 text-slate-400 truncate max-w-xs font-medium">
                                    {{ $log->user_agent ?? 'Mozilla/5.0' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
                <div class="p-5 border-t border-slate-100 bg-creamBase/50">
                    {{ $logs->appends(request()->query())->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
