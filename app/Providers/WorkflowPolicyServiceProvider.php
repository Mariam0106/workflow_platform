<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\AuditLog;
use App\Models\Form;
use App\Models\FormCategory;
use App\Models\Request;
use App\Models\Validation;
use App\Models\Workflow;
use App\Models\WorkflowCategory;
use App\Policies\Workflow\AuditLogPolicy;
use App\Policies\Workflow\FormCategoryPolicy;
use App\Policies\Workflow\FormPolicy;
use App\Policies\Workflow\RequestPolicy;
use App\Policies\Workflow\ValidationPolicy;
use App\Policies\Workflow\WorkflowCategoryPolicy;
use App\Policies\Workflow\WorkflowPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * ==========================================================================
 * WorkflowPolicyServiceProvider
 * ==========================================================================
 *
 * Enregistre les Policies du domaine Workflow - exact miroir de
 * OrganisationPolicyServiceProvider (même raisonnement : Policies sous
 * App\Policies\Workflow\* plutôt que le App\Policies\* plat attendu par
 * l'auto-decouverte Laravel, donc enregistrement explicite via
 * Gate::policy() ici).
 * ==========================================================================
 */
class WorkflowPolicyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(Workflow::class, WorkflowPolicy::class);
        Gate::policy(WorkflowCategory::class, WorkflowCategoryPolicy::class);
        Gate::policy(Form::class, FormPolicy::class);
        Gate::policy(FormCategory::class, FormCategoryPolicy::class);
        Gate::policy(Request::class, RequestPolicy::class);
        Gate::policy(Validation::class, ValidationPolicy::class);
        Gate::policy(AuditLog::class, AuditLogPolicy::class);
    }
}
