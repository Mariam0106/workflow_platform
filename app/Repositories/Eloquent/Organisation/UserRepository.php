<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Organisation;

use App\Contracts\Repositories\Organisation\DepartmentRepositoryInterface;
use App\Contracts\Repositories\Organisation\EntityRepositoryInterface;
use App\Contracts\Repositories\Organisation\UserRepositoryInterface;
use App\DataTransferObjects\Organisation\CreateUserData;
use App\DataTransferObjects\Organisation\UpdateUserData;
use App\Enums\ApplicationRoleCode;
use App\Exceptions\Organisation\UserNotFoundException;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * ==========================================================================
 * UserRepository
 * ==========================================================================
 *
 * Eloquent-based implementation of UserRepositoryInterface.
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
 * Jalon J2 — Manager Resolution (required by Lali's Workflow Engine)
 * --------------------------------------------------------------------------
 * findManager()            — N+1 direct manager of a User
 * findDepartmentManager()  — manager of the User's Department
 * findEntityManager()      — manager of the User's Entity
 * findUsersWithRole()      — all active Users with a given Application Role
 *
 * @see \App\Contracts\Repositories\Organisation\UserRepositoryInterface
 * ==========================================================================
 */
class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly User $model,
        private readonly DepartmentRepositoryInterface $departmentRepository,
        private readonly EntityRepositoryInterface $entityRepository,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function findById(int $id): User
    {
        $user = $this->model->newQuery()->find($id);

        if ($user === null) {
            throw UserNotFoundException::withId($id);
        }

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->newQuery()->where('email', $email)->first();
    }

    /**
     * {@inheritDoc}
     */
    public function findActive(): Collection
    {
        return $this->model->newQuery()->active()->orderBy('last_name')->orderBy('first_name')->get();
    }

    /**
     * {@inheritDoc}
     */
    public function findAll(): Collection
    {
        return $this->model->newQuery()->orderBy('last_name')->orderBy('first_name')->get();
    }

    /**
     * {@inheritDoc}
     */
    public function findByDepartment(int $departmentId): Collection
    {
        return $this->model->newQuery()
            ->where('department_id', $departmentId)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function findByEntity(int $entityId): Collection
    {
        return $this->model->newQuery()
            ->where('entity_id', $entityId)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function findSubordinates(int $managerId): Collection
    {
        return $this->model->newQuery()
            ->where('manager_id', $managerId)
            ->where('is_active', true)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    }

    /**
     * {@inheritDoc}
     *
     * Returns the direct manager (N+1) of the given User.
     * The manager is the User referenced by the manager_id column.
     */
    public function findManager(int $userId): User
    {
        $user = $this->findById($userId);

        if ($user->manager_id === null) {
            throw UserNotFoundException::managerNotConfigured($userId);
        }

        $manager = $this->model->newQuery()->find($user->manager_id);

        if ($manager === null) {
            throw UserNotFoundException::managerNotConfigured($userId);
        }

        return $manager;
    }

    /**
     * {@inheritDoc}
     *
     * Delegates to DepartmentRepository::findManager() using the User's
     * department_id.
     */
    public function findDepartmentManager(int $userId): User
    {
        $user = $this->findById($userId);

        return $this->departmentRepository->findManager($user->department_id);
    }

    /**
     * {@inheritDoc}
     *
     * Delegates to EntityRepository::findManager() using the User's
     * entity_id.
     */
    public function findEntityManager(int $userId): User
    {
        $user = $this->findById($userId);

        return $this->entityRepository->findManager($user->entity_id);
    }

    /**
     * {@inheritDoc}
     *
     * Returns all active Users who have the specified Application Role.
     * Uses a subquery to match against the application_roles.code column
     * via the relationship.
     */
    public function findUsersWithRole(ApplicationRoleCode $role): Collection
    {
        return $this->model->newQuery()
            ->where('is_active', true)
            ->whereHas('applicationRole', function ($query) use ($role): void {
                $query->where('code', $role->value);
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    }

    /**
     * {@inheritDoc}
     *
     * Creates a new User from a CreateUserData.
     *
     * NOTE: the DTO carries the password in clear text on purpose - the
     * User model's `password` cast is `'hashed'`, which auto-hashes on
     * write (and is idempotent if it's already hashed), so this is safe
     * whether or not a Service hashes it first. Still, once UserService
     * exists (Étape 8), it should own that decision explicitly rather
     * than relying on the implicit cast.
     */
    public function createFromData(CreateUserData $dto): User
    {
        return $this->model->newQuery()->create($dto->toArray());
    }

    /**
     * {@inheritDoc}
     *
     * Updates an existing User from an UpdateUserData.
     *
     * Only the fields actually present in the DTO are applied
     * (see UpdateUserData::toArray() logic — it filters on keys
     * that were provided, not on null values).
     *
     * @throws UserNotFoundException
     */
    public function updateFromData(int $id, UpdateUserData $dto): User
    {
        $user = $this->findById($id);

        if ($dto->isEmpty()) {
            return $user;
        }

        $user->update($dto->toArray());

        return $user->fresh();
    }

    /**
     * {@inheritDoc}
     */
    public function save(User $user): User
    {
        $user->save();

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(User $user): bool
    {
        return $user->delete();
    }
}
