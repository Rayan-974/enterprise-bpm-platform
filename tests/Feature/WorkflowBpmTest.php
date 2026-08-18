<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkflowDefinition;
use App\Models\WorkflowInstance;
use App\Services\SLAEngineService;
use App\Services\WorkflowEngineService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowBpmTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_can_start_workflow_instance()
    {
        $user = User::where('email', 'john.doe@enterprise.com')->first();
        $definition = WorkflowDefinition::where('code', 'CAPEX-PROC')->first();

        $this->actingAs($user);

        $payload = [
            'title' => 'New Server Hardware Purchase',
            'amount' => 15000,
            'vendor_name' => 'Dell Enterprise',
            'category' => 'Hardware & Infrastructure',
            'justification' => 'Upgrading datacenter server capacity.',
        ];

        /** @var WorkflowEngineService $engine */
        $engine = app(WorkflowEngineService::class);
        $instance = $engine->startWorkflow($definition, $user, $payload);

        $this->assertDatabaseHas('workflow_instances', [
            'id' => $instance->id,
            'status' => 'in_progress',
            'requester_id' => $user->id,
        ]);

        $this->assertDatabaseHas('tasks', [
            'workflow_instance_id' => $instance->id,
            'status' => 'pending',
        ]);
    }

    public function test_approval_advances_workflow_step()
    {
        $user = User::where('email', 'john.doe@enterprise.com')->first();
        $manager = User::where('email', 'admin@enterprise.com')->first();
        $definition = WorkflowDefinition::where('code', 'HR-LEAVE')->first();

        /** @var WorkflowEngineService $engine */
        $engine = app(WorkflowEngineService::class);
        $instance = $engine->startWorkflow($definition, $user, [
            'leave_type' => 'Annual Leave',
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-05',
            'reason' => 'Summer Vacation',
        ]);

        $task = Task::where('workflow_instance_id', $instance->id)->where('status', 'pending')->first();

        // Process Manager Approval
        $engine->processDecision($task, $manager, 'approved', 'Approved. Have a great vacation!');

        $task->refresh();
        $this->assertEquals('completed', $task->status);
        $this->assertDatabaseHas('approvals', [
            'workflow_instance_id' => $instance->id,
            'approver_id' => $manager->id,
            'decision' => 'approved',
        ]);
    }

    public function test_rejection_marks_workflow_rejected()
    {
        $user = User::where('email', 'john.doe@enterprise.com')->first();
        $manager = User::where('email', 'admin@enterprise.com')->first();
        $definition = WorkflowDefinition::where('code', 'FIN-REIMB')->first();

        /** @var WorkflowEngineService $engine */
        $engine = app(WorkflowEngineService::class);
        $instance = $engine->startWorkflow($definition, $user, [
            'amount' => 5000,
            'expense_date' => '2026-08-01',
            'description' => 'Unjustified expense',
        ]);

        $task = Task::where('workflow_instance_id', $instance->id)->first();
        $engine->processDecision($task, $manager, 'rejected', 'Missing itemized receipt.');

        $instance->refresh();
        $this->assertEquals('rejected', $instance->status);
    }

    public function test_sla_breach_auto_escalates_task()
    {
        $user = User::where('email', 'john.doe@enterprise.com')->first();
        $cfo = User::where('email', 'finance.head@enterprise.com')->first();
        $definition = WorkflowDefinition::where('code', 'CAPEX-PROC')->first();

        /** @var WorkflowEngineService $engine */
        $engine = app(WorkflowEngineService::class);
        $instance = $engine->startWorkflow($definition, $user, [
            'title' => 'New Laptops',
            'amount' => 5000,
            'vendor_name' => 'Apple',
            'category' => 'Hardware & Infrastructure',
            'justification' => 'Engineering refresh',
        ]);

        $task = Task::where('workflow_instance_id', $instance->id)->first();
        
        // Ensure escalation user is set on step
        $task->step->update(['escalation_user_id' => $cfo->id]);
        $task->update(['due_at' => now()->subHours(5)]);

        /** @var SLAEngineService $slaEngine */
        $slaEngine = app(SLAEngineService::class);
        $result = $slaEngine->checkAndEscalateOverdueTasks();

        $this->assertGreaterThan(0, $result['escalated']);
        $task->refresh();
        $this->assertEquals('escalated', $task->status);
    }

    public function test_rest_api_workflows_endpoint()
    {
        $response = $this->getJson('/api/v1/workflows');
        $response->assertStatus(200)
                 ->assertJsonStructure(['status', 'data']);
    }
}
