<?php

declare(strict_types=1);

namespace App\Exceptions\Workflow;

use App\Models\User;
use App\Models\WorkflowStep;

/**
 * ==========================================================================
 * ValidationException
 * ==========================================================================
 *
 * NOTE : ne pas confondre avec Illuminate\Validation\ValidationException
 * (celle de Laravel, pour les erreurs de formulaire HTTP). Celle-ci
 * concerne exclusivement les regles de gestion de la Validation
 * metier (approbation/rejet d'une Request par un Validateur).
 * Toujours utiliser le nom complet (App\Exceptions\Workflow\Validation
 * Exception) dans les imports pour eviter toute confusion avec celle de
 * Laravel.
 * ==========================================================================
 */
class ValidationException extends WorkflowException
{
    /**
     * BR-36 : Only the assigned Validator may validate.
     */
    public static function unauthorizedValidator(User $user, WorkflowStep $step): self
    {
        return new self(
            "User \"{$user->email}\" is not an authorized validator for Step \"{$step->name}\".",
            'validation.unauthorized_validator',
            ['user_id' => $user->id, 'workflow_step_id' => $step->id],
            httpStatus: 403,
        );
    }

    /**
     * BR-40 : Reject comment is mandatory.
     */
    public static function missingRejectComment(): self
    {
        return new self(
            'A comment is required when rejecting a Request.',
            'validation.reject_comment_required',
            httpStatus: 422,
        );
    }
}
