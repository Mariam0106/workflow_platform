<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * NOTE : les bindings de domaine ne vivent plus ici (evite tout
     * conflit Git sur ce fichier partage). Voir
     * App\Providers\WorkflowServiceProvider (domaine Workflow) et
     * App\Providers\OrganisationRepositoryServiceProvider (domaine
     * Organisation, cote collegue).
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
