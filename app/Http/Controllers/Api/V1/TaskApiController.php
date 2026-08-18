<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Services\WorkflowEngineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskApiController extends Controller
{
    public function __construct(protected WorkflowEngineService $workflowEngine) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user() ?? \App\Models\User::first();
        $tasks = Task::with(['workflowInstance.definition', 'step'])
            ->where('status', 'pending')
            ->where(function ($q) use ($user) {
                $q->where('assignee_id', $user->id)
                  ->orWhere('delegated_to_id', $user->id);
            })
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $tasks,
        ]);
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['status' => 'error', 'message' => 'Task not found'], 404);
        }

        $user = $request->user() ?? \App\Models\User::first();
        $comments = $request->input('comments');

        $instance = $this->workflowEngine->processDecision($task, $user, 'approved', $comments);

        return response()->json([
            'status' => 'success',
            'message' => 'Task approved successfully',
            'data' => $instance,
        ]);
    }

    public function reject(Request $request, int $id): JsonResponse
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['status' => 'error', 'message' => 'Task not found'], 404);
        }

        $user = $request->user() ?? \App\Models\User::first();
        $comments = $request->input('comments', 'Rejected via API');

        $instance = $this->workflowEngine->processDecision($task, $user, 'rejected', $comments);

        return response()->json([
            'status' => 'success',
            'message' => 'Task rejected successfully',
            'data' => $instance,
        ]);
    }
}
