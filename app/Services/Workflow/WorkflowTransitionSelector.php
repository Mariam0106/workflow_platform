<?php

declare(strict_types=1);

namespace App\Services\Workflow;

use App\Exceptions\Workflow\InvalidTransitionException;
use App\Models\WorkflowStep;
use App\Models\WorkflowTransition;
use Illuminate\Support\Collection;

/**
 * ==========================================================================
 * WorkflowTransitionSelector
 * ==========================================================================
 *
 * BR-20/21/22/23 : selectionne LA transition a executer parmi celles
 * qui sortent d'un Step.
 *
 * Principe : les transitions sont triees par priorite decroissante,
 * puis on retient la PREMIERE dont les Conditions (evaluees par
 * TransitionConditionEvaluator) sont vraies. Une Transition par defaut
 * (sans Condition) evalue toujours a "vrai" (cf. evaluateAll() sur une
 * liste vide) - il suffit donc de lui donner la priorite la plus basse
 * pour qu'elle ne serve que de filet de securite. Aucune branche
 * "if is_default" separee n'est necessaire : c'est la meme regle qui
 * s'applique a toutes les transitions.
 * ==========================================================================
 */
class WorkflowTransitionSelector
{
    public function __construct(
        private readonly TransitionConditionEvaluator $evaluator,
    ) {
    }

    /**
     * @param  Collection<int, \App\Models\RequestValue>  $requestValues
     *
     * @throws InvalidTransitionException si aucune Transition eligible n'est
     *                              trouvee (BR-22 - erreur de
     *                              configuration du Workflow).
     */
    public function select(WorkflowStep $step, Collection $requestValues): WorkflowTransition
    {
        $transitions = $step->outgoingTransitions()
            ->with('transitionConditions')
            ->where('is_active', true)
            ->orderByDesc('priority')
            ->get();

        foreach ($transitions as $transition) {
            if ($this->evaluator->evaluateAll($transition->transitionConditions, $requestValues)) {
                return $transition;
            }
        }

        throw InvalidTransitionException::noEligibleTransition($step);
    }
}
