<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Services\DigitalSignatureService;
use App\Services\TaskManagementService;
use App\Services\WorkflowEngineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function __construct(
        protected WorkflowEngineService $workflowEngine,
        protected TaskManagementService $taskService,
        protected DigitalSignatureService $signatureService
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->get('status', 'pending');

        $query = Task::with(['workflowInstance.definition', 'workflowInstance.requester', 'step'])
            ->where(function ($q) use ($user) {
                $q->where('assignee_id', $user->id)
                  ->orWhere('delegated_to_id', $user->id);
            });

        if ($status === 'overdue') {
            $query->where('status', 'pending')->where('due_at', '<', now());
        } elseif ($status === 'delegated') {
            $query->where('status', 'delegated');
        } else {
            $query->where('status', $status);
        }

        $tasks = $query->orderBy('due_at', 'asc')->paginate(15);
        $eligibleUsers = User::where('is_active', true)->where('id', '!=', $user->id)->get();

        return view('tasks.index', compact('tasks', 'status', 'eligibleUsers'));
    }

    public function show(int $id)
    {
        $task = Task::with(['workflowInstance.definition.steps', 'workflowInstance.requester', 'workflowInstance.approvals.approver', 'step'])->findOrFail($id);
        $eligibleUsers = User::where('is_active', true)->where('id', '!=', Auth::id())->get();

        return view('tasks.show', compact('task', 'eligibleUsers'));
    }

    public function approve(Request $request, int $id)
    {
        $task = Task::findOrFail($id);
        $user = Auth::user();
        $comments = $request->input('comments') ?? 'Approved';
        $signatureData = $request->input('signature_data');

        // Create SHA-256 Digital Signature
        $this->signatureService->signApproval($task, $user, $signatureData);

        // Process Decision
        $instance = $this->workflowEngine->processDecision($task, $user, 'approved', $comments);

        return redirect()->route('tasks.index')
            ->with('success', "Task for '{$task->step->name}' successfully approved & digitally signed!");
    }

    public function reject(Request $request, int $id)
    {
        $request->validate([
            'comments' => 'nullable|string|max:1000'
        ]);

        $task = Task::findOrFail($id);
        $user = Auth::user();

        $comments = $request->input('comments');
        if (empty($comments)) {
            $comments = "Rejected by {$user->name}";
        }

        $instance = $this->workflowEngine->processDecision($task, $user, 'rejected', $comments);

        return redirect()->route('tasks.index')
            ->with('success', "Workflow request #{$instance->uuid} rejected.");
    }

    public function delegate(Request $request, int $id)
    {
        $request->validate([
            'delegate_user_id' => 'required|exists:users,id',
            'comments' => 'nullable|string|max:500',
        ]);

        $task = Task::findOrFail($id);
        $delegateUser = User::findOrFail($request->delegate_user_id);

        $this->taskService->delegateTask($task, $delegateUser, $request->comments);

        return redirect()->route('tasks.index')
            ->with('success', "Task successfully delegated to {$delegateUser->name}.");
    }
}
