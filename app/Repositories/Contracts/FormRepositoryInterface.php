<?php

namespace App\Repositories\Contracts;

use App\Models\FormTemplate;
use App\Models\FormField;
use Illuminate\Database\Eloquent\Collection;

interface FormRepositoryInterface
{
    public function createTemplate(array $data): FormTemplate;
    public function getTemplateByWorkflowId(int $workflowDefinitionId): ?FormTemplate;
    public function addField(int $formTemplateId, array $fieldData): FormField;
    public function updateField(int $fieldId, array $fieldData): FormField;
    public function deleteField(int $fieldId): bool;
    public function getFieldsForTemplate(int $formTemplateId): Collection;
}
