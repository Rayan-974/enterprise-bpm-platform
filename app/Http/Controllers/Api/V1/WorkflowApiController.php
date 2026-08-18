<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\WorkflowDefinition;
use App\Models\WorkflowInstance;
use App\Services\WorkflowEngineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkflowApiController extends Controller
{
    public function __construct(protected WorkflowEngineService $workflowEngine) {}

    public function index(Request $request): JsonResponse
    {
        $workflows = WorkflowDefinition::with('department')
            ->where('is_active', true)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $workflows,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $workflow = WorkflowDefinition::with(['steps', 'activeFormTemplate.fields'])->find($id);

        if (!$workflow) {
            return response()->json(['status' => 'error', 'message' => 'Workflow not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $workflow,
        ]);
    }

    public function start(Request $request, int $id): JsonResponse
    {
        $workflow = WorkflowDefinition::with('activeFormTemplate.fields')->find($id);

        if (!$workflow) {
            return response()->json(['status' => 'error', 'message' => 'Workflow not found'], 404);
        }

        try {
            $user = $request->user() ?? \App\Models\User::first();
            $instance = $this->workflowEngine->startWorkflow($workflow, $user, $request->input('payload', []));

            return response()->json([
                'status' => 'success',
                'message' => 'Workflow started successfully',
                'data' => $instance->load(['currentStep', 'definition']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function track(string $uuid): JsonResponse
    {
        $instance = WorkflowInstance::with(['definition', 'currentStep', 'tasks', 'approvals'])->where('uuid', $uuid)->first();

        if (!$instance) {
            return response()->json(['status' => 'error', 'message' => 'Workflow instance not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $instance,
        ]);
    }
}
