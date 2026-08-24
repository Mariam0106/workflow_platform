<?php

declare(strict_types=1);

namespace App\Services\Workflow;

use App\Contracts\Services\Workflow\NotificationSenderInterface;
use App\Enums\NotificationStatus;
use App\Models\Notification;
use App\Notifications\Workflow\WorkflowNotificationMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Throwable;

/**
 * ==========================================================================
 * MailNotificationSender
 * ==========================================================================
 *
 * Implementation REELLE de NotificationSenderInterface (Etape 7),
 * remplace le placeholder LogNotificationSender (Etape 9). BR-44 :
 * n'envoie reellement que le canal Email pour l'instant - le canal
 * In-App n'a pas de mecanisme de "push" externe (il est deja visible
 * des sa creation en base, via l'API - Etape 11), donc simplement
 * marque comme livre.
 *
 * BR-47 : respecte config('workflow.notification_retry_attempts')
 * (Etape 2) via Notification::hasExceededRetryLimit() - abandonne
 * proprement (log + statut Failed) plutot que de boucler indefiniment.
 * ==========================================================================
 */
class MailNotificationSender implements NotificationSenderInterface
{
    public function send(Notification $notification): void
    {
        if ($notification->hasExceededRetryLimit()) {
            Log::warning('Notification abandonnee : nombre maximal de tentatives atteint (BR-47).', [
                'notification_id' => $notification->id,
                'attempt_count' => $notification->attempt_count,
            ]);

            $notification->update([
                'status' => NotificationStatus::Failed,
                'failure_reason' => 'Nombre maximal de tentatives atteint.',
            ]);

            return;
        }

        try {
            if ($notification->isEmail()) {
                NotificationFacade::send($notification->recipient, new WorkflowNotificationMail($notification));
            }

            $notification->update([
                'status' => NotificationStatus::Sent,
                'sent_at' => now(),
                'attempt_count' => $notification->attempt_count + 1,
                'last_attempt_at' => now(),
            ]);
        } catch (Throwable $e) {
            Log::error('Echec d\'envoi de notification.', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
            ]);

            $notification->update([
                'status' => NotificationStatus::Failed,
                'failure_reason' => $e->getMessage(),
                'attempt_count' => $notification->attempt_count + 1,
                'last_attempt_at' => now(),
            ]);
        }
    }
}
