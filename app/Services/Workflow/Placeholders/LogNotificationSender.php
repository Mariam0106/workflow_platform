<?php

declare(strict_types=1);

namespace App\Services\Workflow\Placeholders;

use App\Contracts\Services\Workflow\NotificationSenderInterface;
use App\Enums\NotificationStatus;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

/**
 * ==========================================================================
 * LogNotificationSender (implementation TEMPORAIRE)
 * ==========================================================================
 *
 * A REMPLACER a l'Etape 12 ("Notifications, Emails, Jobs, Listeners" du
 * roadmap partage) par une vraie implementation qui envoie reellement
 * un email (Laravel Mail/Notification) ou pousse une notification
 * In-App. En attendant, celle-ci degrade proprement : elle journalise
 * l'intention d'envoi et marque la Notification comme envoyee, pour que
 * le reste du pipeline (Listeners, tests) soit deja testable de bout en
 * bout sans dependre de l'infrastructure d'envoi reelle.
 *
 * Meme logique que NullOrganisationManagerResolver (Etape 9) : un
 * binding temporaire, explicite, facile a retrouver et remplacer plus
 * tard (chercher "LogNotificationSender" dans le code).
 * ==========================================================================
 */
class LogNotificationSender implements NotificationSenderInterface
{
    public function send(Notification $notification): void
    {
        Log::info('Notification (placeholder, pas de veritable envoi encore branche - Etape 12)', [
            'notification_id' => $notification->id,
            'channel' => $notification->channel->value,
            'recipient_id' => $notification->recipient_id,
            'title' => $notification->title,
        ]);

        $notification->update([
            'status' => NotificationStatus::Sent,
            'sent_at' => now(),
            'attempt_count' => $notification->attempt_count + 1,
            'last_attempt_at' => now(),
        ]);
    }
}
