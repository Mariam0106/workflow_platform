<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Department;
use App\Models\Entity;
use App\Models\User;
use App\Policies\Organisation\DepartmentPolicy;
use App\Policies\Organisation\EntityPolicy;
use App\Policies\Organisation\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * ==========================================================================
 * OrganisationPolicyServiceProvider
 * ==========================================================================
 *
 * Registers the Organisation module's Policies. A dedicated provider
 * (same pattern as OrganisationRepositoryServiceProvider, Étape 7) rather
 * than adding to AppServiceProvider - keeps this a small, isolated diff
 * with zero risk of merge conflict with Lali's own PolicyServiceProvider
 * for the Workflow module.
 *
 * NOTE : Policies live under App\Policies\Organisation\* instead of the
 * flat App\Policies\* Laravel expects for auto-discovery, so they must be
 * registered explicitly here - Gate::policy() rather than relying on
 * the naming convention.
 * ==========================================================================
 */
class OrganisationPolicyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Department::class, DepartmentPolicy::class);
        Gate::policy(Entity::class, EntityPolicy::class);
    }
}
