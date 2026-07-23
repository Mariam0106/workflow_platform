<?php

declare(strict_types=1);

namespace App\Services\Organisation;

use App\Contracts\Repositories\Organisation\DepartmentRepositoryInterface;
use App\Contracts\Repositories\Organisation\EntityRepositoryInterface;
use App\Contracts\Repositories\Organisation\UserRepositoryInterface;
use App\DataTransferObjects\Organisation\CreateUserData;
use App\DataTransferObjects\Organisation\UpdateUserData;
use App\Events\Organisation\UserCreated;
use App\Exceptions\Organisation\DepartmentNotFoundException;
use App\Exceptions\Organisation\EntityNotFoundException;
use App\Exceptions\Organisation\InvalidManagerAssignmentException;
use App\Exceptions\Organisation\UnauthorizedActionException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * ==========================================================================
 * UserService
 * ==========================================================================
 *
 * Every write path for a User goes through here - Controllers never call
 * UserRepository::createFromData()/updateFromData() directly. This is where
 * password hashing, authorization (via PermissionService), and hierarchy
 * integrity (no cyclical N+1 chains) are enforced in exactly one place.
 *
 * Business Rules covered
 * --------------------------------------------------------------------------
 * BR-03/04/05/06  Entity/Department/BusinessFunction/ApplicationRole are
 *                 mandatory and must reference active records (BR-09).
 * BR-07           Only active Users may log in (enforced at auth time,
 *                 not here - see LoginRequest::authenticate()).
 * ==========================================================================
 */
