<?php

declare(strict_types=1);

namespace App\Services\Organisation;

use App\Contracts\Repositories\Organisation\EntityRepositoryInterface;
use App\DataTransferObjects\Organisation\EntityData;
use App\Exceptions\Organisation\UnauthorizedActionException;
use App\Models\Entity;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Support\Arr;

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
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @throws UnauthorizedActionException
     */
    public function create(EntityData $dto, User $actor): Entity
    {
        $this->permissions->ensureCanManageOrganisation($actor);

        $entity = new Entity($dto->toArray());
        $entity = $this->entities->save($entity);

        $this->auditLogger->log($actor->id, 'entity_created', 'Entity', $entity->id, newValues: $dto->toArray());

        return $entity;
    }

    /**
     * @throws UnauthorizedActionException
     */
    public function update(EntityData $dto, User $actor): Entity
    {
        $this->permissions->ensureCanManageOrganisation($actor);

        $entity = $this->entities->findById($dto->id);
        $oldValues = Arr::only($entity->getAttributes(), ['name', 'code', 'manager_id', 'description', 'is_active']);

        $entity->fill($dto->toArray());
        $entity = $this->entities->save($entity);

        $this->auditLogger->log($actor->id, 'entity_updated', 'Entity', $entity->id, $oldValues, $dto->toArray());

        return $entity;
    }

    /**
     * @throws UnauthorizedActionException
     */
    public function archive(int $entityId, User $actor): Entity
    {
        $this->permissions->ensureCanManageOrganisation($actor);

        $entity = $this->entities->findById($entityId);
        $entity->is_active = false;
        $entity = $this->entities->save($entity);

        $this->auditLogger->log($actor->id, 'entity_archived', 'Entity', $entity->id);

        return $entity;
    }

    /**
     * @throws UnauthorizedActionException
     */
    public function restore(int $entityId, User $actor): Entity
    {
        $this->permissions->ensureCanManageOrganisation($actor);

        $entity = $this->entities->findById($entityId);
        $entity->is_active = true;
        $entity = $this->entities->save($entity);

        $this->auditLogger->log($actor->id, 'entity_restored', 'Entity', $entity->id);

        return $entity;
    }
}
