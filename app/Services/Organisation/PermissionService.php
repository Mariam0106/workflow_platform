<?php

declare(strict_types=1);

namespace App\Services\Organisation;

use App\Enums\ApplicationRoleCode;
use App\Exceptions\Organisation\UnauthorizedActionException;
use App\Models\User;

/**
 * ==========================================================================
 * PermissionService
 * ==========================================================================
 *
 * Coarse-grained role checks (BR-06 : a User has exactly one Application
 * Role among ADMIN / VALIDATOR / USER). Used by UserService/DepartmentService
 * to guard admin-only actions before Policies exist (Étape 10).
 *
 * This is NOT a replacement for Policies - it answers "is this role
 * allowed to even attempt this kind of action", not "is this specific
 * User allowed to act on this specific resource" (e.g. "can Validator X
 * approve Request Y" is a Workflow-side, resource-level question, out of
 * scope here).
 * ==========================================================================
 */
class PermissionService
{
    public function isAdministrator(User $user): bool
    {
        return $user->hasRole(ApplicationRoleCode::Administrator);
    }

    public function isValidator(User $user): bool
    {
        return $user->hasRole(ApplicationRoleCode::Validator);
    }

    public function isStandardUser(User $user): bool
    {
        return $user->hasRole(ApplicationRoleCode::User);
    }

    /**
     * Only Administrators manage the Organisation module (Users,
     * Departments, Entities) - see cahier des charges, profil USER ADMIN.
     */
    public function canManageOrganisation(User $user): bool
    {
        return $this->isAdministrator($user);
    }

    /**
     * @throws UnauthorizedActionException
     */
    public function ensureCanManageOrganisation(User $actor): void
    {
        if (! $this->canManageOrganisation($actor)) {
            throw UnauthorizedActionException::requiresRole(
                requiredRole: ApplicationRoleCode::Administrator->value,
                actingUserId: $actor->id,
            );
        }
    }

    /**
     * A User may always manage their own profile (limited fields - see
     * UserService::updateOwnProfile()), regardless of role.
     */
    public function canManageOwnProfile(User $actor, int $targetUserId): bool
    {
        return $actor->id === $targetUserId;
    }
}
