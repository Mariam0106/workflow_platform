<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * BR-06 (ajout multi-rôle) :
     *
     * `users.application_role_id` devient `users.default_application_role_id`.
     * Il ne représente plus "LE" Rôle de l'Utilisateur (un Utilisateur peut
     * désormais en avoir plusieurs, voir user_application_roles) mais :
     *
     *   1. le Rôle proposé par défaut au premier login (avant que
     *      l'Utilisateur n'ait choisi un Rôle actif pour sa session,
     *      voir SetActiveApplicationRole) ;
     *   2. la clé de tri "priorité" existante (OrdersByRolePriority) ;
     *   3. un filet de sécurité : chaque Utilisateur DOIT continuer à
     *      avoir une valeur ici même si sa ligne pivot est un jour vidée
     *      par erreur (elle reste NOT NULL, contrairement au pivot qui
     *      pourrait légalement être vide le temps d'une réaffectation).
     *
     * Le renommage est fait via une nouvelle migration plutôt qu'en
     * modifiant la migration d'origine (déjà potentiellement appliquée
     * en environnement de dev partagé) — voir convention Laravel.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('application_role_id', 'default_application_role_id');
        });

        // Backfill : chaque Utilisateur existant obtient une ligne pivot
        // pour son ancien (unique) Rôle, afin que BR-06 ("au moins un
        // Rôle Applicatif autorisé") reste vraie sans interruption.
        $now = now();

        DB::table('users')
            ->select('id', 'default_application_role_id')
            ->orderBy('id')
            ->chunkById(500, function ($users) use ($now) {
                $rows = $users->map(fn ($user) => [
                    'user_id' => $user->id,
                    'application_role_id' => $user->default_application_role_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();

                if ($rows !== []) {
                    DB::table('user_application_roles')->insertOrIgnore($rows);
                }
            });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('default_application_role_id', 'application_role_id');
        });
    }
};
