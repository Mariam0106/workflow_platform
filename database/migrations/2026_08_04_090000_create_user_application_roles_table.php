<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * BR-06 : "Un Utilisateur peut être associé à plusieurs Rôles
     * Applicatifs." — table pivot portant les Rôles AUTORISÉS pour un
     * Utilisateur (Administrateur / Utilisateur / Validateur, plusieurs
     * possibles). Le Rôle ACTIF pour la session courante n'est PAS
     * stocké ici : c'est un état de session (voir
     * app/Http/Middleware/SetActiveApplicationRole.php), pas une donnée
     * métier persistante.
     *
     * Un ancien Utilisateur mono-rôle (avant cette migration) reste
     * valide : sa ligne pivot est créée par la migration suivante
     * (2026_08_04_090100_...) à partir de son ancien
     * `application_role_id`.
     */
    public function up(): void
    {
        Schema::create('user_application_roles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            $table->foreignId('application_role_id')
                  ->constrained('application_roles')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();

            $table->timestamps();

            // BR-06 : un Rôle n'est associé qu'une seule fois à un même
            // Utilisateur (pas de doublon "Validateur x2").
            $table->unique(['user_id', 'application_role_id'], 'user_app_role_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_application_roles');
    }
};
