<?php

namespace App\Repositories\Contracts;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface TaskRepositoryInterface
{
    public function createTask(array $data): Task;
    public function findById(int $id): ?Task;
    public function getUserPendingTasks(int $userId, ?string $userRole = null): Collection;
    public function getUserPaginatedTasks(int $userId, string $status = 'pending', int $perPage = 15): LengthAwarePaginator;
    public function getOverdueTasks(): Collection;
    public function updateTaskStatus(Task $task, string $status, ?string $comments = null): Task;
    public function delegateTask(Task $task, int $delegateUserId, ?string $comments = null): Task;
}
