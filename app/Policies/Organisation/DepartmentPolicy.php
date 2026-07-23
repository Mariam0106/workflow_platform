<?php

declare(strict_types=1);

namespace App\Policies\Organisation;

use App\Models\Department;
use App\Models\User;
use App\Services\Organisation\PermissionService;

/**
 * ==========================================================================
 * DepartmentPolicy
 * ==========================================================================
 *
 * Gates the BackOffice Department management screens (Étape 13/14,
 * cahier des charges "Gestion des entités et départements") -
 * Administrator-only, no self-service concept applies here (unlike
 * UserPolicy).
 * ==========================================================================
 */
class DepartmentPolicy
{
    public function __construct(
        private readonly PermissionService $permissions,
    ) {}

    /**
     * Every ability on this Policy is Administrator-only - handled
     * entirely by before(), the individual methods below just deny.
     */
    public function before(User $actor, string $ability): ?bool
    {
        return $this->permissions->isAdministrator($actor) ? true : null;
    }

    public function viewAny(User $actor): bool
    {
        return false;
    }

    public function view(User $actor, Department $department): bool
    {
        return false;
    }

    public function create(User $actor): bool
    {
        return false;
    }

    public function update(User $actor, Department $department): bool
    {
        return false;
    }

    /**
     * BR-09 : an archived Department cannot receive new Users - see
     * DepartmentService::archive()/restore().
     */
    public function archive(User $actor, Department $department): bool
    {
        return false;
    }

    public function restore(User $actor, Department $department): bool
    {
        return false;
    }
}
