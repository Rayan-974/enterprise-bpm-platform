<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Models\WorkflowInstance;
use App\Models\WorkflowStep;
use App\Repositories\Contracts\TaskRepositoryInterface;

class TaskManagementService
{
    public function __construct(
        protected TaskRepositoryInterface $taskRepo,
        protected NotificationService $notificationService,
        protected AuditLoggerService $auditLogger
    ) {}

    public function assignTask(WorkflowInstance $instance, WorkflowStep $step): Task
    {
        $assigneeId = null;
        $assigneeRole = null;
        $requester = $instance->requester;

        switch ($step->assignee_type) {
            case 'role':
                $assigneeRole = $step->assignee_value;
                break;
            case 'department_head':
                // 1. First check workflow target department head (e.g., Finance Head for Finance workflows)
                // 2. Then check instance department head / requester department head
                // 3. Fallback to requester manager
                $assigneeId = $instance->definition->department?->head_user_id 
                    ?? $instance->department?->head_user_id 
                    ?? $requester->department?->head_user_id
                    ?? $requester->manager_id;
                break;
            case 'requester_manager':
            case 'manager':
                $assigneeId = $requester->manager_id ?? $instance->definition->department?->head_user_id;
                break;
            case 'user':
                $assigneeId = (int) $step->assignee_value;
                break;
        }

        $dueAt = now()->addHours($step->sla_hours);

        $task = $this->taskRepo->createTask([
            'workflow_instance_id' => $instance->id,
            'step_id' => $step->id,
            'assignee_id' => $assigneeId,
            'assignee_role' => $assigneeRole,
            'status' => 'pending',
            'due_at' => $dueAt,
        ]);

        // Send notification to assignee if assigned to specific user
        if ($assigneeId) {
            $user = User::find($assigneeId);
            if ($user) {
                $this->notificationService->send(
                    $user,
                    "New Task Assigned: {$instance->definition->name}",
                    "Task step '{$step->name}' requires your approval.",
                    'task_assigned',
                    ['task_id' => $task->id, 'uuid' => $instance->uuid]
                );
            }
        }

        $this->auditLogger->log('task.assigned', Task::class, $task->id, null, $task->toArray());

        return $task;
    }

    public function delegateTask(Task $task, User $delegatedToUser, ?string $comments = null): Task
    {
        $oldTask = $task->toArray();
        $updatedTask = $this->taskRepo->delegateTask($task, $delegatedToUser->id, $comments);

        $this->notificationService->send(
            $delegatedToUser,
            "Task Delegated to You: {$task->workflowInstance->definition->name}",
            "Task step '{$task->step->name}' was delegated to you.",
            'task_delegated',
            ['task_id' => $task->id, 'uuid' => $task->workflowInstance->uuid]
        );

        $this->auditLogger->log('task.delegated', Task::class, $task->id, $oldTask, $updatedTask->toArray());

        return $updatedTask;
    }
}
