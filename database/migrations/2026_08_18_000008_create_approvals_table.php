<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_instance_id')->constrained('workflow_instances')->cascadeOnDelete();
            $table->foreignId('task_id')->nullable()->constrained('tasks')->nullOnDelete();
            $table->foreignId('step_id')->constrained('workflow_steps')->cascadeOnDelete();
            $table->foreignId('approver_id')->constrained('users')->cascadeOnDelete();
            $table->enum('decision', ['approved', 'rejected', 'delegated', 'escalated'])->default('approved');
            $table->text('comments')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['workflow_instance_id', 'created_at']);
            $table->index(['approver_id', 'decision']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
