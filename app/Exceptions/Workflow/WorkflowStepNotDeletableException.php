<?php

declare(strict_types=1);

namespace App\Exceptions\Workflow;

use App\Models\WorkflowStep;

/**
 * BR-21 : les Transitions référencent une Étape par sa clé étrangère
 * (contrainte restrictOnDelete en base) - sans ce garde-fou applicatif,
 * tenter de supprimer une Étape encore utilisée par une Transition
 * remonterait une QueryException SQL brute au lieu d'un message métier
 * clair invitant à supprimer d'abord les Transitions concernées.
 */
class WorkflowStepNotDeletableException extends WorkflowEngineException
{
    public static function hasTransitions(WorkflowStep $step): self
    {
        return new self(
            message: "L'étape \"{$step->name}\" est encore reliée à au moins une transition - supprimez d'abord ces transitions.",
            errorCode: 'workflow_step_has_transitions',
            context: ['workflow_step_id' => $step->id],
        );
    }

    protected function defaultHttpStatus(): int
    {
        return 422;
    }
}
