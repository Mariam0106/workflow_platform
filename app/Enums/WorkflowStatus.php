<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * ==========================================================================
 * WorkflowStatus Enum
 * ==========================================================================
 *
 * Lifecycle of a Workflow (BR-15/16/24/25/26).
 *
 * Draft      -> being configured, not usable by any Form yet.
 * Published  -> usable by Forms ; cannot be modified directly (BR-26),
 *               a new version must be created instead.
 * Archived   -> retired ; existing Requests keep using it (BR-25),
 *               no new Request may start on it.
 */
enum WorkflowStatus: string
{
    case Draft = 'Draft';
    case Published = 'Published';
    case Archived = 'Archived';
}
