<?php

declare(strict_types=1);

namespace App\Policies\Organisation;

use App\Models\User;
use App\Services\Organisation\PermissionService;

/**
 * ==========================================================================
 * UserPolicy
 * ==========================================================================
 *
 * Gates the Controller layer (Étape 11) for User-related actions.
 *
 * Deliberately delegates role checks to PermissionService rather than
 * re-implementing them - a Policy answers "can $actor do X to $target",
 * PermissionService answers "does $actor have role Y". Keeping the role
 * logic in one place avoids UserService and UserPolicy silently drifting
 * apart on what "Administrator" means.
 *
 * NOTE : this Policy is intentionally softer than UserService's own
 * guards (e.g. UserService::deactivate() also forbids self-deactivation).
 * The Policy is the first, coarse gate a Controller checks before even
 * calling the Service; the Service enforces the fine business rules
 * regardless of whether a Policy ran first (defense in depth - Services
 * must stay safe to call directly, from Jobs/Artisan/tests).
 * ==========================================================================
 */
class UserPolicy
{
    public function __construct(
        private readonly PermissionService $permissions,
    ) {}

    /**
     * Administrators can do anything on this resource - short-circuits
     * every ability below. Everyone else falls through to the
     * per-ability rules (mainly: acting on one's own account).
     */
    public function before(User $actor, string $ability): ?bool
    {
        return $this->permissions->isAdministrator($actor) ? true : null;
    }

    /**
     * BackOffice user list (Étape 13/14) - Administrator only (via before()).
     */
    public function viewAny(User $actor): bool
    {
        return false;
    }

    /**
     * A User may always view their own profile.
     */
    public function view(User $actor, User $target): bool
    {
        return $actor->id === $target->id;
    }

    /**
     * Admin-initiated creation (Étape 13) - Administrator only (via before()).
     * Self-registration (Jalon J1) does not go through this Policy at all -
     * it's an unauthenticated, public flow.
     */
    public function create(User $actor): bool
    {
        return false;
    }

    /**
     * A User may always update their own profile - see
     * UserService::updateOwnProfile(), which further restricts *which*
     * fields a self-update may touch.
     */
    public function update(User $actor, User $target): bool
    {
        return $actor->id === $target->id;
    }

    /**
     * Deactivating/activating another User is Administrator-only, with
     * no self-service exception (see before() for the Admin bypass -
     * UserService::deactivate() additionally forbids an Admin from
     * deactivating themselves).
     */
    public function deactivate(User $actor, User $target): bool
    {
        return false;
    }

    public function activate(User $actor, User $target): bool
    {
        return false;
    }
}
