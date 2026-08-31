<?php

declare(strict_types=1);

namespace App\Listeners\Workflow;

use App\Enums\NotificationChannel;
use App\Enums\NotificationStatus;
use App\Events\Workflow\RequestApproved;
use App\Events\Workflow\RequestRejected;
use App\Events\Workflow\RequestSubmitted;
use App\Events\Workflow\WorkflowFinished;
use App\Models\Notification;
use App\Models\User;
use App\Services\Workflow\ValidatorResolverService;
use Illuminate\Support\Collection;

/**
 * ==========================================================================
 * SendNotification (Listener)
 * ==========================================================================
 *
 * BR-43 : les Notifications sont generees automatiquement. Cree les
 * enregistrements Notification (statut Pending) - l'envoi réel (Email/
 * In-App) est delegue a QueueEmails / NotificationSenderInterface
 *, volontairement separe: cette classe decide QUI doit
 * etre notifie et POURQUOI, pas COMMENT le message part reellement.
 * ==========================================================================
 */
class SendNotification
{
    public function __construct(
        private readonly ValidatorResolverService $validatorResolver,
    ) {
    }

    /**
     * BR-36/59 : notifie le/les Validateur(s) du Step de depart.
     */
    public function onRequestSubmitted(RequestSubmitted $event): void
    {
        $request = $event->request;

        $this->notifyValidators(
            $request,
            'Nouvelle demande à valider',
            "La demande \"{$request->reference_number}\" attend votre validation."
        );
    }

    /**
     * Si la Request n'est pas terminee, notifie le/les Validateur(s) du
     * NOUVEAU Step courant (WorkflowFinished s'occupe du cas termine).
     */
    public function onRequestApproved(RequestApproved $event): void
    {
        $request = $event->request->fresh();

        if ($request->status->isTerminal()) {
            return;
        }

        $this->notifyValidators(
            $request,
            'Demande en attente de votre validation',
            "La demande \"{$request->reference_number}\" a été approuvée à l'étape précédente et attend maintenant votre validation."
        );
    }

    /**
     * BR-39 : notifie le demandeur du rejet.
     */
    public function onRequestRejected(RequestRejected $event): void
    {
        $request = $event->request;

        $this->notifyUser(
            $request->requester,
            $request,
            'Votre demande a été rejetée',
            "Votre demande \"{$request->reference_number}\" a été rejetée. Motif : {$event->validation->comment}"
        );
    }

    /**
     * Notifie le demandeur de l'aboutissement du Workflow, ainsi que
     * tout destinataire supplémentaire configuré sur le Workflow lui-
     * même (voir WorkflowCompletionNotification) - typiquement
     * l'exécutant réel de l'action décidée (ex. Crédit Client qui va
     * effectivement ouvrir le compte), même s'il n'est Validateur
     * d'aucune Étape et ne l'a donc jamais été notifié jusqu'ici.
     */
    public function onWorkflowFinished(WorkflowFinished $event): void
    {
        $request = $event->request;

        $this->notifyUser(
            $request->requester,
            $request,
            'Votre demande a été approuvée',
            "Votre demande \"{$request->reference_number}\" a été entièrement approuvée."
        );

        $alreadyNotified = [$request->requester_id];

        foreach ($request->workflow?->completionNotifications ?? [] as $completionNotification) {
            foreach ($completionNotification->resolveRecipients() as $recipient) {
                if (in_array($recipient->id, $alreadyNotified, true)) {
                    continue;
                }

                $this->notifyUser(
                    $recipient,
                    $request,
                    'Demande approuvée',
                    "La demande \"{$request->reference_number}\" ({$request->form?->name}) a été entièrement approuvée."
                );

                $alreadyNotified[] = $recipient->id;
            }
        }
    }

    private function notifyValidators(\App\Models\Request $request, string $title, string $message): void
    {
        $validators = $this->validatorResolver->resolve($request->currentStep, $request);

        foreach ($validators as $validator) {
            $this->notifyUser($validator, $request, $title, $message);
        }
    }

    private function notifyUser(User $user, \App\Models\Request $request, string $title, string $message): void
    {
        Notification::create([
            'request_id' => $request->id,
            'recipient_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'channel' => NotificationChannel::Email,
            'status' => NotificationStatus::Pending,
        ]);
    }
}
