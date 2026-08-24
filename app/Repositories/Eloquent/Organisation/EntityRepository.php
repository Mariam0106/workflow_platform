<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Organisation;

use App\Contracts\Repositories\Organisation\EntityRepositoryInterface;
use App\Exceptions\Organisation\EntityNotFoundException;
use App\Models\Entity;
use App\Models\User;
use App\Repositories\Eloquent\Organisation\Concerns\OrdersByRolePriority;
use Illuminate\Database\Eloquent\Collection;

/**
 * ==========================================================================
 * EntityRepository
 * ==========================================================================
 *
 * Eloquent-based implementation of EntityRepositoryInterface.
 *
 * Business Rules covered
 * --------------------------------------------------------------------------
 * BR-01  Each Entity represents one company or business unit.
 * BR-02  Every Department belongs to exactly one Entity.
 * BR-03  Every User belongs to exactly one Entity.
 * BR-09  Archived Entities cannot receive new Users.
 *
 * @see \App\Contracts\Repositories\Organisation\EntityRepositoryInterface
 * ==========================================================================
 */
class EntityRepository implements EntityRepositoryInterface
{
    use OrdersByRolePriority;

    public function __construct(
        private readonly Entity $model,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function findById(int $id): Entity
    {
        $entity = $this->model->newQuery()->find($id);

        if ($entity === null) {
            throw EntityNotFoundException::withId($id);
        }

        return $entity;
    }

    /**
     * {@inheritDoc}
     */
    public function findByCode(string $code): ?Entity
    {
        return $this->model->newQuery()->where('code', $code)->first();
    }

    /**
     * {@inheritDoc}
     */
    public function findActive(): Collection
    {
        return $this->model->newQuery()->active()->orderBy('name')->get();
    }

    /**
     * {@inheritDoc}
     */
    public function findAll(): Collection
    {
        return $this->model->newQuery()->orderBy('name')->get();
    }

    /**
     * {@inheritDoc}
     *
     * The "Entity manager" is defined as the active User belonging to
     * this Entity who has no manager configured (i.e. sits at the top
     * of the N+1 hierarchy for this Entity). If no such User exists,
     * falls back to the active User with the highest Application Role
     * priority in this Entity.
     */
    public function findManager(int $entityId): User
    {
        // First, ensure the Entity exists
        $this->findById($entityId);

        // Try to find the User with no manager (top of hierarchy) in
        // this Entity, ranked by real role priority - see
        // OrdersByRolePriority (not application_role_id, which is
        // just insertion order, not a real hierarchy).
        $manager = $this->orderByRolePriority(
            User::query()
                ->where('entity_id', $entityId)
                ->where('is_active', true)
                ->whereNull('manager_id')
        )->first();

        if ($manager === null) {
            // Fallback: active User with the highest role priority in this Entity
            $manager = $this->orderByRolePriority(
                User::query()
                    ->where('entity_id', $entityId)
                    ->where('is_active', true)
            )->first();
        }

        if ($manager === null) {
            throw EntityNotFoundException::managerNotConfigured($entityId);
        }

        return $manager;
    }

    /**
     * {@inheritDoc}
     */
    public function save(Entity $entity): Entity
    {
        $entity->save();

        return $entity;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(Entity $entity): bool
    {
        return $entity->delete();
    }
}
