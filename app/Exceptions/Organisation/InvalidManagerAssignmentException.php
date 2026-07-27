<?php

declare(strict_types=1);

namespace App\Exceptions\Organisation;

/**
 * Thrown when assigning a manager to a User would break the hierarchy :
 * either the User would become their own manager, or a cycle would be
 * created (e.g. A -> B -> A), which would make N+1 resolution
 * (Jalon J2, UserRepository::findManager()) infinite-loop or return
 * nonsense - critical since Lali's Workflow Engine depends on this chain
 * being a clean tree.
 */
class InvalidManagerAssignmentException extends OrganisationException
{
    public static function selfAssignment(int $userId): self
    {
        return new self(
            message: "Un utilisateur ne peut pas être son propre responsable hiérarchique.",
            errorCode: 'invalid_manager_self_assignment',
            context: ['user_id' => $userId],
        );
    }

    public static function wouldCreateCycle(int $userId, int $managerId): self
    {
        return new self(
            message: "Cette affectation créerait une boucle dans la hiérarchie N+1.",
            errorCode: 'invalid_manager_cycle',
            context: ['user_id' => $userId, 'manager_id' => $managerId],
        );
    }

    protected function defaultHttpStatus(): int
    {
        return 422;
    }
}
