<?php

declare(strict_types=1);

namespace App\Exceptions\Workflow;

use App\Models\User;
use App\Models\WorkflowStep;

/**
 * Thrown when a validation decision cannot be recorded as requested -
 * either the acting User is not the expected Validator (BR-36), or the
 * decision itself is structurally incomplete (BR-40).
 */
class ValidationNotAllowedException extends WorkflowEngineException
{
    /**
     * BR-36 : Only the assigned Validator may validate.
     */
    public static function unauthorizedValidator(User $user, WorkflowStep $step): self
    {
        return new self(
            message: "L'utilisateur \"{$user->email}\" n'est pas un validateur autorisé pour l'étape \"{$step->name}\".",
            errorCode: 'validation_unauthorized_validator',
            context: ['user_id' => $user->id, 'workflow_step_id' => $step->id],
            httpStatus: 403,
        );
    }

    /**
     * BR-40 : Reject comment is mandatory.
     */
    public static function missingRejectComment(): self
    {
        return new self(
            message: 'Un commentaire est obligatoire pour rejeter une demande.',
            errorCode: 'validation_reject_comment_required',
        );
    }

    protected function defaultHttpStatus(): int
    {
        return 422;
    }
}
