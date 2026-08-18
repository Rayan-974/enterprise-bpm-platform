<?php

namespace App\Repositories\Eloquent;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskRepository implements TaskRepositoryInterface
{
    public function createTask(array $data): Task
    {
        return Task::create($data);
    }

    public function findById(int $id): ?Task
    {
        return Task::with(['workflowInstance.definition', 'workflowInstance.requester', 'step', 'assignee', 'delegatedTo'])->find($id);
    }

    public function getUserPendingTasks(int $userId, ?string $userRole = null): Collection
    {
        $query = Task::with(['workflowInstance.definition', 'workflowInstance.requester', 'step'])
            ->where('status', 'pending')
            ->where(function ($q) use ($userId, $userRole) {
                $q->where('assignee_id', $userId)
                  ->orWhere('delegated_to_id', $userId);
                
                if ($userRole) {
                    $q->orWhere('assignee_role', $userRole);
                }
            });

        return $query->orderBy('due_at', 'asc')->get();
    }

    public function getUserPaginatedTasks(int $userId, string $status = 'pending', int $perPage = 15): LengthAwarePaginator
    {
        $query = Task::with(['workflowInstance.definition', 'workflowInstance.requester', 'step'])
            ->where('status', $status)
            ->where(function ($q) use ($userId) {
                $q->where('assignee_id', $userId)
                  ->orWhere('delegated_to_id', $userId);
            });

        return $query->orderBy('updated_at', 'desc')->paginate($perPage);
    }

    public function getOverdueTasks(): Collection
    {
        return Task::with(['workflowInstance', 'step', 'assignee'])
            ->where('status', 'pending')
            ->where('due_at', '<', now())
            ->get();
    }

    public function updateTaskStatus(Task $task, string $status, ?string $comments = null): Task
    {
        $data = ['status' => $status];
        if ($comments) {
            $data['comments'] = $comments;
        }
        if (in_array($status, ['completed', 'escalated', 'expired'])) {
            $data['completed_at'] = now();
        }

        $task->update($data);
        return $task;
    }

    public function delegateTask(Task $task, int $delegateUserId, ?string $comments = null): Task
    {
        $task->update([
            'status' => 'delegated',
            'delegated_to_id' => $delegateUserId,
            'comments' => $comments,
        ]);
        return $task;
    }
}
