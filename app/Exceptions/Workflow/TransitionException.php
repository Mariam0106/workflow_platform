<?php

declare(strict_types=1);

namespace App\Exceptions\Workflow;

use App\Models\WorkflowStep;

/**
 * ==========================================================================
 * TransitionException
 * ==========================================================================
 *
 * Violations survenant pendant la SELECTION d'une Transition par le
 * moteur de workflow (Etape 9 - WorkflowTransitionSelector).
 * ==========================================================================
 */
class TransitionException extends WorkflowException
{
    /**
     * BR-22 : exactly one Transition must be executed. Si aucune des
     * Transitions sortantes n'a ses conditions vraies ET qu'aucune
     * n'est marquee is_default, le moteur ne sait pas ou aller -
     * c'est une erreur de configuration du Workflow, pas une erreur
     * utilisateur.
     */
    public static function noEligibleTransition(WorkflowStep $step): self
    {
        return new self(
            "No eligible Transition found from Step \"{$step->name}\" (workflow_id={$step->workflow_id}). Configure a default Transition to avoid dead ends.",
            'workflow.no_eligible_transition',
            ['workflow_step_id' => $step->id, 'workflow_id' => $step->workflow_id],
        );
    }

    /**
     * BR-22 : plus d'une Transition par defaut sur le meme Step est une
     * erreur de configuration (ambiguite sur laquelle executer).
     */
    public static function multipleDefaultTransitions(WorkflowStep $step): self
    {
        return new self(
            "Step \"{$step->name}\" has more than one default Transition - exactly one is allowed.",
            'workflow.multiple_default_transitions',
            ['workflow_step_id' => $step->id],
        );
    }
}
