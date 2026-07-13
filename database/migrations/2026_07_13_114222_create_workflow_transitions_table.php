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
    Schema::create('workflow_transitions', function (Blueprint $table) {

        // Primary Key
        $table->id();

        // Workflow
        $table->foreignId('workflow_id')
              ->constrained('workflows')
              ->restrictOnDelete()
              ->cascadeOnUpdate();

        // Transition
        $table->foreignId('from_step_id')
              ->constrained('workflow_steps')
              ->restrictOnDelete()
              ->cascadeOnUpdate();

        $table->foreignId('to_step_id')
              ->constrained('workflow_steps')
              ->restrictOnDelete()
              ->cascadeOnUpdate();

        // Action
        $table->string('action_name',100);

        // Configuration
        $table->boolean('is_default')->default(false);

        // Audit
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_transitions');
    }
};
