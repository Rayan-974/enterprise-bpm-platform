<?php

namespace App\Services;

use App\Models\DigitalSignature;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class DigitalSignatureService
{
    /**
     * Create cryptographic SHA-256 digital signature record for an approval action.
     */
    public function signApproval(Task $task, User $signer, ?string $signatureData = null): DigitalSignature
    {
        $instance = $task->workflowInstance;

        // Generate SHA-256 cryptographic digest binding signer, payload, timestamp & secret key
        $rawPayload = json_encode($instance->payload ?? []);
        $timestamp = now()->toIso8601String();
        $secretKey = config('app.key');

        $signatureHash = hash('sha256', "{$instance->uuid}|{$signer->id}|{$task->id}|{$rawPayload}|{$timestamp}|{$secretKey}");

        return DigitalSignature::create([
            'workflow_instance_id' => $instance->id,
            'task_id' => $task->id,
            'user_id' => $signer->id,
            'signature_hash' => $signatureHash,
            'signature_data' => $signatureData ?? 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iNDAiPjx0ZXh0IHg9IjEwIiB5PSIyNSIgZm9udC1mYW1pbHk9ImN1cnNpdmUiIGZvbnQtc2l6ZT0iMTYiIGZpbGw9IiM0QjJFODMiPkRpZ2l0YWxseSBTaWduZWQ8L3RleHQ+PC9zdmc+',
            'ip_address' => Request::ip(),
            'signed_at' => now(),
        ]);
    }
}
