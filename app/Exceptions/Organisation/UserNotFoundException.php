<?php

declare(strict_types=1);

namespace App\Exceptions\Organisation;

/**
 * Thrown when a User is looked up (by id, email, or as somebody's manager)
 * and does not exist / is not visible in the current context.
 */
class UserNotFoundException extends OrganisationException
{
    public static function withId(int $id): self
    {
        return new self(
            message: "User [{$id}] introuvable.",
            errorCode: 'user_not_found',
            status: 404,
            context: ['user_id' => $id],
        );
    }

    public static function withEmail(string $email): self
    {
        return new self(
            message: "Aucun utilisateur trouvé pour l'adresse [{$email}].",
            errorCode: 'user_not_found',
            status: 404,
            context: ['email' => $email],
        );
    }

    /**
     * BR-03/04/05/06 helpers rely on the manager relationship (N+1).
     * Distinct error code from withId(): here the User exists but has
     * no manager configured, which is a data/config problem, not a
     * plain "not found".
     */
    public static function managerNotConfigured(int $userId): self
    {
        return new self(
            message: "L'utilisateur [{$userId}] n'a pas de responsable hiérarchique (N+1) configuré.",
            errorCode: 'user_manager_not_configured',
            status: 422,
            context: ['user_id' => $userId],
        );
    }
}
