<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workflow_instances', function (Blueprint $table) {
            $table->index(['status', 'department_id'], 'idx_wf_status_dept');
            $table->index(['requester_id', 'status'], 'idx_wf_requester_status');
            $table->index(['created_at', 'status'], 'idx_wf_created_status');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->index(['assignee_id', 'status', 'due_at'], 'idx_tasks_assignee_status_due');
            $table->index(['workflow_instance_id', 'step_id'], 'idx_tasks_wf_step');
            $table->index(['status', 'due_at'], 'idx_tasks_status_due');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'idx_audit_user_created');
            $table->index(['entity_type', 'entity_id'], 'idx_audit_entity');
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex('idx_audit_user_created');
            $table->dropIndex('idx_audit_entity');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('idx_tasks_assignee_status_due');
            $table->dropIndex('idx_tasks_wf_step');
            $table->dropIndex('idx_tasks_status_due');
        });

        Schema::table('workflow_instances', function (Blueprint $table) {
            $table->dropIndex('idx_wf_status_dept');
            $table->dropIndex('idx_wf_requester_status');
            $table->dropIndex('idx_wf_created_status');
        });
    }
};
