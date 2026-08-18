<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_definition_id')->constrained('workflow_definitions')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_template_id')->constrained('form_templates')->cascadeOnDelete();
            $table->string('field_name'); // e.g. amount, leave_type, reason
            $table->string('label');
            $table->string('field_type'); // text, number, dropdown, textarea, date, file, multiselect, checkbox
            $table->boolean('is_required')->default(false);
            $table->json('validation_rules')->nullable(); // e.g. ["numeric", "min:100", "max:50000"]
            $table->json('options')->nullable(); // for dropdown / multiselect
            $table->json('conditional_logic')->nullable(); // show/hide depending on other field values
            $table->integer('field_order')->default(1);
            $table->timestamps();

            $table->index(['form_template_id', 'field_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_fields');
        Schema::dropIfExists('form_templates');
    }
};
