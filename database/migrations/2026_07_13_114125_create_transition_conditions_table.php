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
     * - field_name (chaine libre) remplace par form_field_id (FK vers
     *   form_fields) : garantit qu'une condition ne peut jamais
     *   pointer vers un champ de formulaire qui n'existe pas/plus
     *   (integrite referentielle). Avant, renommer ou supprimer un
     *   champ de formulaire pouvait laisser des conditions orphelines
     *   silencieuses.
     * - Ajout de logical_operator et is_active - presents dans le
     *   Model (TransitionCondition::usesAnd()/usesOr()) mais absents
     *   de cette migration.
     */
    public function up(): void
    {
        Schema::create('transition_conditions', function (Blueprint $table) {

            // Primary Key
            $table->id();

            // Parent Transition
            $table->foreignId('workflow_transition_id')
                  ->constrained('workflow_transitions')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            // Business Rule
            $table->foreignId('form_field_id')
                  ->constrained('form_fields')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();

            $table->string('operator', 30);
            $table->string('expected_value', 255)->nullable();

            // Logic
            $table->string('logical_operator', 5)->default('AND');
            $table->integer('execution_order')->default(1);
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
        Schema::dropIfExists('transition_conditions');
    }
};
