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
     * Business Function n'est plus rattachee a un Department unique.
     * BR-05 donne "Commercial, Credit Client, DAF, DG" comme exemples :
     * ce sont des fonctions transverses, reutilisables dans toute
     * l'entreprise (toute entite, tout departement). Un FK department_id
     * obligatoire empechait cette reutilisation.
     */
    public function up(): void
    {
        Schema::create('business_functions', function (Blueprint $table) {

            // Primary Key
            $table->id();

            // Business Information
            $table->string('code', 20)->unique();
            $table->string('name', 150);
            $table->text('description')->nullable();

            // Status
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
        Schema::dropIfExists('business_functions');
    }
};
