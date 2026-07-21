<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Organisation;

use App\Contracts\Repositories\Organisation\DepartmentRepositoryInterface;
use App\Exceptions\Organisation\DepartmentNotFoundException;
use App\Models\Department;
use App\Models\User;
use App\Repositories\Eloquent\Organisation\Concerns\OrdersByRolePriority;
use Illuminate\Database\Eloquent\Collection;

/**
 * ==========================================================================
 * DepartmentRepository
 * ==========================================================================
 *
 * Eloquent-based implementation of DepartmentRepositoryInterface.
 *
 * Business Rules covered
 * --------------------------------------------------------------------------
 * BR-02  Every Department belongs to exactly one Entity.
 * BR-04  Every User belongs to exactly one Department.
 * BR-09  Archived Departments cannot receive new Users.
 *
 * Jalon J2 — Department Manager Resolution
 * --------------------------------------------------------------------------
 * findManager() returns the highest-ranking active User for the given
 * Department (the User with no manager configured, or the highest
 * Application Role within that Department).
 *
 * @see \App\Contracts\Repositories\Organisation\DepartmentRepositoryInterface
 * ==========================================================================
 */
class DepartmentRepository implements DepartmentRepositoryInterface
{
    use OrdersByRolePriority;

    public function __construct(
        private readonly Department $model,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function findById(int $id): Department
    {
        $department = $this->model->newQuery()->find($id);

        if ($department === null) {
            throw DepartmentNotFoundException::withId($id);
        }

        return $department;
    }

    /**
     * {@inheritDoc}
     */
    public function findByCode(string $code): ?Department
    {
        return $this->model->newQuery()->where('code', $code)->first();
    }

    /**
     * {@inheritDoc}
     */
    public function findByEntity(int $entityId): Collection
    {
        return $this->model->newQuery()
            ->where('entity_id', $entityId)
            ->orderBy('name')
            ->get();
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
     * The "Department manager" is defined as the active User belonging to
     * this Department who has no manager configured (top of the N+1
     * hierarchy within this Department). Falls back to the active User
     * with the highest role priority if no hierarchy root exists.
     */
    public function findManager(int $departmentId): User
    {
        // First, ensure the Department exists
        $this->findById($departmentId);

        // Try to find the User with no manager in this Department,
        // ranked by real role priority - see OrdersByRolePriority (not
        // application_role_id, which is just insertion order, not a
        // real hierarchy).
        $manager = $this->orderByRolePriority(
            User::query()
                ->where('department_id', $departmentId)
                ->where('is_active', true)
                ->whereNull('manager_id')
        )->first();

        if ($manager === null) {
            // Fallback: active User with the highest role priority in this Department
            $manager = $this->orderByRolePriority(
                User::query()
                    ->where('department_id', $departmentId)
                    ->where('is_active', true)
            )->first();
        }

        if ($manager === null) {
            throw DepartmentNotFoundException::managerNotConfigured($departmentId);
        }

        return $manager;
    }

    /**
     * {@inheritDoc}
     */
    public function save(Department $department): Department
    {
        $department->save();

        return $department;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(Department $department): bool
    {
        return $department->delete();
    }
}
