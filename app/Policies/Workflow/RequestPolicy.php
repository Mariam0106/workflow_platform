<?php

declare(strict_types=1);

namespace App\Policies\Workflow;

use App\Enums\ApplicationRoleCode;
use App\Enums\RequestStatus;
use App\Models\Request;
use App\Models\User;
use App\Services\Workflow\ValidatorResolverService;

/**
 * ==========================================================================
 * RequestPolicy
 * ==========================================================================
 *
 * BR-31 : apres soumission, la Request devient lecture seule.
 * BR-32 : une Request rejetee ne peut plus etre modifiee.
 *
 * S'appuie sur ValidatorResolverService (Etape 8) plutot que de
 * reimplementer une logique de resolution de validateur ici - une seule
 * source de verite pour "qui peut agir sur cette Request".
 * ==========================================================================
 */
class RequestPolicy
{
    public function __construct(
        private readonly ValidatorResolverService $validatorResolver,
    ) {
    }

    public function before(User $user, string $ability): ?bool
    {
        if (in_array($ability, ['update', 'delete'], true)) {
            return null;
        }

        return $user->hasRole(ApplicationRoleCode::Administrator) ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Visible par : le demandeur lui-meme, ou le Validateur actuellement
     * habilite a agir sur son Step courant (BR-36).
     */
    public function view(User $user, Request $request): bool
    {
        if ($request->requester_id === $user->id) {
            return true;
        }

        return $request->currentStep !== null
            && $this->validatorResolver->isAuthorized($user, $request->currentStep, $request);
    }

    public function create(User $user): bool
    {
        return true;
    }

    /**
     * BR-31/32 : seul le demandeur peut modifier SA PROPRE Request, et
     * uniquement tant qu'elle est en Draft (BR-30 : modifications
     * illimitees a ce stade).
     */
    public function update(User $user, Request $request): bool
    {
        return $request->requester_id === $user->id
            && $request->status === RequestStatus::Draft;
    }

    /**
     * Meme restriction que update() - jamais de suppression au-dela du
     * Draft, par coherence avec BR-31/32.
     */
    public function delete(User $user, Request $request): bool
    {
        return $request->requester_id === $user->id
            && $request->status === RequestStatus::Draft;
    }

    /**
     * BR-30 : soumettre une Request Draft (Draft -> Submitted).
     */
    public function submit(User $user, Request $request): bool
    {
        return $request->requester_id === $user->id
            && $request->status === RequestStatus::Draft;
    }
}
