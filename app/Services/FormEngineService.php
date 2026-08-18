<?php

namespace App\Services;

use App\Models\FormTemplate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FormEngineService
{
    /**
     * Validate incoming payload against FormTemplate fields & rules.
     */
    public function validatePayload(FormTemplate $template, array $payload): array
    {
        $rules = [];
        $messages = [];

        foreach ($template->fields as $field) {
            $fieldRules = [];

            if ($field->is_required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            // Map field types to Laravel validation rules
            switch ($field->field_type) {
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'file':
                    $fieldRules[] = 'string'; // base64 or file path reference
                    break;
                case 'multiselect':
                    $fieldRules[] = 'array';
                    break;
                case 'dropdown':
                    if (!empty($field->options)) {
                        $fieldRules[] = 'in:' . implode(',', (array) $field->options);
                    }
                    break;
                default:
                    $fieldRules[] = 'string';
                    break;
            }

            // Custom validation rules defined as array in database
            if (!empty($field->validation_rules) && is_array($field->validation_rules)) {
                foreach ($field->validation_rules as $customRule) {
                    $fieldRules[] = $customRule;
                }
            }

            $rules[$field->field_name] = $fieldRules;
            $messages[$field->field_name . '.required'] = "The {$field->label} field is required.";
        }

        $validator = Validator::make($payload, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
