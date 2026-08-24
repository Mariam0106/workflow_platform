<?php

declare(strict_types=1);

namespace App\Events\Workflow;

use App\Models\Request;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * ==========================================================================
 * WorkflowFinished
 * ==========================================================================
 *
 * Leve par WorkflowEngineService::recordValidation() quand une Request
 * atteint une Step de fin (is_end) suite a une approbation - le
 * Workflow est acheve avec succes (distinct d'un rejet, voir
 * RequestRejected).
 */
class WorkflowFinished
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Request $request,
    ) {
    }
}
