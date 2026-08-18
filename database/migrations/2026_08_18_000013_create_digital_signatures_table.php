<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digital_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_instance_id')->constrained('workflow_instances')->cascadeOnDelete();
            $table->foreignId('task_id')->nullable()->constrained('tasks')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('signature_hash', 64); // SHA-256 cryptographic hash of approval payload + cert
            $table->text('signature_data')->nullable(); // Base64 signature image or canvas draw data
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('signed_at');
            $table->timestamps();

            $table->index(['workflow_instance_id', 'signature_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_signatures');
    }
};
