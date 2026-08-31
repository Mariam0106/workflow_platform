<?php

declare(strict_types=1);

namespace App\Notifications\Workflow;

use App\Models\Notification as NotificationModel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * ==========================================================================
 * WorkflowNotificationMail
 * ==========================================================================
 *
 * NOTE : ne pas confondre avec App\Models\Notification (l'enregistrement
 * en base, BR-43 a BR-47). Celle-ci est une Notification LARAVEL
 * (Illuminate\Notifications\Notification) - le mecanisme d'envoi
 * effectif (email) - toujours utiliser le nom complet dans les imports.
 *
 * Volontairement générique : le titre/message sont deja composes par
 * SendNotification au moment de creer l'enregistrement
 * App\Models\Notification - cette classe se contente de les mettre en
 * forme pour un email, sans connaitre le detail metier (BR-21 a BR-41)
 * qui a motive l'envoi.
 * ==========================================================================
 */
class WorkflowNotificationMail extends Notification
{
    use Queueable;

    public function __construct(
        private readonly NotificationModel $notification,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject($this->notification->title)
            ->greeting($this->notification->title)
            ->line($this->notification->message)
            ->action('Voir la demande', url('/workflow/requests/'.$this->notification->request_id));
    }
}
