<?php

use App\Providers\AppServiceProvider;
use App\Providers\OrganisationPolicyServiceProvider;
use App\Providers\OrganisationRepositoryServiceProvider;
use App\Providers\WorkflowEventServiceProvider;
use App\Providers\WorkflowPolicyServiceProvider;
use App\Providers\WorkflowServiceProvider;

return [
    AppServiceProvider::class,
    WorkflowServiceProvider::class,
    OrganisationRepositoryServiceProvider::class,
    OrganisationPolicyServiceProvider::class,
    WorkflowEventServiceProvider::class,
    WorkflowPolicyServiceProvider::class,
];