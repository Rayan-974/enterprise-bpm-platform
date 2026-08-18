<?php

namespace App\Services;

use App\Models\Approval;
use App\Models\Department;
use App\Models\Task;
use App\Models\WorkflowInstance;
use App\Models\WorkflowStep;
use Illuminate\Support\Facades\Cache;

class AnalyticsService
{
    /**
     * Get KPI Overview Metrics with Redis caching for 1M+ record scale.
     */
    public function getOverviewKPIs(): array
    {
        return Cache::remember('analytics_overview_kpis', 60, function () {
            $totalWorkflows = WorkflowInstance::count();
            $inProgress = WorkflowInstance::where('status', 'in_progress')->count();
            $approved = WorkflowInstance::where('status', 'approved')->count();
            $rejected = WorkflowInstance::where('status', 'rejected')->count();

            $overdueTasks = Task::where('status', 'pending')
                ->where('due_at', '<', now())
                ->count();

            $totalCompletedTasks = Task::where('status', 'completed')->count();
            $onTimeTasks = Task::where('status', 'completed')
                ->whereColumn('completed_at', '<=', 'due_at')
                ->count();

            $slaComplianceRate = $totalCompletedTasks > 0
                ? round(($onTimeTasks / $totalCompletedTasks) * 100, 1)
                : 100.0;

            return [
                'total_workflows' => $totalWorkflows,
                'in_progress' => $inProgress,
                'approved' => $approved,
                'rejected' => $rejected,
                'overdue_tasks' => $overdueTasks,
                'sla_compliance_rate' => $slaComplianceRate,
            ];
        });
    }

    /**
     * Get Bottleneck Steps (Cached for performance at scale).
     */
    public function getBottleneckSteps(int $limit = 5): array
    {
        return Cache::remember("analytics_bottleneck_steps_{$limit}", 60, function () use ($limit) {
            $completedTasks = Task::with(['step.workflowDefinition'])
                ->where('status', 'completed')
                ->whereNotNull('completed_at')
                ->get();

            if ($completedTasks->isEmpty()) {
                return [];
            }

            $grouped = $completedTasks->groupBy('step_id');
            $bottlenecks = [];

            foreach ($grouped as $stepId => $tasks) {
                $step = $tasks->first()?->step;
                $totalTasks = $tasks->count();
                
                $totalHours = $tasks->sum(function ($task) {
                    return max(0, $task->created_at->diffInHours($task->completed_at));
                });

                $avgHours = $totalTasks > 0 ? round($totalHours / $totalTasks, 1) : 0;

                $bottlenecks[] = [
                    'step_name' => $step?->name ?? 'Unknown Step',
                    'workflow_name' => $step?->workflowDefinition?->name ?? 'N/A',
                    'avg_duration_hours' => $avgHours,
                    'total_tasks' => $totalTasks,
                ];
            }

            usort($bottlenecks, fn($a, $b) => $b['avg_duration_hours'] <=> $a['avg_duration_hours']);

            return array_slice($bottlenecks, 0, $limit);
        });
    }

    /**
     * Get Department Performance Metrics (Cached for high throughput).
     */
    public function getDepartmentPerformance(): array
    {
        return Cache::remember('analytics_department_performance', 60, function () {
            $departments = Department::where('is_active', true)->get();
            $result = [];

            foreach ($departments as $dept) {
                $total = WorkflowInstance::where('department_id', $dept->id)->count();
                $approved = WorkflowInstance::where('department_id', $dept->id)->where('status', 'approved')->count();
                $inProgress = WorkflowInstance::where('department_id', $dept->id)->where('status', 'in_progress')->count();

                $result[] = [
                    'department_id' => $dept->id,
                    'name' => $dept->name,
                    'code' => $dept->code,
                    'total_requests' => $total,
                    'approved' => $approved,
                    'in_progress' => $inProgress,
                ];
            }

            return $result;
        });
    }
}
