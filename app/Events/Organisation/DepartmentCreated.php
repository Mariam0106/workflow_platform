<?php

declare(strict_types=1);

namespace App\Events\Organisation;

use App\Models\Department;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * ==========================================================================
 * DepartmentCreated
 * ==========================================================================
 *
 * Fired once a Department has been successfully persisted - by
 * DepartmentService, never by the Repository directly.
 *
 * Consumers (Étape 9, Workflow side - Lali) : typically an audit log
 * entry. This class only carries data, it has no opinion on what
 * happens next.
 * ==========================================================================
 */
class DepartmentCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Department $department,
    ) {}
}
