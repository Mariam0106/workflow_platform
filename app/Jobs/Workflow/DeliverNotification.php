<?php

declare(strict_types=1);

namespace App\Jobs\Workflow;

use App\Contracts\Services\Workflow\NotificationSenderInterface;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * ==========================================================================
 * DeliverNotification (Job)
 * ==========================================================================
 *
 * Le travail effectif d'envoi vit ICI, pas dans le Listener QueueEmails
 * - separation deliberee: un Listener decide QUAND declencher
 * un envoi, un Job sait COMMENT le faire, avec ses propres tentatives/
 * delai de re-essai geres par la file d'attente Laravel elle-même
 * (indépendamment de BR-47/attempt_count, qui suit les tentatives
 * METIER au niveau de la Notification, pas les tentatives techniques de
 * livraison de la Job).
 * ==========================================================================
 */
class DeliverNotification implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Tentatives techniques de la Job elle-même (re-essai reseau/
     * transitoire) - distinct de Notification::attempt_count (BR-47),
     * qui compte les tentatives METIER geree par
     * MailNotificationSender::send().
     */
    public int $tries = 3;

    public function __construct(
        public readonly int $notificationId,
    ) {
    }

    public function handle(NotificationSenderInterface $sender): void
    {
        $notification = Notification::find($this->notificationId);

        // La Notification a pu etre supprimee entre l'événement et
        // l'execution de la Job (file d'attente asynchrone) - rien a
        // faire dans ce cas, ce n'est pas une erreur.
        if ($notification === null) {
            return;
        }

        $sender->send($notification);
    }
}
