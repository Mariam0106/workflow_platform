<?php

use App\Providers\AppServiceProvider;
use App\Providers\OrganisationPolicyServiceProvider;
use App\Providers\OrganisationRepositoryServiceProvider;

return [
    AppServiceProvider::class,
    OrganisationRepositoryServiceProvider::class,
    OrganisationPolicyServiceProvider::class,
];
