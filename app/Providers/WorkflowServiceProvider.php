<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Repositories\Workflow\FormCategoryRepositoryInterface;
use App\Contracts\Repositories\Workflow\FormRepositoryInterface;
use App\Contracts\Repositories\Workflow\RequestRepositoryInterface;
use App\Contracts\Repositories\Workflow\WorkflowCategoryRepositoryInterface;
use App\Contracts\Repositories\Workflow\WorkflowRepositoryInterface;
use App\Contracts\Services\Workflow\NotificationSenderInterface;
use App\Contracts\Services\Workflow\OrganisationManagerResolverInterface;
use App\Contracts\Services\Workflow\WorkflowEngineInterface;
use App\Repositories\Eloquent\Workflow\FormCategoryRepository;
use App\Repositories\Eloquent\Workflow\FormRepository;
use App\Repositories\Eloquent\Workflow\RequestRepository;
use App\Repositories\Eloquent\Workflow\WorkflowCategoryRepository;
use App\Repositories\Eloquent\Workflow\WorkflowRepository;
use App\Services\Workflow\MailNotificationSender;
use App\Services\Organisation\OrganisationManagerResolver;
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
 * OrganisationManagerResolverInterface est bindee sur
 * App\Services\Organisation\OrganisationManagerResolver, qui s'appuie
 * sur departments.manager_id / entities.manager_id (voir migration
 * add_manager_id_to_departments_and_entities) - remplace l'ancien
 * NullOrganisationManagerResolver (placeholder qui degradait toujours
 * vers "aucun validateur trouve" pour les Etapes DEPARTMENT_MANAGER /
 * ENTITY_MANAGER).
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
        WorkflowCategoryRepositoryInterface::class => WorkflowCategoryRepository::class,
        FormRepositoryInterface::class => FormRepository::class,
        FormCategoryRepositoryInterface::class => FormCategoryRepository::class,
        RequestRepositoryInterface::class => RequestRepository::class,
        OrganisationManagerResolverInterface::class => OrganisationManagerResolver::class,
        NotificationSenderInterface::class => MailNotificationSender::class,
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
