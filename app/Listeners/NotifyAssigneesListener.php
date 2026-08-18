<?php

namespace App\Listeners;

use App\Events\WorkflowApproved;
use App\Events\WorkflowRejected;
use App\Events\WorkflowStarted;
use App\Services\NotificationService;

class NotifyAssigneesListener
{
    public function __construct(protected NotificationService $notificationService) {}

    public function handleWorkflowStarted(WorkflowStarted $event): void
    {
        $instance = $event->instance;
        $this->notificationService->send(
            $instance->requester,
            "Workflow Submitted: {$instance->definition->name}",
            "Your request #{$instance->uuid} has been submitted and is currently in progress.",
            'workflow_status',
            ['uuid' => $instance->uuid]
        );
    }

    public function handleWorkflowApproved(WorkflowApproved $event): void
    {
        $instance = $event->instance;
        $this->notificationService->send(
            $instance->requester,
            "Workflow Approved: {$instance->definition->name}",
            "Your request #{$instance->uuid} has been fully approved!",
            'workflow_status',
            ['uuid' => $instance->uuid]
        );
    }

    public function handleWorkflowRejected(WorkflowRejected $event): void
    {
        $instance = $event->instance;
        $this->notificationService->send(
            $instance->requester,
            "Workflow Rejected: {$instance->definition->name}",
            "Your request #{$instance->uuid} was rejected. Reason: " . ($event->reason ?? 'None provided.'),
            'workflow_status',
            ['uuid' => $instance->uuid]
        );
    }
}
