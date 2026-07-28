<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * ==========================================================================
 * Migration : application_role_user (relation N-N User <-> ApplicationRole)
 * ==========================================================================
 *
 * AJOUT (demande client, post Étape 12) :
 * jusqu'ici un User possédait exactement un Application Role (BR-06,
 * colonne users.application_role_id). Le client souhaite maintenant
 * qu'un User puisse être *autorisé* pour plusieurs rôles applicatifs
 * (ex : "User" ET "Validator"), et choisisse son rôle actif après
 * authentification.
 *
 * Choix délibéré (non-destructif) :
 * - `users.application_role_id` est CONSERVÉE telle quelle : elle
 *   représente désormais le rôle ACTIF de la session courante, et
 *   continue d'alimenter, sans aucune modification, tout le code déjà
 *   écrit (Policies, ValidatorResolverService, PermissionService,
 *   User::hasRole(), OrdersByRolePriority, etc. - Étapes 5 à 12).
 * - Cette table pivot ajoute l'ensemble des rôles *autorisés* pour un
 *   User (dont son rôle actif fait toujours partie). C'est parmi cet
 *   ensemble que l'écran de sélection de rôle (post-login) proposera un
 *   choix.
 *
 * Une ligne unique par (user_id, application_role_id) - un User ne peut
 * pas être autorisé deux fois pour le même rôle.
 * ==========================================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_role_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            $table->foreignId('application_role_id')
                  ->constrained('application_roles')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            $table->timestamps();

            $table->unique(['user_id', 'application_role_id']);
        });

        // Backfill : chaque User existant devient autorisé pour son
        // rôle actuel (users.application_role_id), afin que la nouvelle
        // relation reste cohérente avec les données déjà en base et que
        // $user->authorizedRoles() ne soit jamais vide pour un compte
        // existant.
        $now = now();

        DB::table('users')
            ->whereNotNull('application_role_id')
            ->select(['id', 'application_role_id'])
            ->orderBy('id')
            ->chunkById(500, function ($users) use ($now): void {
                $rows = $users->map(fn ($user) => [
                    'user_id' => $user->id,
                    'application_role_id' => $user->application_role_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();

                if ($rows !== []) {
                    DB::table('application_role_user')->insert($rows);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_role_user');
    }
};
