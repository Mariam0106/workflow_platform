<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * ==========================================================================
 * FormStatus Enum
 * ==========================================================================
 *
 * Lifecycle of a Form (BR-15/16/17). Same semantics as WorkflowStatus.
 */
enum FormStatus: string
{
    case Draft = 'Draft';
    case Published = 'Published';
    case Archived = 'Archived';
}
