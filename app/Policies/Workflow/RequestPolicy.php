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
 * S'appuie sur ValidatorResolverService plutôt que de
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

    /**
     * BR-74/75 : vue d'ensemble "toutes les Demandes de la plateforme"
     * (RequestController du BackOffice) - Administrateur uniquement
     * (via before()) ; false ici pour tout le monde d'autre, qui garde
     * ses propres vues scopées (my-requests/my-validations).
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Visible par : le demandeur lui-même, le Validateur actuellement
     * habilité à agir sur son Step courant (BR-36), tout Validateur
     * ayant déjà pris une décision sur cette Request par le passé, OU
     * (une fois la Request terminée) tout destinataire configuré sur
     * son Workflow pour les notifications de clôture - sans ce
     * dernier cas, ces destinataires recevraient bien leur
     * notification mais ne pourraient jamais l'ouvrir (BR-63 : une
     * Notification doit pouvoir être suivie jusqu'à ce qu'elle
     * représente).
     */
    public function view(User $user, Request $request): bool
    {
        if ($request->requester_id === $user->id) {
            return true;
        }

        if ($request->currentStep !== null && $this->validatorResolver->isAuthorized($user, $request->currentStep, $request)) {
            return true;
        }

        if ($request->validations()->where('validator_id', $user->id)->exists()) {
            return true;
        }

        if ($request->status->isTerminal()) {
            foreach ($request->workflow?->completionNotifications ?? [] as $completionNotification) {
                if ($completionNotification->resolveRecipients()->contains('id', $user->id)) {
                    return true;
                }
            }
        }

        return false;
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
