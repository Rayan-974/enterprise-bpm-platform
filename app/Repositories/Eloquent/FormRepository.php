<?php

namespace App\Repositories\Eloquent;

use App\Models\FormTemplate;
use App\Models\FormField;
use App\Repositories\Contracts\FormRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class FormRepository implements FormRepositoryInterface
{
    public function createTemplate(array $data): FormTemplate
    {
        return FormTemplate::create($data);
    }

    public function getTemplateByWorkflowId(int $workflowDefinitionId): ?FormTemplate
    {
        return FormTemplate::with('fields')->where('workflow_definition_id', $workflowDefinitionId)->where('is_active', true)->first();
    }

    public function addField(int $formTemplateId, array $fieldData): FormField
    {
        $fieldData['form_template_id'] = $formTemplateId;
        return FormField::create($fieldData);
    }

    public function updateField(int $fieldId, array $fieldData): FormField
    {
        $field = FormField::findOrFail($fieldId);
        $field->update($fieldData);
        return $field;
    }

    public function deleteField(int $fieldId): bool
    {
        $field = FormField::findOrFail($fieldId);
        return $field->delete();
    }

    public function getFieldsForTemplate(int $formTemplateId): Collection
    {
        return FormField::where('form_template_id', $formTemplateId)->orderBy('field_order')->get();
    }
}
