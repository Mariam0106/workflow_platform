<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * ==========================================================================
 * ValidationDecision Enum
 * ==========================================================================
 *
 * BR-38 : a Validator's decision is Approve or Reject. Only.
 * BR-33 : "Returned for correction" is explicitly NOT supported -
 *         there is intentionally no third case here.
 */
enum ValidationDecision: string
{
    case Approved = 'Approved';
    case Rejected = 'Rejected';
}
