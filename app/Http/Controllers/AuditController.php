<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\AuditRepositoryInterface;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function __construct(protected AuditRepositoryInterface $auditRepo) {}

    public function index(Request $request)
    {
        $filters = $request->only(['action', 'entity_type', 'user_id']);
        $logs = $this->auditRepo->getPaginatedLogs($filters, 20);

        return view('audit.index', compact('logs', 'filters'));
    }
}
