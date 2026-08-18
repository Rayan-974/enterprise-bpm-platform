<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_definition_id')->constrained('workflow_definitions')->cascadeOnDelete();
            $table->integer('step_order')->default(1);
            $table->string('name');
            $table->enum('type', ['approval', 'decision', 'parallel', 'auto_action', 'notification'])->default('approval');
            $table->enum('assignee_type', ['role', 'department_head', 'manager', 'user', 'requester_manager'])->default('role');
            $table->string('assignee_value')->nullable(); // role name or user_id
            $table->json('condition_rules')->nullable(); // rules for decision node evaluation
            $table->integer('sla_hours')->default(24);
            $table->foreignId('escalation_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('auto_action_type')->nullable(); // approve, reject, notify
            $table->boolean('require_all_parallel')->default(false); // for parallel step types
            $table->timestamps();

            $table->index(['workflow_definition_id', 'step_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_steps');
    }
};
