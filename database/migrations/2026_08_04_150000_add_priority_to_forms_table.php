<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * BR-15/56 (ajout) : urgence métier d'un Formulaire - visible par
     * tous les Validateurs qui traitent des Demandes issues de ce
     * Formulaire, pour qu'ils sachent lesquelles traiter en premier.
     *
     * Volontairement distinct de `workflow_transitions.priority` (qui
     * sert uniquement au moteur pour arbitrer entre deux Transitions
     * possibles, BR-23 - jamais montré à un Validateur) : deux concepts
     * différents qui partagent juste le mot "priorité".
     */
    public function up(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->string('priority', 20)->default('Normal')->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn('priority');
        });
    }
};
