<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Un Workflow peut désigner d'autres destinataires (Fonction Métier ou
 * Utilisateur) à notifier une fois la Demande entièrement approuvée -
 * en plus du Demandeur, déjà notifié par défaut (voir
 * SendNotification::onWorkflowFinished()). Cas réel : la clôture
 * "Ouverture de compte" doit prévenir Crédit Client (qui va réellement
 * créer le compte) même s'il n'est plus le Validateur d'aucune Étape.
 *
 * "notify_reference" reprend volontairement le même principe que
 * workflow_steps.validator_reference : une simple colonne entière dont
 * le sens dépend de notify_type, pas de clé étrangère typée - un
 * Administrateur doit pouvoir configurer ceci entièrement depuis
 * l'interface, sans jamais nécessiter de migration pour un nouveau cas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_completion_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained('workflows')->cascadeOnDelete();
            $table->string('notify_type', 30); // 'BUSINESS_FUNCTION' | 'USER'
            $table->unsignedBigInteger('notify_reference');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_completion_notifications');
    }
};
