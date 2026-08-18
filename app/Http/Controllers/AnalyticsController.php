<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use App\Services\SLAEngineService;
use Illuminate\Support\Facades\Cache;

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
}
