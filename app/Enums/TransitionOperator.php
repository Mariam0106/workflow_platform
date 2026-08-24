<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * ==========================================================================
 * TransitionOperator Enum
 * ==========================================================================
 *
 * BR-21 : comparison operators the TransitionConditionEvaluator (Etape 9)
 * will support when comparing a RequestValue against
 * transition_conditions.expected_value.
 */
enum TransitionOperator: string
{
    case Equals = '=';
    case NotEquals = '!=';
    case GreaterThan = '>';
    case GreaterThanOrEqual = '>=';
    case LessThan = '<';
    case LessThanOrEqual = '<=';
    case Contains = 'contains';
}
