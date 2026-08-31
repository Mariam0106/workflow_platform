<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Donne enfin a Department/Entity une vraie notion de "responsable",
 * jusqu'ici absente (voir NullOrganisationManagerResolver, qui
 * dégradait silencieusement les Étapes de type DEPARTMENT_MANAGER /
 * ENTITY_MANAGER en "aucun validateur trouve"). Nullable : un
 * Departement/une Entite peut exister sans responsable designe encore,
 * exactement comme users.manager_id l'est deja pour le sommet de la
 * hierarchie.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->foreignId('manager_id')
                ->nullable()
                ->after('entity_id')
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });

        Schema::table('entities', function (Blueprint $table) {
            $table->foreignId('manager_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('manager_id');
        });

        Schema::table('entities', function (Blueprint $table) {
            $table->dropConstrainedForeignId('manager_id');
        });
    }
};
