<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * ==========================================================================
 * WorkflowPriority Enum
 * ==========================================================================
 *
 * Named constants for workflow_transitions.priority (BR-23).
 *
 * NOTE : priority is stored as a plain integer column (not enum-cast),
 * because an administrator must be able to set ANY intermediate value
 * (e.g. 42) to arbitrate between two transitions without a migration
 * (BR-56/57). These cases are convenience constants for code that
 * creates/compares priorities, not a strict Eloquent cast.
 */
enum WorkflowPriority: int
{
    case High = 100;
    case Medium = 50;
    case Low = 10;
}
