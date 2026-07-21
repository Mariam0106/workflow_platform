<?php

declare(strict_types=1);

namespace App\Contracts\Repositories\Organisation;

use App\DataTransferObjects\Organisation\CreateUserDTO;
use App\DataTransferObjects\Organisation\UpdateUserDTO;
use App\Enums\ApplicationRoleCode;
use App\Exceptions\Organisation\UserNotFoundException;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * ==========================================================================
 * UserRepositoryInterface
 * ==========================================================================
 *
 * Defines the contract for accessing and persisting User objects.
 *
 * Business Rules covered
 * --------------------------------------------------------------------------
 * BR-03  Every User belongs to exactly one Entity.
 * BR-04  Every User belongs to exactly one Department.
 * BR-05  Every User has exactly one Business Function.
 * BR-06  Every User has exactly one Application Role.
 * BR-07  Only active Users may access the platform.
 * BR-08  Company email is mandatory.
 *
 * Jalon J2 methods (required by Lali for Workflow Engine)
 * --------------------------------------------------------------------------
 * findManager()              — N+1 direct manager of a User
 * findDepartmentManager()    — manager of the User's Department
 * findEntityManager()        — manager of the User's Entity
 * findUsersWithRole()        — all Users with a given Application Role
 *
 * @see \App\Repositories\Eloquent\Organisation\UserRepository
 * ==========================================================================
 */
interface UserRepositoryInterface
{
    /**
     * Find a User by their primary key.
     *
     * @throws UserNotFoundException
     */
    public function findById(int $id): User;

    /**
     * Find a User by their email address.
     */
    public function findByEmail(string $email): ?User;

    /**
     * Return all active Users.
     *
     * @return Collection<int, User>
     */
    public function findActive(): Collection;

    /**
     * Return all Users (including inactive).
     *
     * @return Collection<int, User>
     */
    public function findAll(): Collection;

    /**
     * Return all Users belonging to a specific Department.
     *
     * @return Collection<int, User>
     */
    public function findByDepartment(int $departmentId): Collection;

    /**
     * Return all Users belonging to a specific Entity.
     *
     * @return Collection<int, User>
     */
    public function findByEntity(int $entityId): Collection;

    /**
     * Return all Users who report to a given manager (direct subordinates).
     *
     * @return Collection<int, User>
     */
    public function findSubordinates(int $managerId): Collection;

    /**
     * ====================================================================
     * Jalon J2 — N+1 Direct Manager Resolution
     * ====================================================================
     *
     * Returns the direct manager (N+1) of the given User.
     *
     * @throws UserNotFoundException if the User does not exist
     * @throws UserNotFoundException if the User has no manager configured
     *         (managerNotConfigured variant)
     */
    public function findManager(int $userId): User;

    /**
     * ====================================================================
     * Jalon J2 — Department Manager Resolution
     * ====================================================================
     *
     * Returns the manager (highest-ranking active User) for the Department
     * that the given User belongs to.
     *
     * @throws UserNotFoundException if the User does not exist
     * @throws UserNotFoundException if no Department manager can be
     *         determined
     */
    public function findDepartmentManager(int $userId): User;

    /**
     * ====================================================================
     * Jalon J2 — Entity Manager Resolution
     * ====================================================================
     *
     * Returns the manager (highest-ranking active User) for the Entity
     * that the given User belongs to.
     *
     * @throws UserNotFoundException if the User does not exist
     * @throws UserNotFoundException if no Entity manager can be determined
     */
    public function findEntityManager(int $userId): User;

    /**
     * ====================================================================
     * Jalon J2 — Users by Application Role
     * ====================================================================
     *
     * Returns all active Users who have the specified Application Role.
     *
     * @return Collection<int, User>
     */
    public function findUsersWithRole(ApplicationRoleCode $role): Collection;

    /**
     * Create a new User from a CreateUserDTO.
     *
     * NOTE: the User model's `password` cast ('hashed') auto-hashes on
     * write, so the DTO may carry a clear-text password safely.
     */
    public function createFromDto(CreateUserDTO $dto): User;

    /**
     * Update an existing User from an UpdateUserDTO.
     *
     * Only the fields actually present in the DTO are applied
     * (see UpdateUserDTO::toArray() logic).
     *
     * @throws UserNotFoundException
     */
    public function updateFromDto(int $id, UpdateUserDTO $dto): User;

    /**
     * Save (create or update) a User.
     */
    public function save(User $user): User;

    /**
     * Delete a User (soft delete).
     */
    public function delete(User $user): bool;
}
