<?php

namespace App\Services;

use App\Events\SLABreached;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;

class SLAEngineService
{
    public function __construct(
        protected TaskRepositoryInterface $taskRepo,
        protected NotificationService $notificationService,
        protected AuditLoggerService $auditLogger
    ) {}

    /**
     * Check pending tasks for SLA breaches and process escalations.
     */
    public function checkAndEscalateOverdueTasks(): array
    {
        $overdueTasks = $this->taskRepo->getOverdueTasks();
        $escalatedCount = 0;

        foreach ($overdueTasks as $task) {
            // Escalate task
            $step = $task->step;
            $escalationUserId = $step->escalation_user_id;

            if (!$escalationUserId) {
                // Default to department head or admin
                $escalationUserId = $task->workflowInstance->department?->head_user_id;
            }

            if ($escalationUserId && $escalationUserId !== $task->assignee_id) {
                $task->update([
                    'status' => 'escalated',
                    'delegated_to_id' => $escalationUserId,
                    'comments' => 'Auto-escalated due to SLA timeout violation.',
                ]);

                $escalatedUser = User::find($escalationUserId);
                if ($escalatedUser) {
                    $this->notificationService->send(
                        $escalatedUser,
                        "SLA BREACH ESCALATION: {$task->workflowInstance->definition->name}",
                        "Task for step '{$step->name}' exceeded SLA deadline and has been escalated to you.",
                        'sla_breach',
                        ['task_id' => $task->id, 'uuid' => $task->workflowInstance->uuid]
                    );
                }

                event(new SLABreached($task, 'overdue'));
                $this->auditLogger->log('sla.breach_escalated', Task::class, $task->id);
                $escalatedCount++;
            }
        }

        return [
            'checked' => count($overdueTasks),
            'escalated' => $escalatedCount,
        ];
    }
}
