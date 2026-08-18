<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$task = App\Models\Task::latest()->first();
if ($task) {
    echo "Task ID: " . $task->id . "\n";
    echo "Assigned User ID: " . $task->assigned_user_id . "\n";
    echo "Assigned User Name: " . ($task->assignedUser?->name ?? 'None') . "\n";
    echo "Status: " . $task->status . "\n";
    echo "Workflow Definition: " . $task->workflowInstance->definition->name . "\n";
    echo "Workflow Code: " . $task->workflowInstance->definition->code . "\n";
    echo "Requester Name: " . $task->workflowInstance->requester->name . "\n";
    echo "Requester Dept: " . ($task->workflowInstance->requester->department?->name ?? 'None') . "\n";
    echo "Step Name: " . $task->step->name . "\n";
    echo "Step Assignee Type: " . $task->step->assignee_type . "\n";
    echo "Step Assignee Value: " . $task->step->assignee_value . "\n";
} else {
    echo "No task found in database.\n";
}

$instance = App\Models\WorkflowInstance::latest()->first();
if ($instance) {
    echo "\nLatest Instance Status: " . $instance->status . "\n";
    echo "Latest Instance Current Step ID: " . $instance->current_step_id . "\n";
}
