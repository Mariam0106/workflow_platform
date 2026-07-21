<?php

declare(strict_types=1);

namespace App\Contracts\Services\Workflow;

use App\Models\Notification;

/**
 * ==========================================================================
 * NotificationSenderInterface
 * ==========================================================================
 *
 * Abstraction du canal d'envoi reel (BR-44 : Email / In-App). Le jour ou
 * l'entreprise veut ajouter/remplacer un canal (Teams, Slack, SMS), on
 * cree une nouvelle implementation de cette interface et on change le
 * binding dans AppServiceProvider - aucun Service ni Controller appelant
 * n'a besoin d'etre modifie.
 * ==========================================================================
 */
interface NotificationSenderInterface
{
    /**
     * Tente d'envoyer la Notification sur son canal (Notification::
     * channel). Doit mettre a jour attempt_count/last_attempt_at et,
     * en cas d'echec, failure_reason (BR-47) - c'est cette
     * implementation qui decide comment reessayer, pas l'appelant.
     */
    public function send(Notification $notification): void;
}
