<?php

declare(strict_types=1);

namespace App\Contracts\Repositories\Workflow;

use App\Models\Request;
use App\Models\User;
use App\ValueObjects\RequestReference;
use Illuminate\Database\Eloquent\Collection;

/**
 * ==========================================================================
 * RequestRepositoryInterface
 * ==========================================================================
 */
interface RequestRepositoryInterface
{
    public function findById(int $id): ?Request;

    public function findByReference(RequestReference $reference): ?Request;

    /**
     * BR-29 : prochain numero de sequence pour une annee donnee - doit
     * etre atomique (verrouillage/transaction) pour garantir l'unicite
     * sous forte concurrence. C'est le Repository, pas le Value Object
     * RequestReference, qui sait COMMENT obtenir ce numero (compteur
     * dedie, MAX(id)+1 verrouille, etc.) - RequestReference se contente
     * de le formater.
     */
    public function nextSequenceNumber(int $year): int;

    /**
     * BR-36 : Requests actuellement en attente de validation par ce
     * User precis (utilise par le tableau de bord "mes validations a
     * faire", Etape 15).
     */
    public function findPendingForValidator(User $validator): Collection;
}
