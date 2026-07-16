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
     * - "reference" renomme en "reference_number" pour matcher le
     *   Model et le vocabulaire exact de BR-29 ("unique Reference
     *   Number").
     * - Ajout de "workflow_version" (copie explicite de
     *   workflows.version au moment de la creation de la Request).
     *   Meme si workflow_id pointe deja vers une version precise
     *   (une ligne = une version), stocker le numero directement sur
     *   la Request satisfait BR-34 de maniere explicite et lisible
     *   sans jointure, et securise l'historique si jamais la
     *   structure de versioning des workflows evolue plus tard.
     * - "status" passe d'un ENUM MySQL a un varchar (coherent avec le
     *   reste : les valeurs seront contraintes par un Enum PHP a
     *   l'Etape 1, sans dependre d'une modification de schema pour
     *   ajouter un statut futur - BR-56/57).
     * - CORRECTION (Etape 1) : le defaut/valeurs initiales
     *   (DRAFT/SUBMITTED/IN_PROGRESS/APPROVED/REJECTED/CANCELLED, en
     *   majuscules) ne correspondaient pas a ce que le Model Request
     *   attendait deja (Draft/Submitted/Rejected/Completed, Title
     *   Case) - ses methodes isDraft()/isSubmitted()/isRejected()/
     *   isCompleted() ne matchaient donc jamais rien en pratique.
     *   Aligne sur le Model + App\Enums\RequestStatus.
     * - Ajout de softDeletes() : le Model Request utilise le trait
     *   SoftDeletes, la colonne deleted_at manquait.
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
            $table->string('reference_number', 50)->unique();
            $table->unsignedInteger('workflow_version');

            $table->string('status', 20)->default('Draft');

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();

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
        Schema::dropIfExists('requests');
    }
};
