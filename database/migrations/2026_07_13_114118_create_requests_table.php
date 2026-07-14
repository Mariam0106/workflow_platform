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
    Schema::create('requests', function (Blueprint $table) {

        // Primary Key
        $table->id();

        // Relationships
        $table->foreignId('form_id')
              ->constrained('forms')
              ->restrictOnDelete()
              ->cascadeOnUpdate();

        $table->foreignId('workflow_id')
              ->constrained('workflows')
              ->restrictOnDelete()
              ->cascadeOnUpdate();

        $table->foreignId('requester_id')
              ->constrained('users')
              ->restrictOnDelete()
              ->cascadeOnUpdate();

        // Workflow State
        $table->foreignId('current_step_id')
              ->constrained('workflow_steps')
              ->restrictOnDelete()
              ->cascadeOnUpdate();

        // Business
        $table->string('reference',50)->unique();

        $table->enum('status',[
            'DRAFT',
            'SUBMITTED',
            'IN_PROGRESS',
            'APPROVED',
            'REJECTED',
            'CANCELLED'
        ])->default('DRAFT');

        $table->timestamp('submitted_at')->nullable();
        $table->timestamp('completed_at')->nullable();

        // Audit
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
