<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Workflow;

use App\Contracts\Repositories\Workflow\RequestRepositoryInterface;
use App\Enums\RequestStatus;
use App\Enums\ValidatorType;
use App\Models\Request;
use App\Models\User;
use App\ValueObjects\RequestReference;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RequestRepository implements RequestRepositoryInterface
{
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
     * NOTE (Etape 8) : verrouille les lignes de l'annee concernee pour
     * calculer le prochain numero, ce qui protege correctement contre
     * les collisions sous MySQL (InnoDB) tant que la transaction
     * englobe aussi l'INSERT final (a faire par WorkflowEngineService,
     * Etape 9 - ne PAS appeler cette methode dans une transaction
     * separee de la creation de la Request, sinon la fenetre de race
     * condition se rouvre entre les deux appels).
     *
     * Filet de securite supplementaire : reference_number est UNIQUE en
     * base (Etape 0) - meme en cas de collision improbable, l'INSERT
     * echouera proprement plutot que de dupliquer une reference,
     * remontant une QueryException que le Service devra intercepter et
     * reessayer (voir Etape 9).
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
     * Ne resout que les strategies structurellement verifiables sans
     * dependre du domaine Organisation (ROLE via application_role_id,
     * USER via l'id exact, N_PLUS_1 via manager_id deja present sur
     * users - Etape 0). Les strategies ENTITY_MANAGER/DEPARTMENT_MANAGER
     * necessitent une notion de "responsable d'entite/departement" qui
     * releve du domaine Organisation et sera branchee via
     * ValidatorResolverService (Etape 9), pas ici.
     */
    public function findPendingForValidator(User $validator): Collection
    {
        return Request::query()
            ->where('status', RequestStatus::Submitted)
            ->where(function ($query) use ($validator) {
                $query->whereHas('currentStep', function ($q) use ($validator) {
                    $q->where('validator_type', ValidatorType::User)
                        ->where('validator_reference', $validator->id);
                })->orWhereHas('currentStep', function ($q) use ($validator) {
                    $q->where('validator_type', ValidatorType::Role)
                        ->where('validator_reference', $validator->application_role_id);
                })->orWhere(function ($q) use ($validator) {
                    $q->whereHas('currentStep', fn ($cs) => $cs->where('validator_type', ValidatorType::NPlus1))
                        ->whereHas('requester', fn ($u) => $u->where('manager_id', $validator->id));
                });
            })
            ->get();
    }
}
