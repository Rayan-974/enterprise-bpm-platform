<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use App\Services\SLAEngineService;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsController extends Controller
{
    public function __construct(
        protected AnalyticsService $analyticsService,
        protected SLAEngineService $slaEngine
    ) {}

    public function index()
    {
        $kpis = $this->analyticsService->getOverviewKPIs();
        $bottlenecks = $this->analyticsService->getBottleneckSteps();
        $departments = $this->analyticsService->getDepartmentPerformance();

        return view('analytics.index', compact('kpis', 'bottlenecks', 'departments'));
    }

    /**
     * Trigger instant manual SLA scan & refresh analytics cache.
     */
    public function scan()
    {
        // 1. Clear analytics cache
        Cache::forget('analytics_overview_kpis');
        Cache::forget('analytics_bottleneck_steps_5');
        Cache::forget('analytics_department_performance');

        // 2. Execute instant SLA check
        $result = $this->slaEngine->checkAndEscalateOverdueTasks();

        return redirect()->route('analytics.index')->with(
            'success',
            "Manual SLA Auto-Scan completed! Checked: {$result['checked']} task(s), Escalated: {$result['escalated']} overdue task(s)."
        );
    }

    /**
     * Export Department Performance & SLA Metrics to CSV.
     */
    public function exportCsv(): StreamedResponse
    {
        $fileName = 'analytics_performance_report_' . date('Y-m-d_H-i') . '.csv';
        $departments = $this->analyticsService->getDepartmentPerformance();
        $kpis = $this->analyticsService->getOverviewKPIs();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($departments, $kpis) {
            $file = fopen('php://output', 'w');

            // Summary Section
            fputcsv($file, ['=== OVERVIEW PERFORMANCE METRICS ===']);
            fputcsv($file, ['Total Workflows Executed', $kpis['total_workflows']]);
            fputcsv($file, ['SLA Compliance Rate (%)', $kpis['sla_compliance_rate'] . '%']);
            fputcsv($file, ['Active Overdue Tasks', $kpis['overdue_tasks']]);
            fputcsv($file, []);

            // Department Section
            fputcsv($file, ['=== DEPARTMENT PERFORMANCE BREAKDOWN ===']);
            fputcsv($file, ['Department ID', 'Department Name', 'Code', 'Total Requests', 'Approved Requests', 'In-Progress Requests']);

            foreach ($departments as $d) {
                fputcsv($file, [
                    $d['id'],
                    $d['name'],
                    $d['code'],
                    $d['total_requests'],
                    $d['approved'],
                    $d['in_progress'],
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
