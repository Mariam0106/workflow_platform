<?php

declare(strict_types=1);

namespace App\Events\Workflow;

use App\Models\Request;
use App\Models\Validation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * ==========================================================================
 * RequestRejected
 * ==========================================================================
 *
 * Leve par WorkflowEngineService::recordValidation() sur une decision
 * Rejected (BR-39 : le Workflow se termine immediatement). Distinct de
 * WorkflowFinished : un rejet est une fin en echec, pas un
 * aboutissement - les Listeners (notification, audit) ont souvent un
 * comportement different pour les deux.
 */
class RequestRejected
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Request $request,
        public readonly Validation $validation,
    ) {
    }
}
