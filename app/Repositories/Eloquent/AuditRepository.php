<?php

namespace App\Repositories\Eloquent;

use App\Models\AuditLog;
use App\Repositories\Contracts\AuditRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class AuditRepository implements AuditRepositoryInterface
{
    public function logAction(array $data): AuditLog
    {
        return AuditLog::create($data);
    }

    public function getPaginatedLogs(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = AuditLog::with('user');

        if (!empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['entity_type'])) {
            $query->where('entity_type', $filters['entity_type']);
        }

        if (!empty($filters['entity_id'])) {
            $query->where('entity_id', $filters['entity_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}
