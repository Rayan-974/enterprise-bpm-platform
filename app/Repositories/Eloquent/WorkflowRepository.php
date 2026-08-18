<?php

namespace App\Repositories\Eloquent;

use App\Models\WorkflowDefinition;
use App\Models\WorkflowInstance;
use App\Repositories\Contracts\WorkflowRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class WorkflowRepository implements WorkflowRepositoryInterface
{
    public function getAllDefinitions(array $filters = []): Collection
    {
        $query = WorkflowDefinition::with(['department', 'steps']);

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->get();
    }

    public function findDefinitionById(int $id): ?WorkflowDefinition
    {
        return WorkflowDefinition::with(['steps', 'activeFormTemplate.fields', 'department'])->find($id);
    }

    public function findDefinitionByCode(string $code): ?WorkflowDefinition
    {
        return WorkflowDefinition::with(['steps', 'activeFormTemplate.fields', 'department'])
            ->where('code', $code)
            ->first();
    }

    public function createDefinition(array $data): WorkflowDefinition
    {
        return WorkflowDefinition::create($data);
    }

    public function updateDefinition(int $id, array $data): WorkflowDefinition
    {
        $definition = WorkflowDefinition::findOrFail($id);
        $definition->update($data);
        return $definition;
    }

    public function createInstance(array $data): WorkflowInstance
    {
        return WorkflowInstance::create($data);
    }

    public function findInstanceByUuid(string $uuid): ?WorkflowInstance
    {
        return WorkflowInstance::where('uuid', $uuid)->first();
    }

    public function getInstanceWithRelations(string $uuid): ?WorkflowInstance
    {
        return WorkflowInstance::with([
            'definition',
            'requester',
            'department',
            'currentStep',
            'tasks.assignee',
            'tasks.step',
            'approvals.approver',
            'approvals.step',
        ])->where('uuid', $uuid)->first();
    }

    public function updateInstanceStatus(WorkflowInstance $instance, string $status, ?int $nextStepId = null): WorkflowInstance
    {
        $data = ['status' => $status];
        if ($nextStepId !== null) {
            $data['current_step_id'] = $nextStepId;
        }
        if (in_array($status, ['approved', 'rejected', 'completed', 'cancelled'])) {
            $data['completed_at'] = now();
        }

        $instance->update($data);
        return $instance;
    }

    public function getRunningInstances(int $limit = 50): Collection
    {
        return WorkflowInstance::with(['definition', 'requester', 'currentStep'])
            ->where('status', 'in_progress')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getPaginatedInstances(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = WorkflowInstance::with(['definition', 'requester', 'department', 'currentStep']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['requester_id'])) {
            $query->where('requester_id', $filters['requester_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('definition', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%");
            })->orWhere('uuid', 'like', "%{$search}%");
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}
