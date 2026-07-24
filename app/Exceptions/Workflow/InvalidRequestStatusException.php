<?php

declare(strict_types=1);

namespace App\Exceptions\Workflow;

use App\Models\Request;

/**
 * Thrown when an action is attempted on a Request whose current status
 * does not allow it - BR-31 (read-only after submission), BR-32
 * (rejected Requests cannot be modified).
 */
class InvalidRequestStatusException extends WorkflowEngineException
{
    public static function notEditable(Request $request): self
    {
        return new self(
            message: "La demande \"{$request->reference_number}\" n'est pas modifiable (statut actuel : {$request->status->value}).",
            errorCode: 'request_not_editable',
            context: ['request_id' => $request->id, 'status' => $request->status->value],
            httpStatus: 403,
        );
    }

    protected function defaultHttpStatus(): int
    {
        return 403;
    }
}
