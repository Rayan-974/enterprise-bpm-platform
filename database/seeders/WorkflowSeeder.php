<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\FormTemplate;
use App\Models\FormField;
use App\Models\User;
use App\Models\WorkflowDefinition;
use App\Models\WorkflowStep;
use Illuminate\Database\Seeder;

class WorkflowSeeder extends Seeder
{
    public function run(): void
    {
        $hr = Department::where('code', 'HR')->first();
        $fin = Department::where('code', 'FIN')->first();
        $proc = Department::where('code', 'PROC')->first();
        $legal = Department::where('code', 'LEGAL')->first();
        $admin = User::where('email', 'admin@enterprise.com')->first();
        $finHead = User::where('email', 'finance.head@enterprise.com')->first();

        // =========================================================================
        // WORKFLOW 1: Capital Expenditure & Purchase Order (CAPEX)
        // =========================================================================
        $capex = WorkflowDefinition::updateOrCreate(['code' => 'CAPEX-PROC'], [
            'name' => 'Capital Expenditure & Procurement Approval',
            'category' => 'procurement',
            'description' => 'Approval chain for capital expenditures exceeding department thresholds.',
            'department_id' => $proc?->id,
            'version' => 1,
            'is_active' => true,
            'sla_hours' => 48,
            'created_by' => $admin?->id,
        ]);

        // Form Template
        $capexForm = FormTemplate::updateOrCreate(['workflow_definition_id' => $capex->id], [
            'title' => 'Capital Expenditure Request Form',
            'description' => 'Fill in justification, itemized breakdown, and target vendor details.',
            'is_active' => true,
        ]);

        $capexFields = [
            ['field_name' => 'title', 'label' => 'Project Title', 'field_type' => 'text', 'is_required' => true, 'field_order' => 1],
            ['field_name' => 'amount', 'label' => 'Total Amount (USD)', 'field_type' => 'number', 'is_required' => true, 'validation_rules' => ['numeric', 'min:100'], 'field_order' => 2],
            ['field_name' => 'vendor_name', 'label' => 'Vendor / Supplier Name', 'field_type' => 'text', 'is_required' => true, 'field_order' => 3],
            ['field_name' => 'category', 'label' => 'Expenditure Category', 'field_type' => 'dropdown', 'is_required' => true, 'options' => ['Hardware & Infrastructure', 'Software Licenses', 'Consulting', 'Facilities'], 'field_order' => 4],
            ['field_name' => 'justification', 'label' => 'Business Justification', 'field_type' => 'textarea', 'is_required' => true, 'field_order' => 5],
        ];
        foreach ($capexFields as $f) {
            FormField::updateOrCreate(['form_template_id' => $capexForm->id, 'field_name' => $f['field_name']], $f);
        }

        // Steps
        WorkflowStep::updateOrCreate(['workflow_definition_id' => $capex->id, 'step_order' => 1], [
            'name' => 'Direct Manager Approval',
            'type' => 'approval',
            'assignee_type' => 'manager',
            'sla_hours' => 24,
            'escalation_user_id' => $finHead?->id,
        ]);
        WorkflowStep::updateOrCreate(['workflow_definition_id' => $capex->id, 'step_order' => 2], [
            'name' => 'Procurement Verification',
            'type' => 'approval',
            'assignee_type' => 'department_head',
            'sla_hours' => 24,
        ]);
        WorkflowStep::updateOrCreate(['workflow_definition_id' => $capex->id, 'step_order' => 3], [
            'name' => 'CFO / Finance Authorization',
            'type' => 'approval',
            'assignee_type' => 'role',
            'assignee_value' => 'Manager',
            'sla_hours' => 48,
        ]);

        // =========================================================================
        // WORKFLOW 2: Employee Leave Request (HR)
        // =========================================================================
        $leave = WorkflowDefinition::updateOrCreate(['code' => 'HR-LEAVE'], [
            'name' => 'Employee Leave Request',
            'category' => 'hr',
            'description' => 'Annual, Sick, or Unpaid Leave request submission and manager sign-off.',
            'department_id' => $hr?->id,
            'version' => 1,
            'is_active' => true,
            'sla_hours' => 24,
            'created_by' => $admin?->id,
        ]);

        $leaveForm = FormTemplate::updateOrCreate(['workflow_definition_id' => $leave->id], [
            'title' => 'Leave Request Form',
            'description' => 'Specify dates and leave type.',
            'is_active' => true,
        ]);

        $leaveFields = [
            ['field_name' => 'leave_type', 'label' => 'Leave Type', 'field_type' => 'dropdown', 'is_required' => true, 'options' => ['Annual Leave', 'Sick Leave', 'Parental Leave', 'Unpaid Leave'], 'field_order' => 1],
            ['field_name' => 'start_date', 'label' => 'Start Date', 'field_type' => 'date', 'is_required' => true, 'field_order' => 2],
            ['field_name' => 'end_date', 'label' => 'End Date', 'field_type' => 'date', 'is_required' => true, 'field_order' => 3],
            ['field_name' => 'reason', 'label' => 'Comments / Notes', 'field_type' => 'textarea', 'is_required' => false, 'field_order' => 4],
        ];
        foreach ($leaveFields as $f) {
            FormField::updateOrCreate(['form_template_id' => $leaveForm->id, 'field_name' => $f['field_name']], $f);
        }

        WorkflowStep::updateOrCreate(['workflow_definition_id' => $leave->id, 'step_order' => 1], [
            'name' => 'Line Manager Sign-off',
            'type' => 'approval',
            'assignee_type' => 'manager',
            'sla_hours' => 12,
        ]);
        WorkflowStep::updateOrCreate(['workflow_definition_id' => $leave->id, 'step_order' => 2], [
            'name' => 'HR Operations Record Update',
            'type' => 'approval',
            'assignee_type' => 'department_head',
            'sla_hours' => 24,
        ]);

        // =========================================================================
        // WORKFLOW 3: Vendor Reimbursement
        // =========================================================================
        $reimb = WorkflowDefinition::updateOrCreate(['code' => 'FIN-REIMB'], [
            'name' => 'Expense Reimbursement Request',
            'category' => 'finance',
            'description' => 'Employee travel, client entertainment, and pocket expenses reimbursement.',
            'department_id' => $fin?->id,
            'version' => 1,
            'is_active' => true,
            'sla_hours' => 36,
            'created_by' => $admin?->id,
        ]);

        $reimbForm = FormTemplate::updateOrCreate(['workflow_definition_id' => $reimb->id], [
            'title' => 'Expense Claim Form',
            'description' => 'Attach receipts and specify expenses.',
            'is_active' => true,
        ]);

        $reimbFields = [
            ['field_name' => 'amount', 'label' => 'Claim Amount (USD)', 'field_type' => 'number', 'is_required' => true, 'field_order' => 1],
            ['field_name' => 'expense_date', 'label' => 'Expense Date', 'field_type' => 'date', 'is_required' => true, 'field_order' => 2],
            ['field_name' => 'description', 'label' => 'Expense Description', 'field_type' => 'textarea', 'is_required' => true, 'field_order' => 3],
        ];
        foreach ($reimbFields as $f) {
            FormField::updateOrCreate(['form_template_id' => $reimbForm->id, 'field_name' => $f['field_name']], $f);
        }

        WorkflowStep::updateOrCreate(['workflow_definition_id' => $reimb->id, 'step_order' => 1], [
            'name' => 'Finance Audit',
            'type' => 'approval',
            'assignee_type' => 'department_head',
            'sla_hours' => 24,
        ]);

        // =========================================================================
        // WORKFLOW 4: Contract Legal Review
        // =========================================================================
        $legalWf = WorkflowDefinition::updateOrCreate(['code' => 'LEGAL-REV'], [
            'name' => 'Master Services Agreement & Contract Legal Review',
            'category' => 'legal',
            'description' => 'Review vendor contracts, NDAs, and enterprise agreements.',
            'department_id' => $legal?->id,
            'version' => 1,
            'is_active' => true,
            'sla_hours' => 72,
            'created_by' => $admin?->id,
        ]);

        $legalForm = FormTemplate::updateOrCreate(['workflow_definition_id' => $legalWf->id], [
            'title' => 'Contract Review Submission',
            'description' => 'Submit contract draft for legal assessment.',
            'is_active' => true,
        ]);

        $legalFields = [
            ['field_name' => 'contract_title', 'label' => 'Contract Title', 'field_type' => 'text', 'is_required' => true, 'field_order' => 1],
            ['field_name' => 'counterparty', 'label' => 'Counterparty / Company', 'field_type' => 'text', 'is_required' => true, 'field_order' => 2],
            ['field_name' => 'contract_value', 'label' => 'Contract Total Value (USD)', 'field_type' => 'number', 'is_required' => true, 'field_order' => 3],
            ['field_name' => 'special_terms', 'label' => 'Special Terms or Notes', 'field_type' => 'textarea', 'is_required' => false, 'field_order' => 4],
        ];
        foreach ($legalFields as $f) {
            FormField::updateOrCreate(['form_template_id' => $legalForm->id, 'field_name' => $f['field_name']], $f);
        }

        WorkflowStep::updateOrCreate(['workflow_definition_id' => $legalWf->id, 'step_order' => 1], [
            'name' => 'Legal Counsel Assessment',
            'type' => 'approval',
            'assignee_type' => 'department_head',
            'sla_hours' => 48,
        ]);
    }
}
