<?php

declare(strict_types=1);

namespace App\Contracts\Repositories\Organisation;

use App\Exceptions\Organisation\EntityNotFoundException;
use App\Models\Entity;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * ==========================================================================
 * EntityRepositoryInterface
 * ==========================================================================
 *
 * Defines the contract for accessing and persisting Entity objects.
 *
 * Business Rules covered
 * --------------------------------------------------------------------------
 * BR-01  Each Entity represents one company or business unit.
 * BR-02  Every Department belongs to exactly one Entity.
 * BR-03  Every User belongs to exactly one Entity.
 * BR-09  Archived Entities cannot receive new Users.
 *
 * Jalon J2 methods (required by Lali for Workflow Engine)
 * --------------------------------------------------------------------------
 * findManager()  — returns the top-level manager of an Entity
 *
 * @see \App\Repositories\Eloquent\Organisation\EntityRepository
 * ==========================================================================
 */
interface EntityRepositoryInterface
{
    /**
     * Find an Entity by its primary key.
     *
     * @throws EntityNotFoundException
     */
    public function findById(int $id): Entity;

    /**
     * Find an Entity by its unique code.
     */
    public function findByCode(string $code): ?Entity;

    /**
     * Return all active Entities.
     *
     * @return Collection<int, Entity>
     */
    public function findActive(): Collection;

    /**
     * Return all Entities (including archived).
     *
     * @return Collection<int, Entity>
     */
    public function findAll(): Collection;

    /**
     * ====================================================================
     * Jalon J2 — Entity Manager Resolution
     * ====================================================================
     *
     * Returns the top-level manager for the given Entity.
     *
     * The "Entity manager" is defined as the active User belonging to
     * this Entity who has no manager configured (i.e. sits at the top
     * of the N+1 hierarchy for this Entity). If no such User exists,
     * falls back to the active User with the highest Application Role
     * priority in this Entity.
     *
     * Required by Lali's Workflow Engine for:
     * - Resolving escalation targets (BR-46)
     * - Determining Entity-level approval routing
     *
     * @throws EntityNotFoundException         if the Entity does not exist
     * @throws EntityNotFoundException          if no manager can be determined
     *         (managerNotConfigured variant)
     */
    public function findManager(int $entityId): User;

    /**
     * Save (create or update) an Entity.
     */
    public function save(Entity $entity): Entity;

    /**
     * Delete an Entity.
     */
    public function delete(Entity $entity): bool;
}
