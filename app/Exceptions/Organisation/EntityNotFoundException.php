<?php

declare(strict_types=1);

namespace App\Exceptions\Organisation;

/**
 * Thrown when a business Entity is looked up and does not exist, or has
 * no manager configured (needed for hierarchical validation routing).
 */
class EntityNotFoundException extends OrganisationException
{
    public static function withId(int $id): self
    {
        return new self(
            message: "Entité [{$id}] introuvable.",
            errorCode: 'entity_not_found',
            context: ['entity_id' => $id],
        );
    }

    public static function managerNotConfigured(int $entityId): self
    {
        return new self(
            message: "L'entité [{$entityId}] n'a pas de responsable configuré.",
            errorCode: 'entity_manager_not_configured',
            context: ['entity_id' => $entityId],
            httpStatus: 422,
        );
    }

    /**
     * BR-09 : archived Entities cannot receive new Users.
     */
    public static function archived(int $entityId): self
    {
        return new self(
            message: "L'entité [{$entityId}] est archivée et ne peut pas recevoir de nouveaux utilisateurs.",
            errorCode: 'entity_archived',
            context: ['entity_id' => $entityId],
            httpStatus: 422,
        );
    }

    protected function defaultHttpStatus(): int
    {
        return 404;
    }
}