class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly DepartmentRepositoryInterface $departments,
        private readonly EntityRepositoryInterface $entities,
        private readonly PermissionService $permissions,
    ) {}

    /**
     * Self-registration (Jalon J1) - no permission check, anyone with a
     * valid company email may sign up. Kept separate from createByAdmin()
     * so the intent is unambiguous at the call site.
     */
    public function register(CreateUserData $dto): User
    {
        return $this->createUser($dto);
    }

    /**
     * Admin-initiated creation (Étape 13 - BackOffice user management).
     *
     * @throws UnauthorizedActionException
     */
    public function createByAdmin(CreateUserData $dto, User $actor): User
    {
        $this->permissions->ensureCanManageOrganisation($actor);

        return $this->createUser($dto);
    }

    private function createUser(CreateUserData $dto): User
    {
        $this->assertEntityAndDepartmentAreActive($dto->entityId, $dto->departmentId);

        if ($dto->managerId !== null) {
            $this->assertManagerAssignmentIsValid(userId: null, managerId: $dto->managerId);
        }

        // Hash centralisé ici : le Repository/DTO transportent le mot de
        // passe en clair jusque-là (voir CreateUserData). Le cast 'hashed'
        // du Model est idempotent, donc pas de double-hash même si ce
        // Service est un jour contourné.
        $user = $this->users->createFromData(
            new CreateUserData(
                entityId: $dto->entityId,
                departmentId: $dto->departmentId,
                businessFunctionId: $dto->businessFunctionId,
                applicationRoleId: $dto->applicationRoleId,
                managerId: $dto->managerId,
                firstName: $dto->firstName,
                lastName: $dto->lastName,
                email: $dto->email,
                phone: $dto->phone,
                password: Hash::make($dto->password),
                isActive: $dto->isActive,
                employeeNumber: $dto->employeeNumber,
                jobTitle: $dto->jobTitle,
            )
        );

        // Étape 9 : émis ici, jamais depuis le Repository (le Repository
        // ne connaît pas les événements métier) - couvre les deux
        // parcours (self-registration J1 et créationByAdmin, Étape 13),
        // puisque tous deux passent par createUser().
        UserCreated::dispatch($user);

        return $user;
    }

    /**
     * Self-service profile update - a User may always edit their own
     * basic info, regardless of role. Deliberately narrow : anything
     * touching entity/department/role/manager/is_active is rejected here,
     * even if present in $dto - use updateByAdmin() for that.
     *
     * @throws UnauthorizedActionException
     */
    public function updateOwnProfile(int $userId, UpdateUserData $dto, User $actor): User
    {
        if (! $this->permissions->canManageOwnProfile($actor, $userId)) {
            throw UnauthorizedActionException::requiresRole('SELF', $actor->id);
        }

        $safeDto = UpdateUserData::fromArray(array_intersect_key(
            $dto->toArray(),
            array_flip(['first_name', 'last_name', 'phone']),
        ));

        return $this->users->updateFromData($userId, $safeDto);
    }

    /**
     * Admin-initiated update - full access to every field, including
     * hierarchy changes (guarded against cycles).
     *
     * @throws UnauthorizedActionException
     * @throws InvalidManagerAssignmentException
     */
    public function updateByAdmin(int $userId, UpdateUserData $dto, User $actor): User
    {
        $this->permissions->ensureCanManageOrganisation($actor);

        if (array_key_exists('manager_id', $dto->toArray())) {
            $this->assertManagerAssignmentIsValid($userId, $dto->managerId);
        }

        if ($dto->departmentId !== null || $dto->entityId !== null) {
            $current = $this->users->findById($userId);
            $this->assertEntityAndDepartmentAreActive(
                $dto->entityId ?? $current->entity_id,
                $dto->departmentId ?? $current->department_id,
            );
        }

        return $this->users->updateFromData($userId, $dto);
    }

    /**
     * @throws UnauthorizedActionException
     */
    public function deactivate(int $userId, User $actor): User
    {
        $this->permissions->ensureCanManageOrganisation($actor);

        if ($actor->id === $userId) {
            throw UnauthorizedActionException::requiresRole('ANOTHER_ADMIN', $actor->id);
        }

        $user = $this->users->findById($userId);
        $user->is_active = false;

        return $this->users->save($user);
    }

    /**
     * @throws UnauthorizedActionException
     */
    public function activate(int $userId, User $actor): User
    {
        $this->permissions->ensureCanManageOrganisation($actor);

        $user = $this->users->findById($userId);
        $user->is_active = true;

        return $this->users->save($user);
    }

    /**
     * BR-09 : archived Departments/Entities cannot receive new Users.
     */
    private function assertEntityAndDepartmentAreActive(int $entityId, int $departmentId): void
    {
        $entity = $this->entities->findById($entityId);
        $department = $this->departments->findById($departmentId);

        // findById() already throws *NotFoundException if missing - here
        // we additionally check "active", which is a different failure
        // mode (exists but archived), so a dedicated, clearer message
        // matters for whoever reads the error.
        if (! $entity->isActive()) {
            throw EntityNotFoundException::archived($entityId);
        }

        if (! $department->isActive()) {
            throw DepartmentNotFoundException::archived($departmentId);
        }
    }

    /**
     * Prevents self-assignment and cyclical N+1 chains (A manages B
     * manages A). $userId is null for a brand-new User (can't create a
     * cycle with someone who doesn't exist yet, but self-assignment via
     * a race is still impossible since the id doesn't exist).
     *
     * @throws InvalidManagerAssignmentException
     */
    private function assertManagerAssignmentIsValid(?int $userId, ?int $managerId): void
    {
        if ($managerId === null) {
            return;
        }

        if ($userId !== null && $managerId === $userId) {
            throw InvalidManagerAssignmentException::selfAssignment($userId);
        }

        if ($userId === null) {
            return;
        }

        // Walk up the proposed manager's own chain - if $userId shows up,
        // assigning $managerId to $userId would close a loop.
        $current = $this->users->findById($managerId);
        $visited = [];

        while ($current->manager_id !== null) {
            if ($current->manager_id === $userId || in_array($current->manager_id, $visited, true)) {
                throw InvalidManagerAssignmentException::wouldCreateCycle($userId, $managerId);
            }

            $visited[] = $current->manager_id;
            $current = $this->users->findById($current->manager_id);
        }
    }
}
