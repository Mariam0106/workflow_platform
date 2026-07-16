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
     * "decision" passe d'un ENUM MySQL (PENDING/APPROVED/REJECTED/
     * RETURNED) a un varchar contraint uniquement par le Model
     * (Validation::APPROVED / Validation::REJECTED).
     * - RETURNED est retire : BR-33 dit explicitement que le retour
     *   pour correction n'est PAS supporte.
     * - PENDING est retire : une ligne Validation n'existe que lorsque
     *   la decision a ete prise (BR-37, "chaque validation est
     *   timestampee") - l'attente n'est pas un etat de la validation,
     *   c'est simplement l'absence de ligne.
     */
    public function up(): void
    {
        Schema::create('validations', function (Blueprint $table) {

            // Primary Key
            $table->id();

            // Relationships
            $table->foreignId('request_id')
                  ->constrained('requests')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            $table->foreignId('workflow_step_id')
                  ->constrained('workflow_steps')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();

            $table->foreignId('validator_id')
                  ->constrained('users')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();

            // Decision
            $table->string('decision', 20);
            $table->text('comment')->nullable();
            $table->timestamp('validated_at')->nullable();

            // Audit
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validations');
    }
};
