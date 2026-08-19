<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Repositories\Contracts\AuditRepositoryInterface;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditController extends Controller
{
    public function __construct(protected AuditRepositoryInterface $auditRepo) {}

    public function index(Request $request)
    {
        $filters = $request->only(['action', 'entity_type', 'user_id']);
        $perPage = $request->input('per_page', 'all');
        $logs = $this->auditRepo->getPaginatedLogs($filters, $perPage);

        return view('audit.index', compact('logs', 'filters', 'perPage'));
    }

    /**
     * Download full audit trail log as CSV file.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $fileName = 'audit_trail_report_' . date('Y-m-d_H-i') . '.csv';
        $logs = AuditLog::with('user')->orderBy('created_at', 'desc')->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Log ID', 'Timestamp', 'Action Event', 'User / Actor', 'User Email', 'Target Entity', 'Entity ID', 'IP Address', 'User Agent']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at ? $log->created_at->toIso8601String() : '',
                    $log->action,
                    $log->user ? $log->user->name : 'System / Automated',
                    $log->user ? $log->user->email : 'N/A',
                    $log->auditable_type ?? 'N/A',
                    $log->auditable_id ?? 'N/A',
                    $log->ip_address ?? '127.0.0.1',
                    $log->user_agent ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
