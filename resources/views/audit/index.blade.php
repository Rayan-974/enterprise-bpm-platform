@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="bg-white p-8 rounded-3xl shadow-md border border-slate-200/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-extrabold text-purpleSecondary tracking-tight">Enterprise Audit Trail Inspector</h1>
            <p class="text-sm font-semibold text-slate-500 mt-2">Complete immutable audit logging capturing user activity, workflow state changes, IP address, and timestamps.</p>
        </div>
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
            <div class="p-5 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
