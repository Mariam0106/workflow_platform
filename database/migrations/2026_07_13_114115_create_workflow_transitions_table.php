<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * CORRECTION (Etape 0) :
     * Ajout de priority, description et is_active - presents dans le
     * Model (WorkflowTransition) mais absents de cette migration.
     * "priority" est indispensable pour BR-23 (arbitrage entre
     * plusieurs transitions dont les conditions sont vraies).
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
            $table->string('action_name', 100);
            $table->text('description')->nullable();

            // Configuration
            $table->unsignedInteger('priority')->default(50);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);

            // Audit
            $table->timestamps();
            $table->softDeletes();
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
