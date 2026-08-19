@extends('layouts.app')

@section('content')
<div class="space-y-8 page-fade-up">
    <!-- Header -->
    <div class="bg-white/90 backdrop-blur-md p-8 rounded-3xl shadow-xl border border-slate-200/80 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 shiny-card">
        <div>
            <h1 class="text-3xl font-extrabold gradient-text tracking-tight">Workflow Analytics & SLA Intelligence</h1>
            <p class="text-sm font-medium text-slate-500 mt-2">Real-time SLA monitoring, bottleneck step identification, and department efficiency scores.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('analytics.exportCsv') }}" class="shine-sweep bg-purpleSecondary hover:bg-purpleHover text-white font-bold text-xs px-5 py-3 rounded-2xl shadow-lg transition hover:scale-105 uppercase tracking-wider flex items-center gap-2">
                <span>📥 Export to CSV Report</span>
            </a>
            <form method="POST" action="{{ route('analytics.scan') }}">
                @csrf
                <button type="submit" class="shine-sweep bg-skyPrimary hover:bg-skyHover text-purpleSecondary font-bold text-xs px-5 py-3 rounded-2xl border border-skyPrimary shadow-lg transition hover:scale-105 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-purpleSecondary animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    <span>Run Auto-Scan Now</span>
                </button>
            </form>
        </div>
    </div>

    <!-- SLA & Process Overview Cards (With Stat Count-Up) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white/90 backdrop-blur-md rounded-3xl p-7 shadow-xl border border-slate-200/80 text-center shiny-card">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-widest block">SLA Compliance Rate</span>
            <div class="text-5xl font-extrabold text-emerald-600 mt-4 tracking-tight stat-countup">{{ $kpis['sla_compliance_rate'] }}%</div>
            <p class="text-xs font-normal text-slate-400 mt-2">Percentage of tasks resolved within SLA duration</p>
        </div>

        <div class="bg-white/90 backdrop-blur-md rounded-3xl p-7 shadow-xl border border-slate-200/80 text-center shiny-card">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-widest block">Total Workflows Executed</span>
            <div class="text-5xl font-extrabold text-purpleSecondary mt-4 tracking-tight stat-countup">{{ $kpis['total_workflows'] }}</div>
            <p class="text-xs font-normal text-slate-400 mt-2">Across 12 Countries & 45 Departments</p>
        </div>

        <div class="bg-white/90 backdrop-blur-md rounded-3xl p-7 shadow-xl border border-slate-200/80 text-center shiny-card {{ $kpis['overdue_tasks'] > 0 ? 'pulse-glow-red' : '' }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-widest block">Active Overdue Tasks</span>
            <div class="text-5xl font-extrabold text-rose-600 mt-4 tracking-tight stat-countup">{{ $kpis['overdue_tasks'] }}</div>
            <p class="text-xs font-normal text-slate-400 mt-2">Escalations auto-dispatched to managers</p>
        </div>
    </div>

    <!-- Interactive Visual Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Department Request Volume (Bar Chart) -->
        <div class="bg-white/90 backdrop-blur-md rounded-3xl p-8 shadow-xl border border-slate-200/80 shiny-card">
            <h2 class="text-xl font-bold gradient-text mb-6 flex items-center gap-3">
                <svg class="w-6 h-6 text-purpleSecondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Department Performance Comparison
            </h2>
            <div class="h-64 relative">
                <canvas id="deptChart"></canvas>
            </div>
        </div>

        <!-- Workflow Status Breakdown (Doughnut Chart) -->
        <div class="bg-white/90 backdrop-blur-md rounded-3xl p-8 shadow-xl border border-slate-200/80 shiny-card">
            <h2 class="text-xl font-bold gradient-text mb-6 flex items-center gap-3">
                <svg class="w-6 h-6 text-skyPrimary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                SLA Compliance & Execution Status
            </h2>
            <div class="h-64 relative flex items-center justify-center">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Bottlenecks & Department Efficiency Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Bottleneck Heatmap Table -->
        <div class="bg-white/90 backdrop-blur-md rounded-3xl p-8 shadow-xl border border-slate-200/80">
            <h2 class="text-xl font-bold gradient-text mb-6 flex items-center gap-3">
                <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Approval Bottleneck Heatmap
            </h2>

            @if(empty($bottlenecks))
                <div class="p-10 text-center bg-white rounded-3xl border border-slate-200 shadow-sm">
                    <h3 class="text-base font-extrabold text-purpleSecondary">No Bottlenecks Detected</h3>
                    <p class="text-sm font-medium text-slate-600 mt-1">No bottleneck steps detected yet.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($bottlenecks as $b)
                        <div class="p-5 rounded-2xl border border-rose-200 bg-rose-50/60 flex items-center justify-between shiny-card">
                            <div>
                                <h3 class="font-bold text-slate-900 text-base">{{ $b['step_name'] }}</h3>
                                <p class="text-xs font-normal text-slate-500 mt-1">Workflow: {{ $b['workflow_name'] }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-extrabold text-rose-600 tracking-tight">{{ $b['avg_duration_hours'] }} hrs</span>
                                <p class="text-xs font-normal text-slate-400 mt-0.5">Avg completion time ({{ $b['total_tasks'] }} tasks)</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Department Performance Table -->
        <div class="bg-white/90 backdrop-blur-md rounded-3xl p-8 shadow-xl border border-slate-200/80">
            <h2 class="text-xl font-bold gradient-text mb-6 flex items-center gap-3">
                <svg class="w-6 h-6 text-skyPrimary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                Department Performance Summary
            </h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-creamBase/80 text-slate-800 font-bold uppercase tracking-wider border-b border-slate-200">
                            <th class="py-3.5 px-4">Department</th>
                            <th class="py-3.5 px-4 text-center">Total Requests</th>
                            <th class="py-3.5 px-4 text-center">Approved</th>
                            <th class="py-3.5 px-4 text-center">Running</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($departments as $d)
                            <tr class="hover:bg-creamBase/80 transition font-medium">
                                <td class="py-3.5 px-4 font-bold text-slate-900">{{ $d['name'] }} ({{ $d['code'] }})</td>
                                <td class="py-3.5 px-4 text-center font-bold text-purpleSecondary">{{ $d['total_requests'] }}</td>
                                <td class="py-3.5 px-4 text-center font-bold text-emerald-600">{{ $d['approved'] }}</td>
                                <td class="py-3.5 px-4 text-center font-bold text-sky-600">{{ $d['in_progress'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Engine -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Department Performance Bar Chart
    const deptCtx = document.getElementById('deptChart');
    if (deptCtx) {
        const deptNames = {!! json_encode(array_column($departments, 'code')) !!};
        const totalData = {!! json_encode(array_column($departments, 'total_requests')) !!};
        const approvedData = {!! json_encode(array_column($departments, 'approved')) !!};

        new Chart(deptCtx, {
            type: 'bar',
            data: {
                labels: deptNames,
                datasets: [
                    {
                        label: 'Total Requests',
                        data: totalData,
                        backgroundColor: '#4B2E83',
                        borderRadius: 8
                    },
                    {
                        label: 'Approved Requests',
                        data: approvedData,
                        backgroundColor: '#87CEEB',
                        borderRadius: 8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { font: { weight: 'bold', size: 11 } } }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // 2. Status Breakdown Doughnut Chart
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        const complianceRate = {{ $kpis['sla_compliance_rate'] }};
        const overdueTasks = {{ $kpis['overdue_tasks'] }};

        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['SLA Compliant', 'Breached / Overdue'],
                datasets: [{
                    data: [complianceRate, Math.max(100 - complianceRate, overdueTasks)],
                    backgroundColor: ['#10B981', '#E11D48'],
                    borderWidth: 3,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { weight: 'bold', size: 11 } } }
                },
                cutout: '70%'
            }
        });
    }
});
</script>
@endsection
