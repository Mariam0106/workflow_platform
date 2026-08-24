<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * L'urgence était jusqu'ici une propriété du Formulaire (forms.priority,
 * réglée une fois par l'Administrateur, s'appliquant alors à TOUTES les
 * demandes créées depuis ce Formulaire, sans distinction). Ce n'est pas
 * ce qu'attend le métier : c'est LA DEMANDE elle-même qui est urgente
 * ou pas, au cas par cas, décidé par le Demandeur au moment de
 * l'envoyer - pas une caractéristique figée du Formulaire lui-même.
 * forms.priority n'est plus utilisée après ce changement (colonne
 * laissée en place, sans risque, plutôt que supprimée).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->string('priority', 20)->default('Normal')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn('priority');
        });
    }
};
