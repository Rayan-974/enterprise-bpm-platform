<?php

namespace App\Repositories\Contracts;

use App\Models\AuditLog;
use Illuminate\Pagination\LengthAwarePaginator;

interface AuditRepositoryInterface
{
    public function logAction(array $data): AuditLog;
    public function getPaginatedLogs(array $filters = [], int|string $perPage = 50);
}
