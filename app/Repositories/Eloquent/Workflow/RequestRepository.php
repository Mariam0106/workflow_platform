<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Workflow;

use App\Contracts\Repositories\Workflow\RequestRepositoryInterface;
use App\Enums\RequestStatus;
use App\Enums\ValidatorType;
use App\Models\Request;
use App\Models\User;
use App\Services\Workflow\ValidatorResolverService;
use App\ValueObjects\RequestReference;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RequestRepository implements RequestRepositoryInterface
{
    public function __construct(
        private readonly ValidatorResolverService $validatorResolver,
    ) {}

    public function findById(int $id): ?Request
    {
        return Request::find($id);
    }

    public function findByReference(RequestReference $reference): ?Request
    {
        return Request::query()
            ->where('reference_number', $reference->value)
            ->first();
    }

    /**
     * NOTE: verrouille les lignes de l'année concernee pour
     * calculer le prochain numero, ce qui protege correctement contre
     * les collisions sous MySQL (InnoDB) tant que la transaction
     * englobe aussi l'INSERT final (a faire par WorkflowEngineService,
     * Étape 9 - ne PAS appeler cette methode dans une transaction
     * separee de la creation de la Request, sinon la fenetre de race
     * condition se rouvre entre les deux appels).
     *
     * Filet de sécurité supplementaire : reference_number est UNIQUE en
     * base - même en cas de collision improbable, l'INSERT
     * echouera proprement plutôt que de dupliquer une référence,
     * remontant une QueryException que le Service devra intercepter et
     * reessayer (voir Étape 9).
     */
    public function nextSequenceNumber(int $year): int
    {
        return DB::transaction(function () use ($year) {
            $count = Request::withTrashed()
                ->whereYear('created_at', $year)
                ->lockForUpdate()
                ->count();

            return $count + 1;
        });
    }

    /**
     * BR-36 : Requests actuellement en attente de CE validateur precis.
     *
     * Ne resout que les stratégies structurellement verifiables sans
     * dependre du domaine Organisation (ROLE via user_application_roles,
     * USER via l'id exact, N_PLUS_1 via manager_id deja present sur
     * users - Étape 0). Les stratégies ENTITY_MANAGER/DEPARTMENT_MANAGER
     * necessitent une notion de "responsable d'entité/departement" qui
     * releve du domaine Organisation et sera branchee via
     * ValidatorResolverService, pas ici.
     *
     * NOTE (multi-role, BR-06) : ROLE se verifie desormais contre TOUS
     * les Roles Applicatifs autorises de $validator (pivot
     * user_application_roles), pas seulement son Role par defaut -
     * sinon un Validateur multi-role n'ayant pas "Validateur" comme
     * Role par defaut ne verrait jamais les Requests qui l'attendent.
     */
    public function findPendingForValidator(User $validator): Collection
    {
        $assignedRoleIds = $validator->applicationRoles()->pluck('application_roles.id');

        return Request::query()
            ->where('status', RequestStatus::Submitted)
            ->where(function ($query) use ($validator, $assignedRoleIds) {
                $query->whereHas('currentStep', function ($q) use ($validator) {
                    $q->where('validator_type', ValidatorType::User)
                        ->where('validator_reference', $validator->id);
                })->orWhereHas('currentStep', function ($q) use ($assignedRoleIds) {
                    $q->where('validator_type', ValidatorType::Role)
                        ->whereIn('validator_reference', $assignedRoleIds);
                })->orWhereHas('currentStep', function ($q) use ($validator) {
                    // BR-05 : "Fonction Métier" route vers N'IMPORTE QUI
                    // ayant cette fonction (Commercial, DCM, Crédit
                    // Client, DAF, DG...), indépendamment de son propre
                    // Département/Entité - manquait entièrement ici,
                    // silencieusement (aucune erreur, juste "0 à
                    // valider" et absent de la liste), depuis l'ajout
                    // de ce type de Validateur.
                    $q->where('validator_type', ValidatorType::BusinessFunction)
                        ->where('validator_reference', $validator->business_function_id);
                })->orWhereHas('currentStep', function ($q) {
                    // Élargi à toute Étape N+1, sans filtrer sur le
                    // Responsable direct du Demandeur ici - une chaîne
                    // de plusieurs Étapes N+1 successives escalade d'un
                    // niveau hiérarchique à chaque fois (voir
                    // ValidatorResolverService::resolveNPlus1()), donc
                    // "être le bon N+1" ne se vérifie plus par une
                    // simple comparaison SQL de manager_id - filtré
                    // précisément juste après, en PHP, avec la même
                    // logique que l'autorisation réelle.
                    $q->where('validator_type', ValidatorType::NPlus1);
                });
            })
            ->with('currentStep', 'requester.manager.manager.manager.manager')
            ->get()
            ->filter(function (Request $request) use ($validator) {
                if ($request->currentStep?->validator_type !== ValidatorType::NPlus1) {
                    return true; // déjà filtré précisément en SQL ci-dessus
                }

                return $this->validatorResolver->isAuthorized($validator, $request->currentStep, $request);
            })
            ->values();
    }
}
