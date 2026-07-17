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
     * - is_initial/is_final renommes en is_start/is_end pour matcher
     *   le Model existant (WorkflowStep::isStart()/isEnd()) et le MCD
     *   final valide.
     * - validator_type passe d'un ENUM MySQL a un varchar : la liste
     *   des types de validateur pourra evoluer sans nouvelle migration
     *   (BR-56/57/59) ; la contrainte de valeurs sera assuree par un
     *   Enum PHP (App\Enums\ValidatorType) a l'Etape 1.
     */
    public function up(): void
    {
        Schema::create('workflow_steps', function (Blueprint $table) {

            // Primary Key
            $table->id();

            // Parent Workflow
            $table->foreignId('workflow_id')
                  ->constrained('workflows')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();

            // Business Information
            $table->string('code', 30);
            $table->string('name', 150);
            $table->text('description')->nullable();

            // Step Configuration
            $table->integer('step_order');
            $table->boolean('is_start')->default(false);
            $table->boolean('is_end')->default(false);

            // Validator Configuration
            $table->string('validator_type', 30);
            $table->unsignedBigInteger('validator_reference')->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            // Audit
            $table->timestamps();
            $table->softDeletes();

            // Constraints
            $table->unique(['workflow_id', 'step_order']);
            $table->unique(['workflow_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_steps');
    }
};
