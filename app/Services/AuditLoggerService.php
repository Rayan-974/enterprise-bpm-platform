<?php

namespace App\Services;

use App\Repositories\Contracts\AuditRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLoggerService
{
    public function __construct(
        protected AuditRepositoryInterface $auditRepo
    ) {}

    public function log(string $action, string $entityType, int $entityId, ?array $oldValues = null, ?array $newValues = null): void
    {
        $this->auditRepo->logAction([
            'user_id' => Auth::id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
