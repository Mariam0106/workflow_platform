<?php

declare(strict_types=1);

namespace App\Policies\Organisation;

use App\Models\Entity;
use App\Models\User;
use App\Services\Organisation\PermissionService;

/**
 * ==========================================================================
 * EntityPolicy
 * ==========================================================================
 *
 * Gates the BackOffice Entity management screens (Étape 13/14) -
 * Administrator-only.
 *
 * NOTE : no EntityService exists yet (Étape 8 only delivered UserService/
 * DepartmentService/PermissionService per le roadmap) - this Policy is
 * ready ahead of it, ready to gate EntityService/EntityController once
 * they're built.
 * ==========================================================================
 */
class EntityPolicy
{
    public function __construct(
        private readonly PermissionService $permissions,
    ) {}

    public function before(User $actor, string $ability): ?bool
    {
        return $this->permissions->isAdministrator($actor) ? true : null;
    }

    public function viewAny(User $actor): bool
    {
        return false;
    }

    public function view(User $actor, Entity $entity): bool
    {
        return false;
    }

    public function create(User $actor): bool
    {
        return false;
    }

    public function update(User $actor, Entity $entity): bool
    {
        return false;
    }

    public function archive(User $actor, Entity $entity): bool
    {
        return false;
    }

    public function restore(User $actor, Entity $entity): bool
    {
        return false;
    }
}
