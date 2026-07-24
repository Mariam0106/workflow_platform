<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Repositories\Workflow\FormRepositoryInterface;
use App\Contracts\Repositories\Workflow\RequestRepositoryInterface;
use App\Contracts\Repositories\Workflow\WorkflowRepositoryInterface;
use App\Contracts\Services\Workflow\OrganisationManagerResolverInterface;
use App\Contracts\Services\Workflow\WorkflowEngineInterface;
use App\Repositories\Eloquent\Workflow\FormRepository;
use App\Repositories\Eloquent\Workflow\RequestRepository;
use App\Repositories\Eloquent\Workflow\WorkflowRepository;
use App\Services\Workflow\Placeholders\NullOrganisationManagerResolver;
use App\Services\Workflow\WorkflowEngineService;
use Illuminate\Support\ServiceProvider;

/**
 * ==========================================================================
 * WorkflowServiceProvider
 * ==========================================================================
 *
 * Enregistre les interfaces du module Workflow avec leurs implementations
 * concretes dans le conteneur - miroir exact de
 * App\Providers\OrganisationRepositoryServiceProvider (domaine
 * Organisation). Chaque domaine a son propre Service Provider : plus
 * aucun risque de conflit Git sur un AppServiceProvider partage.
 *
 * IMPORTANT : OrganisationManagerResolverInterface est bindee
 * temporairement sur NullOrganisationManagerResolver (retourne toujours
 * null - degrade proprement, BR-59 ENTITY_MANAGER/DEPARTMENT_MANAGER).
 * A remplacer par le binding vers l'implementation reelle des que le
 * domaine Organisation la fournit (voir LISEZMOI de l'Etape 9).
 *
 * @see \App\Contracts\Repositories\Workflow
 * @see \App\Contracts\Services\Workflow
 * @see \App\Repositories\Eloquent\Workflow
 * @see \App\Services\Workflow
 * ==========================================================================
 */
class WorkflowServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        WorkflowRepositoryInterface::class => WorkflowRepository::class,
        FormRepositoryInterface::class => FormRepository::class,
        RequestRepositoryInterface::class => RequestRepository::class,
        OrganisationManagerResolverInterface::class => NullOrganisationManagerResolver::class,
        WorkflowEngineInterface::class => WorkflowEngineService::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bindings are declared in the $bindings property above.
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
