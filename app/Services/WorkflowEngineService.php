<?php

namespace App\Services;

use App\Events\StepCompleted;
use App\Events\WorkflowApproved;
use App\Events\WorkflowRejected;
use App\Events\WorkflowStarted;
use App\Models\Approval;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkflowDefinition;
use App\Models\WorkflowInstance;
use App\Models\WorkflowStep;
use App\Repositories\Contracts\WorkflowRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class WorkflowEngineService
{
    public function __construct(
        protected WorkflowRepositoryInterface $workflowRepo,
        protected FormEngineService $formEngine,
        protected TaskManagementService $taskService,
        protected AuditLoggerService $auditLogger
    ) {}

    /**
     * Start a new Workflow Instance.
     */
    public function startWorkflow(WorkflowDefinition $definition, User $requester, array $payload): WorkflowInstance
    {
        // 1. Validate Payload if active form template exists
        $formTemplate = $definition->activeFormTemplate;
        if ($formTemplate) {
            $this->formEngine->validatePayload($formTemplate, $payload);
        }

        return DB::transaction(function () use ($definition, $requester, $payload) {
            $firstStep = $definition->steps()->orderBy('step_order')->first();

            $dueAt = now()->addHours($definition->sla_hours);

            $instance = $this->workflowRepo->createInstance([
                'workflow_definition_id' => $definition->id,
                'requester_id' => $requester->id,
                'department_id' => $requester->department_id,
                'current_step_id' => $firstStep?->id,
                'status' => 'in_progress',
                'payload' => $payload,
                'started_at' => now(),
                'due_at' => $dueAt,
            ]);

            $this->auditLogger->log('workflow.started', WorkflowInstance::class, $instance->id, null, [
                'definition_id' => $definition->id,
                'requester_id' => $requester->id,
                'uuid' => $instance->uuid,
            ]);

            event(new WorkflowStarted($instance));

            // Assign Task for First Step if exists
            if ($firstStep) {
                $this->taskService->assignTask($instance, $firstStep);
            }

            return $instance;
        });
    }

    /**
     * Process Task Decision (Approve / Reject).
     */
    public function processDecision(Task $task, User $approver, string $decision, ?string $comments = null): WorkflowInstance
    {
        return DB::transaction(function () use ($task, $approver, $decision, $comments) {
            $instance = $task->workflowInstance;
            $currentStep = $task->step;

            // 1. Log Approval Record
            Approval::create([
                'workflow_instance_id' => $instance->id,
                'task_id' => $task->id,
                'step_id' => $currentStep->id,
                'approver_id' => $approver->id,
                'decision' => $decision,
                'comments' => $comments,
                'ip_address' => Request::ip(),
            ]);

            // 2. Mark Task Completed
            $task->update([
                'status' => 'completed',
                'completed_at' => now(),
                'comments' => $comments,
            ]);

            event(new StepCompleted($instance, $task, $decision));

            // 3. Handle Rejection State Transition
            if ($decision === 'rejected') {
                $this->workflowRepo->updateInstanceStatus($instance, 'rejected');
                event(new WorkflowRejected($instance, $comments));
                $this->auditLogger->log('workflow.rejected', WorkflowInstance::class, $instance->id, null, ['comments' => $comments]);
                return $instance;
            }

            // 4. Handle Approval State Transition -> Determine Next Step
            $nextStep = $this->determineNextStep($instance, $currentStep);

            if ($nextStep) {
                $this->workflowRepo->updateInstanceStatus($instance, 'in_progress', $nextStep->id);
                $this->taskService->assignTask($instance, $nextStep);
                $this->auditLogger->log('workflow.step_advanced', WorkflowInstance::class, $instance->id, null, [
                    'from_step' => $currentStep->name,
                    'to_step' => $nextStep->name,
                ]);
            } else {
                // Workflow Completed & Fully Approved
                $this->workflowRepo->updateInstanceStatus($instance, 'approved');
                event(new WorkflowApproved($instance));
                $this->auditLogger->log('workflow.approved', WorkflowInstance::class, $instance->id);
            }

            return $instance;
        });
    }

    /**
     * Determine Next Step considering sequential, conditional decision nodes, and auto actions.
     */
    protected function determineNextStep(WorkflowInstance $instance, WorkflowStep $currentStep): ?WorkflowStep
    {
        $definition = $instance->definition;
        $allSteps = $definition->steps()->orderBy('step_order')->get();

        $currentIndex = $allSteps->search(fn($step) => $step->id === $currentStep->id);
        if ($currentIndex === false || $currentIndex >= $allSteps->count() - 1) {
            return null;
        }

        $nextStepCandidate = $allSteps[$currentIndex + 1];

        // Evaluate Decision Node Rules if present
        if ($nextStepCandidate->type === 'decision' && !empty($nextStepCandidate->condition_rules)) {
            $matched = $this->evaluateConditions($instance->payload ?? [], $nextStepCandidate->condition_rules);
            if (!$matched) {
                // Skip decision step to step after it
                return $this->determineNextStep($instance, $nextStepCandidate);
            }
        }

        return $nextStepCandidate;
    }

    /**
     * Evaluate JSON condition rules against form payload.
     * Example rules: {"field": "amount", "operator": ">=", "value": 10000}
     */
    public function evaluateConditions(array $payload, array $rules): bool
    {
        if (empty($rules['field']) || empty($rules['operator'])) {
            return true;
        }

        $field = $rules['field'];
        $operator = $rules['operator'];
        $expected = $rules['value'] ?? null;
        $actual = $payload[$field] ?? null;

        switch ($operator) {
            case '>':
                return (float)$actual > (float)$expected;
            case '>=':
                return (float)$actual >= (float)$expected;
            case '<':
                return (float)$actual < (float)$expected;
            case '<=':
                return (float)$actual <= (float)$expected;
            case '==':
            case '=':
                return $actual == $expected;
            case '!=':
                return $actual != $expected;
            case 'in':
                return is_array($expected) && in_array($actual, $expected);
            default:
                return true;
        }
    }
}
