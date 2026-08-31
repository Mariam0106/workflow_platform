<?php

declare(strict_types=1);

namespace App\Policies\Workflow;

use App\Enums\ApplicationRoleCode;
use App\Models\Request;
use App\Models\User;
use App\Models\Validation;
use App\Services\Workflow\ValidatorResolverService;

/**
 * ==========================================================================
 * ValidationPolicy
 * ==========================================================================
 *
 * BR-36 : seul le Validateur assigne peut valider.
 * BR-42 : l'historique de validation n'est JAMAIS modifiable - pas
 * même par un Administrateur. Contrairement a WorkflowPolicy/FormPolicy
 * (ou seuls update/delete echappent au bypass Admin), ici AUCUNE
 * capacite d'ecriture posterieure a la creation n'existe : before() ne
 * bypasse donc que viewAny/view, jamais create (qui a sa propre règle
 * BR-36 stricte) ni update/delete (toujours false, sans exception).
 * ==========================================================================
 */
class ValidationPolicy
{
    public function __construct(
        private readonly ValidatorResolverService $validatorResolver,
    ) {
    }

    public function before(User $user, string $ability): ?bool
    {
        if (in_array($ability, ['view', 'viewAny'], true) && $user->hasRole(ApplicationRoleCode::Administrator)) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Validation $validation): bool
    {
        return $validation->validator_id === $user->id
            || $validation->request->requester_id === $user->id;
    }

    /**
     * BR-36 : seul le Validateur habilite pour ce Step precis peut
     * enregistrer une decision.
     */
    public function create(User $user, Request $request): bool
    {
        return $request->currentStep !== null
            && $this->validatorResolver->isAuthorized($user, $request->currentStep, $request);
    }

    /**
     * BR-42 : jamais, pour personne, sans exception.
     */
    public function update(User $user, Validation $validation): bool
    {
        return false;
    }

    /**
     * BR-42 : jamais, pour personne, sans exception.
     */
    public function delete(User $user, Validation $validation): bool
    {
        return false;
    }
}
