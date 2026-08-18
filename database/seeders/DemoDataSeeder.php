<?php

namespace Database\Seeders;

use App\Models\Approval;
use App\Models\AuditLog;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkflowDefinition;
use App\Models\WorkflowInstance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@enterprise.com')->first();
        $hrHead = User::where('email', 'hr.head@enterprise.com')->first();
        $finHead = User::where('email', 'finance.head@enterprise.com')->first();
        $procHead = User::where('email', 'procurement.head@enterprise.com')->first();
        $legalHead = User::where('email', 'legal.head@enterprise.com')->first();
        $emp1 = User::where('email', 'john.doe@enterprise.com')->first();
        $emp2 = User::where('email', 'maria.garcia@enterprise.com')->first();

        $capexWf = WorkflowDefinition::where('code', 'CAPEX-PROC')->first();
        $leaveWf = WorkflowDefinition::where('code', 'HR-LEAVE')->first();
        $reimbWf = WorkflowDefinition::where('code', 'FIN-REIMB')->first();
        $legalWf = WorkflowDefinition::where('code', 'LEGAL-REV')->first();

        // 1. Expense Reimbursement Request from John Doe (Pending Finance Audit for Finance Head)
        if ($reimbWf && $emp1 && $finHead) {
            $step1 = $reimbWf->steps()->where('step_order', 1)->first();
            $inst1 = WorkflowInstance::create([
                'uuid' => (string) Str::uuid(),
                'workflow_definition_id' => $reimbWf->id,
                'requester_id' => $emp1->id,
                'department_id' => $emp1->department_id,
                'current_step_id' => $step1?->id,
                'status' => 'in_progress',
                'payload' => [
                    'amount' => 1450.00,
                    'expense_date' => '2026-08-15',
                    'description' => 'AWS Cloud Infrastructure certification & travel expenses to DevOps Summit.'
                ],
                'started_at' => now()->subDays(1),
                'due_at' => now()->addHours(36),
            ]);

            Task::create([
                'workflow_instance_id' => $inst1->id,
                'step_id' => $step1->id,
                'assignee_id' => $finHead->id,
                'status' => 'pending',
                'due_at' => now()->addHours(24),
            ]);

            AuditLog::create([
                'user_id' => $emp1->id,
                'action' => 'workflow.started',
                'entity_type' => WorkflowInstance::class,
                'entity_id' => $inst1->id,
                'new_values' => ['workflow' => 'Expense Reimbursement', 'amount' => 1450.00],
                'ip_address' => '127.0.0.1',
            ]);
        }

        // 2. Capital Expenditure Request from John Doe (Pending Direct Manager Approval for Super Admin)
        if ($capexWf && $emp1 && $admin) {
            $step1 = $capexWf->steps()->where('step_order', 1)->first();
            $inst2 = WorkflowInstance::create([
                'uuid' => (string) Str::uuid(),
                'workflow_definition_id' => $capexWf->id,
                'requester_id' => $emp1->id,
                'department_id' => $emp1->department_id,
                'current_step_id' => $step1?->id,
                'status' => 'in_progress',
                'payload' => [
                    'title' => 'High-Performance Workstation Upgrade for AI Engine',
                    'amount' => 4850.00,
                    'vendor_name' => 'Dell Enterprise Hardware Inc.',
                    'category' => 'Hardware & Infrastructure',
                    'justification' => 'Required for running local LLM benchmarks and automated workflow testing suites.'
                ],
                'started_at' => now()->subHours(5),
                'due_at' => now()->addHours(48),
            ]);

            Task::create([
                'workflow_instance_id' => $inst2->id,
                'step_id' => $step1->id,
                'assignee_id' => $admin->id,
                'status' => 'pending',
                'due_at' => now()->addHours(24),
            ]);
        }

        // 3. Leave Request from Maria Garcia (Pending Line Manager Sign-off for HR Head)
        if ($leaveWf && $emp2 && $hrHead) {
            $step1 = $leaveWf->steps()->where('step_order', 1)->first();
            $inst3 = WorkflowInstance::create([
                'uuid' => (string) Str::uuid(),
                'workflow_definition_id' => $leaveWf->id,
                'requester_id' => $emp2->id,
                'department_id' => $emp2->department_id,
                'current_step_id' => $step1?->id,
                'status' => 'in_progress',
                'payload' => [
                    'leave_type' => 'Annual Leave',
                    'start_date' => '2026-09-01',
                    'end_date' => '2026-09-10',
                    'reason' => 'Annual family vacation.'
                ],
                'started_at' => now()->subHours(12),
                'due_at' => now()->addHours(24),
            ]);

            Task::create([
                'workflow_instance_id' => $inst3->id,
                'step_id' => $step1->id,
                'assignee_id' => $hrHead->id,
                'status' => 'pending',
                'due_at' => now()->addHours(12),
            ]);
        }

        // 4. Contract Legal Review from Procurement Lead (Pending Legal Assessment for Legal Head)
        if ($legalWf && $procHead && $legalHead) {
            $step1 = $legalWf->steps()->where('step_order', 1)->first();
            $inst4 = WorkflowInstance::create([
                'uuid' => (string) Str::uuid(),
                'workflow_definition_id' => $legalWf->id,
                'requester_id' => $procHead->id,
                'department_id' => $procHead->department_id,
                'current_step_id' => $step1?->id,
                'status' => 'in_progress',
                'payload' => [
                    'contract_title' => 'Global Cloud Hosting & SLA Master Agreement',
                    'counterparty' => 'Microsoft Azure Enterprise Services',
                    'contract_value' => 125000.00,
                    'special_terms' => 'Includes 99.99% uptime guarantee and multi-region data failover.'
                ],
                'started_at' => now()->subHours(18),
                'due_at' => now()->addHours(72),
            ]);

            Task::create([
                'workflow_instance_id' => $inst4->id,
                'step_id' => $step1->id,
                'assignee_id' => $legalHead->id,
                'status' => 'pending',
                'due_at' => now()->addHours(48),
            ]);
        }

        // 5. Fully Approved Reimbursement Request History
        if ($reimbWf && $emp2 && $finHead) {
            $step1 = $reimbWf->steps()->where('step_order', 1)->first();
            $inst5 = WorkflowInstance::create([
                'uuid' => (string) Str::uuid(),
                'workflow_definition_id' => $reimbWf->id,
                'requester_id' => $emp2->id,
                'department_id' => $emp2->department_id,
                'current_step_id' => $step1?->id,
                'status' => 'approved',
                'payload' => [
                    'amount' => 350.00,
                    'expense_date' => '2026-08-01',
                    'description' => 'Client dinner & marketing presentation materials.'
                ],
                'started_at' => now()->subDays(5),
                'completed_at' => now()->subDays(4),
            ]);

            $task5 = Task::create([
                'workflow_instance_id' => $inst5->id,
                'step_id' => $step1->id,
                'assignee_id' => $finHead->id,
                'status' => 'completed',
                'completed_at' => now()->subDays(4),
            ]);

            Approval::create([
                'workflow_instance_id' => $inst5->id,
                'task_id' => $task5->id,
                'step_id' => $step1->id,
                'approver_id' => $finHead->id,
                'decision' => 'approved',
                'comments' => 'Verified receipts and approved for payout.',
                'ip_address' => '127.0.0.1',
            ]);
        }
    }
}
