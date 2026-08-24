<?php

declare(strict_types=1);

namespace App\Policies\Organisation;

use App\Models\BusinessFunction;
use App\Models\User;
use App\Services\Organisation\PermissionService;

class BusinessFunctionPolicy
{
    public function __construct(
        private readonly PermissionService $permissions,
    ) {}

    public function before(User $actor, string $ability): ?bool
    {
        return $this->permissions->isAdministrator($actor) ? true : null;
    }

    public function viewAny(User $actor): bool
    {
        return false;
    }

    public function view(User $actor, BusinessFunction $businessFunction): bool
    {
        return false;
    }

    public function create(User $actor): bool
    {
        return false;
    }

    public function update(User $actor, BusinessFunction $businessFunction): bool
    {
        return false;
    }

    public function archive(User $actor, BusinessFunction $businessFunction): bool
    {
        return false;
    }

    public function restore(User $actor, BusinessFunction $businessFunction): bool
    {
        return false;
    }
}
