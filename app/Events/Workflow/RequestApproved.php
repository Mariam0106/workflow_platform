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
 * RequestApproved
 * ==========================================================================
 *
 * Leve par WorkflowEngineService::recordValidation() a chaque decision
 * Approved (BR-41), que la Request avance simplement d'une Step ou
 * qu'elle atteigne son Step finale (dans ce dernier cas,
 * WorkflowFinished est leve EN PLUS de celui-ci, pas a la place - un
 * Listener interesse seulement par "approbation d'etape" n'a pas besoin
 * de savoir si c'etait la derniere).
 */
class RequestApproved
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
