<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Suivi de la dernière connexion réussie d'un Utilisateur - permet à
 * l'Administrateur de repérer d'un coup d'œil les comptes inactifs
 * (Utilisateur ou Validateur), sans devoir fouiller l'Historique
 * général pour chaque personne une par une. Complémentaire, pas
 * redondant, avec l'Historique (BR-69 à BR-73) : chaque connexion y
 * reste tracée individuellement (traçabilité complète), tandis que
 * cette colonne ne garde que LA DERNIÈRE, pour un accès rapide.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_login_at');
        });
    }
};
