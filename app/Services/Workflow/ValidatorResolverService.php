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

            // BR-06 (multi-role) : un Step configure sur un Role donne
            // doit resoudre TOUT Utilisateur AUTORISE pour ce Role
            // (pivot user_application_roles), pas seulement ceux dont
            // il s'agit du Role par defaut - sinon un Validateur
            // multi-role dont le Role par defaut est "User" ne serait
            // jamais propose comme validateur pour ce Step.
            ValidatorType::Role => User::query()
                ->whereHas('applicationRoles', function ($query) use ($step): void {
                    $query->where('application_roles.id', $step->validator_reference);
                })
                ->where('is_active', true)
                ->get(),

            // BR-05 : route vers TOUT Utilisateur ayant cette Fonction
            // Métier (Commercial, Crédit Client, DAF, DG, ou toute
            // autre créée plus tard depuis le BackOffice) - quel que
            // soit son propre Département/Entité, contrairement a
            // Role qui lui n'est pas rattaché a une notion metier.
            ValidatorType::BusinessFunction => User::query()
                ->where('business_function_id', $step->validator_reference)
                ->where('is_active', true)
                ->get(),

            ValidatorType::NPlus1 => $this->resolveNPlus1($step, $request),

            ValidatorType::EntityManager => $this->wrap(
                $this->managerResolver->managerOfEntity($request->requester->entity)
            ),

            ValidatorType::DepartmentManager => $this->wrap(
                $this->managerResolver->managerOfDepartment($request->requester->department)
            ),
        };
    }

    /**
     * BR-59 (précision) : si plusieurs Étapes "Responsable direct (N+1)"
     * se succèdent dans un même Workflow, chacune doit escalader d'un
     * niveau hiérarchique de plus par rapport à la précédente - sans ce
     * correctif, TOUTES resolvaient systématiquement vers le même N+1
     * du Demandeur d'origine, obligeant la même personne à valider
     * plusieurs fois de suite la même Demande, ce qui n'a pas de sens.
     *
     * Le niveau à escalader se déduit du nombre d'Étapes N+1 déjà
     * traversées par CETTE Demande précise (ses Validations passées
     * dont l'Étape est elle-même de type N+1) - pas de la position de
     * $step dans le Workflow, car le chemin réellement suivi peut
     * varier selon les Transitions/Conditions (BR-84).
     *
     * @return Collection<int, User>
     */
    private function resolveNPlus1(WorkflowStep $step, Request $request): Collection
    {
        $priorNPlus1Count = $request->validations()
            ->whereHas('workflowStep', fn ($query) => $query->where('validator_type', ValidatorType::NPlus1))
            ->count();

        $manager = $request->requester->manager;

        for ($level = 0; $level < $priorNPlus1Count && $manager !== null; $level++) {
            $manager = $manager->manager;
        }

        return $this->wrap($manager);
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
