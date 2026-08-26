<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Distingue explicitement le statut d'une INSCRIPTION (Pending tant que
 * l'Administrateur ne l'a pas traitée, Approved une fois activée,
 * Rejected si refusée) de is_active (qui reste "ce compte peut-il se
 * connecter EN CE MOMENT", et sert aussi à désactiver un compte déjà
 * approuvé plus tard - un usage différent). Toujours "Approved" par
 * défaut : un Utilisateur créé directement par un Administrateur depuis
 * le BackOffice n'est jamais concerné par ce circuit, seule
 * l'auto-inscription publique (register()) démarre en "Pending".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('registration_status', 20)->default('Approved')->after('is_active');
            $table->timestamp('approved_at')->nullable()->after('registration_status');
            $table->foreignId('approved_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            $table->text('rejected_reason')->nullable()->after('approved_by');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn(['registration_status', 'approved_at', 'rejected_reason']);
        });
    }
};
