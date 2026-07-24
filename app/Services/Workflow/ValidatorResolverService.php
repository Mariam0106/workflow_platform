<?php

declare(strict_types=1);

namespace App\Services\Workflow;

use App\Contracts\Services\Workflow\OrganisationManagerResolverInterface;
use App\Enums\ValidatorType;
use App\Models\Request;
use App\Models\User;
use App\Models\WorkflowStep;
use Illuminate\Support\Collection;

/**
 * ==========================================================================
 * ValidatorResolverService
 * ==========================================================================
 *
 * BR-59 : le moteur determine le validateur via la configuration
 * (workflow_steps.validator_type / validator_reference), jamais via du
 * code metier code en dur. Ajouter une nouvelle Entity/Departement/Role
 * ne necessite donc jamais de modifier cette classe.
 * ==========================================================================
 */
class ValidatorResolverService
{
    public function __construct(
        private readonly OrganisationManagerResolverInterface $managerResolver,
    ) {
    }

    /**
     * Retourne tous les Users habilites a valider ce Step pour cette
     * Request precise (BR-27/59).
     *
     * @return Collection<int, User>
     */
    public function resolve(WorkflowStep $step, Request $request): Collection
    {
        return match ($step->validator_type) {
            ValidatorType::User => User::query()
                ->where('id', $step->validator_reference)
                ->where('is_active', true)
                ->get(),

            ValidatorType::Role => User::query()
                ->where('application_role_id', $step->validator_reference)
                ->where('is_active', true)
                ->get(),

            ValidatorType::NPlus1 => $this->wrap($request->requester->manager),

            ValidatorType::EntityManager => $this->wrap(
                $this->managerResolver->managerOfEntity($request->requester->entity)
            ),

            ValidatorType::DepartmentManager => $this->wrap(
                $this->managerResolver->managerOfDepartment($request->requester->department)
            ),
        };
    }

    /**
     * BR-36 : seul le Validateur assigne peut valider.
     */
    public function isAuthorized(User $user, WorkflowStep $step, Request $request): bool
    {
        return $this->resolve($step, $request)->contains('id', $user->id);
    }

    /**
     * @return Collection<int, User>
     */
    private function wrap(?User $user): Collection
    {
        return $user ? new Collection([$user]) : new Collection();
    }
}
