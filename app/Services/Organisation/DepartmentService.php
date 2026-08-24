<?php

declare(strict_types=1);

namespace App\Services\Organisation;

use App\Contracts\Repositories\Organisation\DepartmentRepositoryInterface;
use App\Contracts\Repositories\Organisation\EntityRepositoryInterface;
use App\DataTransferObjects\Organisation\DepartmentData;
use App\Events\Organisation\DepartmentCreated;
use App\Exceptions\Organisation\UnauthorizedActionException;
use App\Models\Department;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Support\Arr;

/**
 * ==========================================================================
 * DepartmentService
 * ==========================================================================
 *
 * Every write path for a Department goes through here - admin-only
 * (Départements sont gérés depuis le Backoffice, cahier des charges
 * "Gestion des entités et départements").
 *
 * Business Rules covered
 * --------------------------------------------------------------------------
 * BR-02  Every Department belongs to exactly one Entity (must be active).
 * BR-09  Archived Departments cannot receive new Users (enforced in
 *        UserService, not duplicated here).
 * ==========================================================================
 */
class DepartmentService
{
    public function __construct(
        private readonly DepartmentRepositoryInterface $departments,
        private readonly EntityRepositoryInterface $entities,
        private readonly PermissionService $permissions,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @throws UnauthorizedActionException
     */
    public function create(DepartmentData $dto, User $actor): Department
    {
        $this->permissions->ensureCanManageOrganisation($actor);

        // BR-02 : the parent Entity must exist and be active - findById()
        // throws EntityNotFoundException on its own if missing.
        $this->entities->findById($dto->entityId);

        $department = new Department($dto->toArray());

        $department = $this->departments->save($department);

        // Étape 9 : émis ici (Service), jamais depuis le Repository.
        DepartmentCreated::dispatch($department);

        $this->auditLogger->log($actor->id, 'department_created', 'Department', $department->id, newValues: $dto->toArray());

        return $department;
    }

    /**
     * @throws UnauthorizedActionException
     */
    public function update(DepartmentData $dto, User $actor): Department
    {
        $this->permissions->ensureCanManageOrganisation($actor);

        $department = $this->departments->findById($dto->id);
        $this->entities->findById($dto->entityId);

        $oldValues = Arr::only($department->getAttributes(), ['name', 'code', 'entity_id', 'manager_id', 'description', 'is_active']);

        $department->fill($dto->toArray());
        $department = $this->departments->save($department);

        $this->auditLogger->log($actor->id, 'department_updated', 'Department', $department->id, $oldValues, $dto->toArray());

        return $department;
    }

    /**
     * Archive a Department - existing Users keep their assignment (no
     * cascade), but BR-09 blocks any *new* User from being assigned to
     * it going forward (enforced by UserService).
     *
     * @throws UnauthorizedActionException
     */
    public function archive(int $departmentId, User $actor): Department
    {
        $this->permissions->ensureCanManageOrganisation($actor);

        $department = $this->departments->findById($departmentId);
        $department->is_active = false;
        $department = $this->departments->save($department);

        $this->auditLogger->log($actor->id, 'department_archived', 'Department', $department->id);

        return $department;
    }

    /**
     * @throws UnauthorizedActionException
     */
    public function restore(int $departmentId, User $actor): Department
    {
        $this->permissions->ensureCanManageOrganisation($actor);

        $department = $this->departments->findById($departmentId);
        $department->is_active = true;
        $department = $this->departments->save($department);

        $this->auditLogger->log($actor->id, 'department_restored', 'Department', $department->id);

        return $department;
    }
}
