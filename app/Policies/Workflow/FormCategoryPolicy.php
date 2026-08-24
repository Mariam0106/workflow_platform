<?php

declare(strict_types=1);

namespace App\Policies\Workflow;

use App\Enums\ApplicationRoleCode;
use App\Models\FormCategory;
use App\Models\User;

class FormCategoryPolicy
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

    public function update(User $user, FormCategory $formCategory): bool
    {
        return false;
    }

    public function archive(User $user, FormCategory $formCategory): bool
    {
        return false;
    }

    public function restore(User $user, FormCategory $formCategory): bool
    {
        return false;
    }
}
