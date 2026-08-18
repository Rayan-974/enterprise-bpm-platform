<?php

namespace Tests\Feature;

use App\Models\DigitalSignature;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WorkflowDefinition;
use App\Services\AIWorkflowOptimizerService;
use App\Services\BpmnEngineService;
use App\Services\DigitalSignatureService;
use App\Services\WorkflowEngineService;
use App\Services\WorkflowVersioningService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdvancedBpmFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_multi_tenant_saas_isolation()
    {
        $tenant = Tenant::create(['name' => 'Acme Corp SaaS', 'domain' => 'acme.bpm.com']);
        $user = User::first();
        $user->update(['tenant_id' => $tenant->id]);

        $this->assertEquals($tenant->id, $user->tenant_id);
        $this->assertDatabaseHas('tenants', ['domain' => 'acme.bpm.com']);
    }

    public function test_bpmn_20_export_and_import()
    {
        $workflow = WorkflowDefinition::with('steps')->first();
        /** @var BpmnEngineService $bpmnEngine */
        $bpmnEngine = app(BpmnEngineService::class);

        // 1. Export to BPMN 2.0 XML
        $xml = $bpmnEngine->exportToBpmnXml($workflow);
        $this->assertStringContainsString('bpmn:definitions', $xml);
        $this->assertStringContainsString('bpmn:userTask', $xml);

        // 2. Import from BPMN 2.0 XML
        $user = User::first();
        $imported = $bpmnEngine->importFromBpmnXml($xml, $user->id);

        $this->assertInstanceOf(WorkflowDefinition::class, $imported);
        $this->assertDatabaseHas('workflow_definitions', ['id' => $imported->id]);
    }

    public function test_digital_signature_generation()
    {
        $user = User::where('email', 'john.doe@enterprise.com')->first();
        $manager = User::where('email', 'admin@enterprise.com')->first();
        $definition = WorkflowDefinition::where('code', 'CAPEX-PROC')->first();

        /** @var WorkflowEngineService $engine */
        $engine = app(WorkflowEngineService::class);
        $instance = $engine->startWorkflow($definition, $user, [
            'title' => 'Test Hardware Purchase',
            'amount' => 12000,
            'vendor_name' => 'Dell Inc',
            'category' => 'Hardware & Infrastructure',
            'justification' => 'Datacenter expansion',
        ]);

        $task = Task::where('workflow_instance_id', $instance->id)->first();

        /** @var DigitalSignatureService $signatureService */
        $signatureService = app(DigitalSignatureService::class);
        $sig = $signatureService->signApproval($task, $manager);

        $this->assertInstanceOf(DigitalSignature::class, $sig);
        $this->assertNotNull($sig->signature_hash);
        $this->assertDatabaseHas('digital_signatures', [
            'task_id' => $task->id,
            'user_id' => $manager->id,
        ]);
    }

    public function test_workflow_versioning()
    {
        $workflow = WorkflowDefinition::where('code', 'CAPEX-PROC')->first();
        /** @var WorkflowVersioningService $versioning */
        $versioning = app(WorkflowVersioningService::class);

        $v2 = $versioning->createNewVersion($workflow);

        $this->assertEquals(2, $v2->version);
        $this->assertTrue($v2->is_active);

        $workflow->refresh();
        $this->assertFalse($workflow->is_active);
    }

    public function test_ai_workflow_optimization_suggestions()
    {
        $workflow = WorkflowDefinition::first();
        /** @var AIWorkflowOptimizerService $aiOptimizer */
        $aiOptimizer = app(AIWorkflowOptimizerService::class);

        $suggestions = $aiOptimizer->generateOptimizationSuggestions($workflow);

        $this->assertIsArray($suggestions);
        $this->assertNotEmpty($suggestions);
    }
}
