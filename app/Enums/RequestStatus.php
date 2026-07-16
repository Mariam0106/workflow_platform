<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * ==========================================================================
 * RequestStatus Enum
 * ==========================================================================
 *
 * Lifecycle of a Request (BR-30/31/32).
 *
 * Draft      -> editable at will (BR-30).
 * Submitted  -> read-only, currently moving through the Workflow (BR-31).
 * Rejected   -> terminal, immutable (BR-32) ; a new Request must be
 *               created if the requester wants another attempt.
 * Completed  -> terminal, fully approved (reached an is_end Step).
 */
enum RequestStatus: string
{
    case Draft = 'Draft';
    case Submitted = 'Submitted';
    case Rejected = 'Rejected';
    case Completed = 'Completed';

    public function isTerminal(): bool
    {
        return match ($this) {
            self::Rejected, self::Completed => true,
            default => false,
        };
    }
}
