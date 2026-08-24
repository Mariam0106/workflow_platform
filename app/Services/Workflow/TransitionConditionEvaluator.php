<?php

declare(strict_types=1);

namespace App\Services\Workflow;

use App\Enums\TransitionLogicalOperator;
use App\Enums\TransitionOperator;
use App\Models\TransitionCondition;
use Illuminate\Support\Collection;

/**
 * ==========================================================================
 * TransitionConditionEvaluator
 * ==========================================================================
 *
 * Brique la plus basse du moteur de workflow (BR-21). Une seule
 * responsabilite : etant donne une Condition et les valeurs d'une
 * Request, dire si elle est vraie ou fausse. Aucune dependance a
 * Eloquent au-dela des Models deja charges - testable sans base de
 * donnees (mocks).
 * ==========================================================================
 */
class TransitionConditionEvaluator
{
    /**
     * Evalue une seule Condition.
     *
     * @param  Collection<int, \App\Models\RequestValue>  $requestValues
     */
    public function evaluate(TransitionCondition $condition, Collection $requestValues): bool
    {
        $requestValue = $requestValues->firstWhere('form_field_id', $condition->form_field_id);

        // Pas de valeur soumise pour ce champ => la condition ne peut
        // pas etre vraie (on ne devine jamais une valeur manquante).
        if ($requestValue === null) {
            return false;
        }

        $actual = $requestValue->value;
        $expected = $condition->expected_value;

        return match ($condition->operator) {
            TransitionOperator::Equals => $actual == $expected,
            TransitionOperator::NotEquals => $actual != $expected,
            TransitionOperator::GreaterThan => (float) $actual > (float) $expected,
            TransitionOperator::GreaterThanOrEqual => (float) $actual >= (float) $expected,
            TransitionOperator::LessThan => (float) $actual < (float) $expected,
            TransitionOperator::LessThanOrEqual => (float) $actual <= (float) $expected,
            TransitionOperator::Contains => str_contains((string) $actual, (string) $expected),
        };
    }

    /**
     * Evalue une liste ORDONNEE de Conditions (BR-21) et les combine
     * selon leur logical_operator respectif.
     *
     * Convention : le logical_operator porte par une Condition decrit
     * comment ELLE se combine avec le resultat accumule des Conditions
     * PRECEDENTES (pli gauche-a-droite, dans l'ordre d'execution_order).
     * La toute premiere Condition n'a pas de "precedent" - son propre
     * logical_operator est ignore pour elle.
     *
     * Une liste VIDE de Conditions est considere vraie par convention :
     * c'est ce qui permet a une Transition par defaut (sans Condition,
     * BR-22/23) de toujours s'appliquer quand rien d'autre ne matche.
     *
     * @param  Collection<int, TransitionCondition>  $conditions
     * @param  Collection<int, \App\Models\RequestValue>  $requestValues
     */
    public function evaluateAll(Collection $conditions, Collection $requestValues): bool
    {
        $ordered = $conditions->where('is_active', true)->sortBy('execution_order')->values();

        if ($ordered->isEmpty()) {
            return true;
        }

        $result = null;

        foreach ($ordered as $condition) {
            $value = $this->evaluate($condition, $requestValues);

            $result = match (true) {
                $result === null => $value,
                $condition->logical_operator === TransitionLogicalOperator::Or => $result || $value,
                default => $result && $value,
            };
        }

        return $result;
    }
}
