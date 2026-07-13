<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('form_fields', function (Blueprint $table) {

        // Primary Key
        $table->id();

        // Parent Form
        $table->foreignId('form_id')
              ->constrained('forms')
              ->cascadeOnDelete()
              ->cascadeOnUpdate();

        // Business Information
        $table->string('label',150);
        $table->string('field_name',100);
        $table->string('field_type',50);

        // Configuration
        $table->boolean('is_required')->default(false);
        $table->boolean('is_readonly')->default(false);
        $table->integer('display_order')->default(1);

        // Validation
        $table->string('default_value')->nullable();
        $table->text('validation_rules')->nullable();

        // Audit
        $table->timestamps();

        // Constraints
        $table->unique(['form_id','field_name']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_fields');
    }
};
