<?php

declare(strict_types=1);

namespace App\Listeners\Workflow;

use App\Enums\NotificationChannel;
use App\Enums\NotificationStatus;
use App\Events\Workflow\RequestApproved;
use App\Events\Workflow\RequestRejected;
use App\Events\Workflow\RequestSubmitted;
use App\Events\Workflow\WorkflowFinished;
use App\Jobs\Workflow\DeliverNotification;
use App\Models\Notification;

/**
 * ==========================================================================
 * QueueEmails (Listener)
 * ==========================================================================
 *
 * BR-44/47 : repere les Notifications de canal Email en attente pour la
 * Request concernee, et met en file d'attente une DeliverNotification
 * Job par Notification - le travail d'envoi effectif vit
 * dans la Job, pas ici (separation deliberee : ce Listener decide QUAND,
 * la Job sait COMMENT).
 *
 * Ce Listener lui-même n'implemente PLUS ShouldQueue: la
 * seule chose qu'il fait est une requete de lecture + dispatch(), assez
 * rapide pour rester synchrone - c'est l'envoi réel (appel reseau au
 * fournisseur mail), dans la Job, qui merite d'etre mis en file
 * d'attente.
 *
 * Ne suppose PAS que SendNotification s'est deja execute avant elle
 * (l'ordre d'execution des Listeners d'un même événement n'est pas
 * garanti) : elle re-interroge elle-même les Notifications Pending du
 * canal Email pour cette Request, plutôt que de recevoir une liste
 * toute prete.
 * ==========================================================================
 */
class QueueEmails
{
    public function onRequestSubmitted(RequestSubmitted $event): void
    {
        $this->dispatchPendingEmails($event->request->id);
    }

    public function onRequestApproved(RequestApproved $event): void
    {
        $this->dispatchPendingEmails($event->request->id);
    }

    public function onRequestRejected(RequestRejected $event): void
    {
        $this->dispatchPendingEmails($event->request->id);
    }

    public function onWorkflowFinished(WorkflowFinished $event): void
    {
        $this->dispatchPendingEmails($event->request->id);
    }

    private function dispatchPendingEmails(int $requestId): void
    {
        Notification::query()
            ->where('request_id', $requestId)
            ->where('channel', NotificationChannel::Email)
            ->where('status', NotificationStatus::Pending)
            ->get()
            ->each(fn (Notification $notification) => DeliverNotification::dispatch($notification->id));
    }
}
