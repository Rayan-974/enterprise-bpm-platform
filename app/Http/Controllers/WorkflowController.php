<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\FormTemplate;
use App\Models\FormField;
use App\Models\WorkflowDefinition;
use App\Models\WorkflowInstance;
use App\Models\WorkflowStep;
use App\Services\AIWorkflowOptimizerService;
use App\Services\BpmnEngineService;
use App\Services\WorkflowEngineService;
use App\Services\WorkflowVersioningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkflowController extends Controller
{
    public function __construct(
        protected WorkflowEngineService $workflowEngine,
        protected BpmnEngineService $bpmnEngine,
        protected AIWorkflowOptimizerService $aiOptimizer,
        protected WorkflowVersioningService $versioningService
    ) {}

    public function index(Request $request)
    {
        $category = $request->get('category');
        $query = WorkflowDefinition::with(['department', 'steps', 'activeFormTemplate']);
        
        if ($category) {
            $query->where('category', $category);
        }

        $workflows = $query->where('is_active', true)->get();

        return view('workflows.index', compact('workflows', 'category'));
    }

    public function show(int $id)
    {
        $workflow = WorkflowDefinition::with(['steps', 'activeFormTemplate.fields', 'department'])->findOrFail($id);
        $aiSuggestions = $this->aiOptimizer->generateOptimizationSuggestions($workflow);

        return view('workflows.show', compact('workflow', 'aiSuggestions'));
    }

    public function submit(Request $request, int $id)
    {
        $workflow = WorkflowDefinition::with(['activeFormTemplate.fields'])->findOrFail($id);
        $user = Auth::user();

        try {
            $payload = $request->except(['_token']);
            $instance = $this->workflowEngine->startWorkflow($workflow, $user, $payload);

            return redirect()->route('workflows.track', $instance->uuid)
                ->with('success', "Workflow request #{$instance->uuid} successfully initiated!");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function track(string $uuid)
    {
        $instance = WorkflowInstance::with([
            'definition.steps',
            'requester',
            'department',
            'currentStep',
            'tasks.assignee',
            'tasks.step',
            'approvals.approver',
            'approvals.step',
        ])->where('uuid', $uuid)->firstOrFail();

        return view('workflows.track', compact('instance'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        return view('workflows.designer', compact('departments'));
    }

    /**
     * Interactive Drag & Drop Workflow Builder UI.
     */
    public function builder(int $id = null)
    {
        $workflow = $id ? WorkflowDefinition::with(['steps', 'activeFormTemplate.fields'])->findOrFail($id) : null;
        $departments = Department::where('is_active', true)->get();
        $aiSuggestions = $workflow ? $this->aiOptimizer->generateOptimizationSuggestions($workflow) : [];

        return view('workflows.builder', compact('workflow', 'departments', 'aiSuggestions'));
    }

    /**
     * Export Workflow to BPMN 2.0 XML.
     */
    public function exportBpmn(int $id)
    {
        $workflow = WorkflowDefinition::with('steps')->findOrFail($id);
        $xml = $this->bpmnEngine->exportToBpmnXml($workflow);

        $filename = "workflow_" . $workflow->code . ".bpmn20.xml";

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Import BPMN 2.0 XML File.
     */
    public function importBpmn(Request $request)
    {
        $request->validate([
            'bpmn_file' => 'required|file|mimes:xml,bpmn,text',
        ]);

        $xmlContent = file_get_contents($request->file('bpmn_file')->getRealPath());
        $workflow = $this->bpmnEngine->importFromBpmnXml($xmlContent, Auth::id());

        return redirect()->route('workflows.show', $workflow->id)
            ->with('success', "BPMN 2.0 process imported successfully as '{$workflow->name}'!");
    }

    /**
     * Create New Version of Workflow.
     */
    public function createVersion(int $id)
    {
        $workflow = WorkflowDefinition::findOrFail($id);
        $newVersion = $this->versioningService->createNewVersion($workflow);

        return redirect()->route('workflows.show', $newVersion->id)
            ->with('success', "New version V{$newVersion->version} created and activated!");
    }

    public function storeDesigner(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:workflow_definitions,code',
            'category' => 'required|string',
            'department_id' => 'nullable|exists:departments,id',
            'sla_hours' => 'required|integer|min:1',
            'steps' => 'required|array|min:1',
            'steps.*.name' => 'required|string',
            'steps.*.type' => 'required|string',
            'steps.*.assignee_type' => 'required|string',
            'form_title' => 'required|string',
            'fields' => 'required|array|min:1',
            'fields.*.label' => 'required|string',
            'fields.*.field_name' => 'required|string',
            'fields.*.field_type' => 'required|string',
        ]);

        $definition = WorkflowDefinition::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'category' => $request->category,
            'description' => $request->description,
            'department_id' => $request->department_id,
            'version' => 1,
            'is_active' => true,
            'sla_hours' => $request->sla_hours,
            'created_by' => Auth::id(),
        ]);

        // Create Steps
        foreach ($request->steps as $index => $s) {
            WorkflowStep::create([
                'workflow_definition_id' => $definition->id,
                'step_order' => $index + 1,
                'name' => $s['name'],
                'type' => $s['type'],
                'assignee_type' => $s['assignee_type'],
                'assignee_value' => $s['assignee_value'] ?? null,
                'sla_hours' => $s['sla_hours'] ?? 24,
            ]);
        }

        // Create Form Template & Fields
        $template = FormTemplate::create([
            'workflow_definition_id' => $definition->id,
            'title' => $request->form_title,
            'description' => $request->form_description,
            'is_active' => true,
        ]);

        foreach ($request->fields as $index => $f) {
            $options = !empty($f['options']) ? array_map('trim', explode(',', $f['options'])) : null;
            FormField::create([
                'form_template_id' => $template->id,
                'field_name' => strtolower(str_replace(' ', '_', $f['field_name'])),
                'label' => $f['label'],
                'field_type' => $f['field_type'],
                'is_required' => isset($f['is_required']),
                'options' => $options,
                'field_order' => $index + 1,
            ]);
        }

        return redirect()->route('workflows.index')
            ->with('success', "Workflow '{$definition->name}' and dynamic form published successfully!");
    }
}
