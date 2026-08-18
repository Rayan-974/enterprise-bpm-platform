<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SlaRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_definition_id',
        'step_id',
        'warning_threshold_percent',
        'max_duration_hours',
        'escalation_action',
        'target_user_id',
    ];

    protected $casts = [
        'warning_threshold_percent' => 'integer',
        'max_duration_hours' => 'integer',
    ];

    public function workflowDefinition(): BelongsTo
    {
        return $this->belongsTo(WorkflowDefinition::class);
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class);
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}
