<?php

declare(strict_types=1);

namespace App\Contracts\Repositories\Organisation;

use App\Exceptions\Organisation\DepartmentNotFoundException;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * ==========================================================================
 * DepartmentRepositoryInterface
 * ==========================================================================
 *
 * Defines the contract for accessing and persisting Department objects.
 *
 * Business Rules covered
 * --------------------------------------------------------------------------
 * BR-02  Every Department belongs to exactly one Entity.
 * BR-04  Every User belongs to exactly one Department.
 * BR-09  Archived Departments cannot receive new Users.
 *
 * Jalon J2 methods (required by Lali for Workflow Engine)
 * --------------------------------------------------------------------------
 * findManager()  — returns the manager of a Department
 *
 * @see \App\Repositories\Eloquent\Organisation\DepartmentRepository
 * ==========================================================================
 */
interface DepartmentRepositoryInterface
{
    /**
     * Find a Department by its primary key.
     *
     * @throws DepartmentNotFoundException
     */
    public function findById(int $id): Department;

    /**
     * Find a Department by its unique code.
     */
    public function findByCode(string $code): ?Department;

    /**
     * Return all Departments belonging to a specific Entity.
     *
     * @return Collection<int, Department>
     */
    public function findByEntity(int $entityId): Collection;

    /**
     * Return all active Departments.
     *
     * @return Collection<int, Department>
     */
    public function findActive(): Collection;

    /**
     * Return all Departments (including archived).
     *
     * @return Collection<int, Department>
     */
    public function findAll(): Collection;

    /**
     * ====================================================================
     * Jalon J2 — Department Manager Resolution
     * ====================================================================
     *
     * Returns the manager (highest-ranking active User) for the given
     * Department. The "Department manager" is defined as the active User
     * belonging to this Department who sits at the top of the N+1
     * hierarchy within this Department (i.e. has no manager configured,
     * or has the highest Application Role).
     *
     * Required by Lali's Workflow Engine for:
     * - Resolving N+1 validation routing (ValidatorType::NPlus1)
     * - Determining Department-level approval routing
     *
     * @throws DepartmentNotFoundException if the Department does not exist
     * @throws DepartmentNotFoundException if no manager can be determined
     *         (managerNotConfigured variant)
     */
    public function findManager(int $departmentId): User;

    /**
     * Save (create or update) a Department.
     */
    public function save(Department $department): Department;

    /**
     * Delete a Department.
     */
    public function delete(Department $department): bool;
}
