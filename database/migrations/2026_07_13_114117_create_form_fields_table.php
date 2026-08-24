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
     * Cette migration ne correspondait plus au Model FormField (qui
     * attend deja technical_name / placeholder / options / is_active
     * et utilise SoftDeletes). Realignee sur le Model :
     * - field_name -> technical_name (BR-14 : unique par formulaire)
     * - Ajout de placeholder, options (JSON, utile pour les champs de
     *   type "select"), is_active (permet de desactiver un champ sans
     *   le supprimer - important car BR-54 interdit de supprimer une
     *   configuration deja referencee par des donnees historiques).
     * - Ajout de deleted_at (soft delete), coherent avec le trait
     *   SoftDeletes deja utilise par le Model.
     * - is_readonly retire (non utilise par le Model actuellement ;
     *   pourra revenir plus tard si besoin, sans redesign majeur).
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
            $table->string('label', 150);
            $table->string('technical_name', 100);
            $table->string('field_type', 50);

            // Configuration
            $table->boolean('is_required')->default(false);
            $table->integer('display_order')->default(1);

            // Presentation & Validation
            $table->string('placeholder')->nullable();
            $table->string('default_value')->nullable();
            $table->text('validation_rules')->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            // Audit
            $table->timestamps();
            $table->softDeletes();

            // Constraints
            $table->unique(['form_id', 'technical_name']);
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
