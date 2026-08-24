<?php

declare(strict_types=1);

namespace App\Policies\Workflow;

use App\Enums\ApplicationRoleCode;
use App\Models\User;
use App\Models\WorkflowCategory;

class WorkflowCategoryPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole(ApplicationRoleCode::Administrator) ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, WorkflowCategory $workflowCategory): bool
    {
        return false;
    }

    public function archive(User $user, WorkflowCategory $workflowCategory): bool
    {
        return false;
    }

    public function restore(User $user, WorkflowCategory $workflowCategory): bool
    {
        return false;
    }
}
