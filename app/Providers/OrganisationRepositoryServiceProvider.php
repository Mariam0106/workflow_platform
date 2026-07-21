<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Repositories\Organisation\DepartmentRepositoryInterface;
use App\Contracts\Repositories\Organisation\EntityRepositoryInterface;
use App\Contracts\Repositories\Organisation\UserRepositoryInterface;
use App\Repositories\Eloquent\Organisation\DepartmentRepository;
use App\Repositories\Eloquent\Organisation\EntityRepository;
use App\Repositories\Eloquent\Organisation\UserRepository;
use Illuminate\Support\ServiceProvider;

/**
 * ==========================================================================
 * OrganisationRepositoryServiceProvider
 * ==========================================================================
 *
 * Registers the Organisation module's repository interfaces with their
 * Eloquent-based implementations in the service container.
 *
 * This ensures that whenever a controller, service, or another repository
 * type-hints one of the Organisation repository interfaces, Laravel's IoC
 * container automatically injects the correct concrete implementation.
 *
 * Registered bindings
 * -------------------
 * - EntityRepositoryInterface::class      → EntityRepository
 * - DepartmentRepositoryInterface::class  → DepartmentRepository
 * - UserRepositoryInterface::class        → UserRepository
 *
 * @see \App\Contracts\Repositories\Organisation
 * @see \App\Repositories\Eloquent\Organisation
 * ==========================================================================
 */
class OrganisationRepositoryServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        EntityRepositoryInterface::class => EntityRepository::class,
        DepartmentRepositoryInterface::class => DepartmentRepository::class,
        UserRepositoryInterface::class => UserRepository::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bindings are declared in the $bindings property above.
        // No additional registration logic is needed at this stage.
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
