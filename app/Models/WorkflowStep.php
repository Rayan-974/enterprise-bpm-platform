<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_definition_id',
        'step_order',
        'name',
        'type',
        'assignee_type',
        'assignee_value',
        'condition_rules',
        'sla_hours',
        'escalation_user_id',
        'auto_action_type',
        'require_all_parallel',
    ];

    protected $casts = [
        'condition_rules' => 'array',
        'step_order' => 'integer',
        'sla_hours' => 'integer',
        'require_all_parallel' => 'boolean',
    ];

    public function workflowDefinition(): BelongsTo
    {
        return $this->belongsTo(WorkflowDefinition::class);
    }

    public function escalationUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'escalation_user_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'step_id');
    }
}
