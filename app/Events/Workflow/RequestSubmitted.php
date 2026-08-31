<?php

declare(strict_types=1);

namespace App\Events\Workflow;

use App\Models\Request;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * ==========================================================================
 * RequestSubmitted
 * ==========================================================================
 *
 * Leve par WorkflowEngineService::submit() une fois la Request creee et
 * positionnee sur son Step de depart (BR-28/29/34/35). Les Listeners
 * (SendNotification, CreateAuditLog) reagissent a cet événement plutôt
 * que d'etre appeles en dur depuis le Service - le moteur reste
 * decouple de "qui doit etre notifie" / "comment auditer".
 */
class RequestSubmitted
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Request $request,
    ) {
    }
}
