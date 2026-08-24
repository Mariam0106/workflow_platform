<?php

declare(strict_types=1);

namespace App\Exceptions\Workflow;

use App\Models\Workflow;
use App\Models\WorkflowStep;

/**
 * Thrown when the Workflow Engine cannot determine a single, valid
 * Transition to execute from a Step - BR-20/21/22/23.
 */
class InvalidTransitionException extends WorkflowEngineException
{
    /**
     * BR-22 : exactement une Transition doit etre executee. Si aucune
     * des Transitions sortantes n'a ses Conditions vraies ET qu'aucune
     * n'est marquee is_default, le moteur ne sait pas ou aller - c'est
     * une erreur de configuration du Workflow, pas une erreur
     * utilisateur.
     */
    public static function noEligibleTransition(WorkflowStep $step): self
    {
        return new self(
            message: "Aucune Transition éligible trouvée depuis l'étape \"{$step->name}\" (workflow_id={$step->workflow_id}). Configurez une Transition par défaut pour éviter une impasse.",
            errorCode: 'workflow_no_eligible_transition',
            context: ['workflow_step_id' => $step->id, 'workflow_id' => $step->workflow_id],
        );
    }

    /**
     * BR-22 : plus d'une Transition par defaut sur le meme Step est une
     * erreur de configuration (ambiguite sur laquelle executer).
     */
    public static function multipleDefaultTransitions(WorkflowStep $step): self
    {
        return new self(
            message: "L'étape \"{$step->name}\" a plus d'une Transition par défaut - une seule est autorisée.",
            errorCode: 'workflow_multiple_default_transitions',
            context: ['workflow_step_id' => $step->id],
        );
    }

    /**
     * BR-18 : un Workflow doit contenir au moins un Step avant de
     * pouvoir etre publie/utilise - sans Step de depart, aucune
     * Transition initiale n'est possible.
     */
    public static function workflowHasNoSteps(Workflow $workflow): self
    {
        return new self(
            message: "Le Workflow \"{$workflow->code}\" ne peut pas être utilisé : il ne contient aucune étape.",
            errorCode: 'workflow_no_steps',
            context: ['workflow_id' => $workflow->id],
        );
    }

    protected function defaultHttpStatus(): int
    {
        return 422;
    }
}
