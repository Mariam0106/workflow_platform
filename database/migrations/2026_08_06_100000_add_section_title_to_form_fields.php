<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Un Champ peut désormais porter un Titre de section (optionnel) -
 * affiché comme en-tête juste avant lui, à la fois côté BackOffice
 * (liste des Champs d'un Formulaire) et côté Demandeur (écran de
 * saisie). Volontairement porté par le Champ lui-même plutôt qu'une
 * table "sections" séparée : ça reste un simple regroupement visuel de
 * champs déjà existants et ordonnés (display_order), pas une nouvelle
 * entité métier avec son propre cycle de vie.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('form_fields', function (Blueprint $table) {
            $table->string('section_title', 150)->nullable()->after('label');
        });
    }

    public function down(): void
    {
        Schema::table('form_fields', function (Blueprint $table) {
            $table->dropColumn('section_title');
        });
    }
};
