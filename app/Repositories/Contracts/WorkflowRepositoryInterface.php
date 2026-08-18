<?php

namespace App\Repositories\Contracts;

use App\Models\WorkflowDefinition;
use App\Models\WorkflowInstance;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface WorkflowRepositoryInterface
{
    public function getAllDefinitions(array $filters = []): Collection;
    public function findDefinitionById(int $id): ?WorkflowDefinition;
    public function findDefinitionByCode(string $code): ?WorkflowDefinition;
    public function createDefinition(array $data): WorkflowDefinition;
    public function updateDefinition(int $id, array $data): WorkflowDefinition;
    
    public function createInstance(array $data): WorkflowInstance;
    public function findInstanceByUuid(string $uuid): ?WorkflowInstance;
    public function getInstanceWithRelations(string $uuid): ?WorkflowInstance;
    public function updateInstanceStatus(WorkflowInstance $instance, string $status, ?int $nextStepId = null): WorkflowInstance;
    public function getRunningInstances(int $limit = 50): Collection;
    public function getPaginatedInstances(array $filters = [], int $perPage = 15): LengthAwarePaginator;
}
