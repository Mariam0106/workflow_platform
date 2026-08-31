<?php

declare(strict_types=1);

namespace App\Listeners\Workflow;

use App\Events\Workflow\RequestApproved;
use App\Events\Workflow\RequestRejected;
use App\Events\Workflow\RequestSubmitted;
use App\Events\Workflow\WorkflowFinished;
use App\Models\AuditLog;

/**
 * ==========================================================================
 * CreateAuditLog (Listener)
 * ==========================================================================
 *
 * BR-48 : chaque action importante est journalisee.
 * BR-50 : User/Date/Heure/Action/Entity/Old/New.
 *
 * Volontairement specifique aux 4 événements Workflow ici (contrairement
 * a l'Observer Eloquent générique prevu plus tard pour les changements
 * de champ bruts) : ces 4 événements representent des ACTIONS METIER
 * (soumission, approbation, rejet, aboutissement), pas de simples
 * modifications de colonnes - la distinction merite des entrees d'audit
 * plus lisibles ("submitted" plutôt que "status changed from X to Y").
 * ==========================================================================
 */
class CreateAuditLog
{
    public function onRequestSubmitted(RequestSubmitted $event): void
    {
        AuditLog::create([
            'user_id' => $event->request->requester_id,
            'action' => 'request_submitted',
            'entity_type' => 'Request',
            'entity_id' => $event->request->id,
            'new_values' => ['status' => $event->request->status->value, 'current_step_id' => $event->request->current_step_id],
        ]);
    }

    public function onRequestApproved(RequestApproved $event): void
    {
        AuditLog::create([
            'user_id' => $event->validation->validator_id,
            'action' => 'request_approved',
            'entity_type' => 'Request',
            'entity_id' => $event->request->id,
            'new_values' => ['validation_id' => $event->validation->id, 'current_step_id' => $event->request->fresh()->current_step_id],
        ]);
    }

    public function onRequestRejected(RequestRejected $event): void
    {
        AuditLog::create([
            'user_id' => $event->validation->validator_id,
            'action' => 'request_rejected',
            'entity_type' => 'Request',
            'entity_id' => $event->request->id,
            'new_values' => ['validation_id' => $event->validation->id, 'comment' => $event->validation->comment],
        ]);
    }

    public function onWorkflowFinished(WorkflowFinished $event): void
    {
        AuditLog::create([
            'user_id' => $event->request->requester_id,
            'action' => 'workflow_finished',
            'entity_type' => 'Request',
            'entity_id' => $event->request->id,
            'new_values' => ['status' => $event->request->status->value],
        ]);
    }
}
