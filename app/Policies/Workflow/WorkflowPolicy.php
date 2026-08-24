<?php

declare(strict_types=1);

namespace App\Policies\Workflow;

use App\Enums\ApplicationRoleCode;
use App\Models\User;
use App\Models\Workflow;

/**
 * ==========================================================================
 * WorkflowPolicy
 * ==========================================================================
 *
 * BR-26 : Published Workflows cannot be modified directly - a new
 * version must be created instead.
 *
 * IMPORTANT : before() ne court-circuite JAMAIS update()/delete(). BR-26
 * est une regle absolue, pas juste une question de permission - meme un
 * Administrateur ne doit pas pouvoir modifier un Workflow publie
 * directement. before() n'accelere donc que les capacites qui ne sont
 * pas verrouillees par le cycle de vie (viewAny/view/create).
 * ==========================================================================
 */
class WorkflowPolicy
{
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

    public function view(User $user, Workflow $workflow): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(ApplicationRoleCode::Administrator);
    }

    /**
     * BR-26 : jamais vrai si le Workflow est publie, quel que soit le role.
     */
    public function update(User $user, Workflow $workflow): bool
    {
        return $user->hasRole(ApplicationRoleCode::Administrator) && ! $workflow->isPublished();
    }

    /**
     * BR-54/76 : la suppression definitive (hors soft-delete) reste
     * reservee aux Administrateurs, et seulement pour un Workflow
     * encore en brouillon - jamais un archivé, qui peut encore être
     * référencé par des Demandes historiques (contrairement à
     * !isPublished(), qui aurait aussi autorisé un Workflow archivé).
     * La verification "ce Workflow est-il encore reference par un
     * Formulaire" releve du Service (integrite des donnees), pas de
     * cette Policy (autorisation de l'acteur).
     */
    public function delete(User $user, Workflow $workflow): bool
    {
        return $user->hasRole(ApplicationRoleCode::Administrator) && $workflow->isDraft();
    }

    /**
     * Capacite personnalisee : publier un Workflow (Draft -> Published).
     */
    public function publish(User $user, Workflow $workflow): bool
    {
        return $user->hasRole(ApplicationRoleCode::Administrator) && $workflow->isDraft();
    }

    /**
     * BR-16 (par analogie) : archiver retire un Workflow de la
     * circulation sans supprimer son historique - possible depuis
     * Draft (abandon) ou Published (fin de vie normale), jamais depuis
     * un Workflow déjà archivé.
     */
    public function archive(User $user, Workflow $workflow): bool
    {
        return $user->hasRole(ApplicationRoleCode::Administrator) && ! $workflow->isArchived();
    }
}
