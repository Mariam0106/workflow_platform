<?php

declare(strict_types=1);

namespace App\Services\Organisation;

use App\Contracts\Repositories\Organisation\EntityRepositoryInterface;
use App\DataTransferObjects\Organisation\EntityData;
use App\Exceptions\Organisation\UnauthorizedActionException;
use App\Models\Entity;
use App\Models\User;

/**
 * ==========================================================================
 * EntityService
 * ==========================================================================
 *
 * Every write path for an Entity goes through here - admin-only. Mirrors
 * DepartmentService, minus the parent-reference check (an Entity has no
 * parent - it IS the top of the hierarchy, BR-02).
 *
 * NOTE : added at Étape 11 to support EntityController - see EntityData
 * docblock for why it wasn't already present from Étape 6/8.
 * ==========================================================================
 */
class EntityService
{
    public function __construct(
        private readonly EntityRepositoryInterface $entities,
        private readonly PermissionService $permissions,
    ) {}

    /**
     * @throws UnauthorizedActionException
     */
    public function create(EntityData $dto, User $actor): Entity
    {
        $this->permissions->ensureCanManageOrganisation($actor);

        $entity = new Entity($dto->toArray());

        return $this->entities->save($entity);
    }

    /**
     * @throws UnauthorizedActionException
     */
    public function update(EntityData $dto, User $actor): Entity
    {
        $this->permissions->ensureCanManageOrganisation($actor);

        $entity = $this->entities->findById($dto->id);
        $entity->fill($dto->toArray());

        return $this->entities->save($entity);
    }

    /**
     * @throws UnauthorizedActionException
     */
    public function archive(int $entityId, User $actor): Entity
    {
        $this->permissions->ensureCanManageOrganisation($actor);

        $entity = $this->entities->findById($entityId);
        $entity->is_active = false;

        return $this->entities->save($entity);
    }

    /**
     * @throws UnauthorizedActionException
     */
    public function restore(int $entityId, User $actor): Entity
    {
        $this->permissions->ensureCanManageOrganisation($actor);

        $entity = $this->entities->findById($entityId);
        $entity->is_active = true;

        return $this->entities->save($entity);
    }
}
