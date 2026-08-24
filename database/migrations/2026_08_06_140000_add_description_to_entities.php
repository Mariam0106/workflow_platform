<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * "entities" n'a jamais eu de colonne "description", contrairement à
 * "departments" qui l'a - bug préexistant (le DTO/formulaire s'y
 * attendaient déjà, jamais déclenché avant qu'une vraie création
 * d'Entité ne passe par ce chemin). Ajoutée ici pour aligner les deux,
 * plutôt que de retirer le champ du formulaire.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entities', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('entities', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};