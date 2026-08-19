<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Task;
use App\Models\WorkflowInstance;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(protected AnalyticsService $analyticsService) {}

    public function index()
    {
        $user = Auth::user();

        // Overview KPI metrics
        $kpis = $this->analyticsService->getOverviewKPIs();

        // User's active pending tasks
        $myPendingTasks = Task::with(['workflowInstance.definition', 'workflowInstance.requester', 'step'])
            ->where('status', 'pending')
            ->where(function ($q) use ($user) {
                $q->where('assignee_id', $user->id)
                  ->orWhere('delegated_to_id', $user->id);
            })
            ->limit(5)
            ->get();

        // Recent Workflow Requests submitted by user
        $myRequests = WorkflowInstance::with(['definition', 'currentStep'])
            ->where('requester_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Global running workflows
        $runningWorkflows = WorkflowInstance::with(['definition', 'requester', 'currentStep'])
            ->where('status', 'in_progress')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent Audit logs
        $recentAudits = AuditLog::with('user')->orderBy('created_at', 'desc')->limit(15)->get();

        return view('dashboard', compact('kpis', 'myPendingTasks', 'myRequests', 'runningWorkflows', 'recentAudits'));
    }
}
