<?php

declare(strict_types=1);

namespace App\Services\Organisation;

use App\Contracts\Repositories\Organisation\BusinessFunctionRepositoryInterface;
use App\DataTransferObjects\Organisation\BusinessFunctionData;
use App\Exceptions\Organisation\UnauthorizedActionException;
use App\Models\BusinessFunction;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Support\Arr;

/**
 * Mirrors EntityService - every write path for a Business Function goes
 * through here, admin-only.
 */
class BusinessFunctionService
{
    public function __construct(
        private readonly BusinessFunctionRepositoryInterface $businessFunctions,
        private readonly PermissionService $permissions,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @throws UnauthorizedActionException
     */
    public function create(BusinessFunctionData $dto, User $actor): BusinessFunction
    {
        $this->permissions->ensureCanManageOrganisation($actor);

        $businessFunction = new BusinessFunction($dto->toArray());
        $businessFunction = $this->businessFunctions->save($businessFunction);

        $this->auditLogger->log($actor->id, 'business_function_created', 'BusinessFunction', $businessFunction->id, newValues: $dto->toArray());

        return $businessFunction;
    }

    /**
     * @throws UnauthorizedActionException
     */
    public function update(BusinessFunctionData $dto, User $actor): BusinessFunction
    {
        $this->permissions->ensureCanManageOrganisation($actor);

        $businessFunction = $this->businessFunctions->findById($dto->id);
        $oldValues = Arr::only($businessFunction->getAttributes(), ['name', 'code', 'description', 'is_active']);

        $businessFunction->fill($dto->toArray());
        $businessFunction = $this->businessFunctions->save($businessFunction);

        $this->auditLogger->log($actor->id, 'business_function_updated', 'BusinessFunction', $businessFunction->id, $oldValues, $dto->toArray());

        return $businessFunction;
    }

    /**
     * @throws UnauthorizedActionException
     */
    public function archive(int $businessFunctionId, User $actor): BusinessFunction
    {
        $this->permissions->ensureCanManageOrganisation($actor);

        $businessFunction = $this->businessFunctions->findById($businessFunctionId);
        $businessFunction->is_active = false;
        $businessFunction = $this->businessFunctions->save($businessFunction);

        $this->auditLogger->log($actor->id, 'business_function_archived', 'BusinessFunction', $businessFunction->id);

        return $businessFunction;
    }

    /**
     * @throws UnauthorizedActionException
     */
    public function restore(int $businessFunctionId, User $actor): BusinessFunction
    {
        $this->permissions->ensureCanManageOrganisation($actor);

        $businessFunction = $this->businessFunctions->findById($businessFunctionId);
        $businessFunction->is_active = true;
        $businessFunction = $this->businessFunctions->save($businessFunction);

        $this->auditLogger->log($actor->id, 'business_function_restored', 'BusinessFunction', $businessFunction->id);

        return $businessFunction;
    }
}
