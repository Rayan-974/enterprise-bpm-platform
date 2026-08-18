<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sla_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_definition_id')->constrained('workflow_definitions')->cascadeOnDelete();
            $table->foreignId('step_id')->nullable()->constrained('workflow_steps')->cascadeOnDelete();
            $table->integer('warning_threshold_percent')->default(75); // warn at 75% SLA time elapsed
            $table->integer('max_duration_hours')->default(24);
            $table->enum('escalation_action', ['notify_manager', 'auto_reassign', 'auto_approve', 'auto_reject'])->default('notify_manager');
            $table->foreignId('target_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sla_rules');
    }
};
