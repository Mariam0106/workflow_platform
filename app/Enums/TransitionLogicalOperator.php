<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * ==========================================================================
 * TransitionLogicalOperator Enum
 * ==========================================================================
 *
 * How a transition_conditions row combines with the NEXT one (by
 * execution_order) when the Workflow Engine (Etape 9) evaluates a
 * Transition - BR-21/22/23.
 */
enum TransitionLogicalOperator: string
{
    case And = 'AND';
    case Or = 'OR';
}
