<?php

namespace App\Services;

use App\Models\Task;
use App\Models\WorkflowDefinition;
use App\Models\WorkflowInstance;
use Illuminate\Support\Facades\DB;

class AIWorkflowOptimizerService
{
    /**
     * AI Intelligence Engine analyzing historical execution metrics and yielding optimization recommendations.
     */
    public function generateOptimizationSuggestions(WorkflowDefinition $definition): array
    {
        $suggestions = [];
        $instances = WorkflowInstance::where('workflow_definition_id', $definition->id)->get();
        $totalInstances = $instances->count();

        // 1. Analyze Average Process Duration vs Defined SLA
        if ($totalInstances > 0) {
            $completed = $instances->where('status', 'approved');
            if ($completed->count() > 0) {
                $avgDurationHours = $completed->avg(function ($inst) {
                    return $inst->started_at && $inst->completed_at ? $inst->started_at->diffInHours($inst->completed_at) : 0;
                });

                if ($avgDurationHours > ($definition->sla_hours * 0.8)) {
                    $suggestions[] = [
                        'type' => 'sla_risk',
                        'severity' => 'high',
                        'title' => 'High SLA Breach Risk Detected',
                        'message' => sprintf(
                            "Average process turnaround time (%.1f hours) is approaching the SLA limit (%d hours). Consider increasing step SLAs or delegating high-volume approval steps.",
                            $avgDurationHours,
                            $definition->sla_hours
                        ),
                    ];
                }
            }
        }

        // 2. Analyze Step Bottlenecks
        foreach ($definition->steps as $step) {
            $tasks = Task::where('step_id', $step->id)->where('status', 'completed')->get();
            if ($tasks->count() >= 1) {
                $avgStepHours = $tasks->avg(function ($t) {
                    return $t->created_at->diffInHours($t->completed_at);
                });

                if ($avgStepHours > 24) {
                    $suggestions[] = [
                        'type' => 'bottleneck',
                        'severity' => 'warning',
                        'title' => "Approval Bottleneck at Step: '{$step->name}'",
                        'message' => sprintf(
                            "Step '%s' takes an average of %.1f hours per task. Recommendation: Convert from single approval to Parallel Approval or add an automated escalation rule.",
                            $step->name,
                            $avgStepHours
                        ),
                    ];
                }
            }
        }

        // 3. Rule-based Best Practice Recommendations
        if ($definition->steps->count() > 4) {
            $suggestions[] = [
                'type' => 'process_complexity',
                'severity' => 'info',
                'title' => 'Complex Sequential Chain (5+ Steps)',
                'message' => 'This workflow has 5 or more sequential steps. Consolidate low-value steps into parallel approval groups to reduce total turnaround time.',
            ];
        }

        if ($definition->steps->count() === 1) {
            $suggestions[] = [
                'type' => 'best_practice',
                'severity' => 'info',
                'title' => 'Single Step Workflow',
                'message' => 'Single-step process detected. For high-value requests, consider adding a secondary finance or legal sign-off step.',
            ];
        }

        // Default suggestion if no bottlenecks exist yet
        if (empty($suggestions)) {
            $suggestions[] = [
                'type' => 'optimal',
                'severity' => 'success',
                'title' => 'Process Running Optimally',
                'message' => 'AI Engine detected no approval bottlenecks or SLA risks. The process is performing within expected parameters.',
            ];
        }

        return $suggestions;
    }
}
