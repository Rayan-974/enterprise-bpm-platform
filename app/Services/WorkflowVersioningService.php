<?php

namespace App\Services;

use App\Models\WorkflowDefinition;
use App\Models\WorkflowStep;
use App\Models\FormTemplate;
use App\Models\FormField;
use Illuminate\Support\Facades\DB;

class WorkflowVersioningService
{
    /**
     * Create a new version of a WorkflowDefinition (Increment version number, copy steps & form schema).
     */
    public function createNewVersion(WorkflowDefinition $definition): WorkflowDefinition
    {
        return DB::transaction(function () use ($definition) {
            // Deactivate old definition
            $definition->update(['is_active' => false]);

            $newVersion = WorkflowDefinition::create([
                'name' => $definition->name,
                'code' => $definition->code . '-V' . ($definition->version + 1),
                'category' => $definition->category,
                'description' => $definition->description,
                'department_id' => $definition->department_id,
                'version' => $definition->version + 1,
                'is_active' => true,
                'sla_hours' => $definition->sla_hours,
                'settings' => $definition->settings,
                'created_by' => $definition->created_by,
                'parent_workflow_id' => $definition->parent_workflow_id ?? $definition->id,
            ]);

            // Copy Steps
            foreach ($definition->steps as $step) {
                WorkflowStep::create([
                    'workflow_definition_id' => $newVersion->id,
                    'step_order' => $step->step_order,
                    'name' => $step->name,
                    'type' => $step->type,
                    'assignee_type' => $step->assignee_type,
                    'assignee_value' => $step->assignee_value,
                    'condition_rules' => $step->condition_rules,
                    'sla_hours' => $step->sla_hours,
                    'escalation_user_id' => $step->escalation_user_id,
                    'auto_action_type' => $step->auto_action_type,
                    'require_all_parallel' => $step->require_all_parallel,
                ]);
            }

            // Copy Form Template & Fields if exists
            if ($definition->activeFormTemplate) {
                $oldForm = $definition->activeFormTemplate;
                $newForm = FormTemplate::create([
                    'workflow_definition_id' => $newVersion->id,
                    'title' => $oldForm->title,
                    'description' => $oldForm->description,
                    'is_active' => true,
                ]);

                foreach ($oldForm->fields as $field) {
                    FormField::create([
                        'form_template_id' => $newForm->id,
                        'field_name' => $field->field_name,
                        'label' => $field->label,
                        'field_type' => $field->field_type,
                        'is_required' => $field->is_required,
                        'validation_rules' => $field->validation_rules,
                        'options' => $field->options,
                        'conditional_logic' => $field->conditional_logic,
                        'field_order' => $field->field_order,
                    ]);
                }
            }

            return $newVersion;
        });
    }
}
