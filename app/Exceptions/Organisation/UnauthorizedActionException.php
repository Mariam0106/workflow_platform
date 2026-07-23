<?php

declare(strict_types=1);

namespace App\Exceptions\Organisation;

/**
 * Thrown by the Service layer when the acting User does not have the
 * required Application Role for the action being attempted (e.g. a
 * non-Administrator trying to create/deactivate another User).
 *
 * NOTE : this is a Service-layer, coarse-grained guard - it exists so
 * Services never silently perform an action they shouldn't. The
 * fine-grained, per-resource authorization (Étape 10 - Policies) will
 * sit in front of Controllers and may end up making some of these
 * checks redundant there, but Services must stay safe to call directly
 * (from Jobs, Artisan commands, tests...) without relying on a Policy
 * having run first.
 */
class UnauthorizedActionException extends OrganisationException
{
    public static function requiresRole(string $requiredRole, int $actingUserId): self
    {
        return new self(
            message: "Cette action nécessite le rôle [{$requiredRole}].",
            errorCode: 'unauthorized_action',
            context: ['required_role' => $requiredRole, 'acting_user_id' => $actingUserId],
        );
    }

    protected function defaultHttpStatus(): int
    {
        return 403;
    }
}
