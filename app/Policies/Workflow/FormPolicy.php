<?php

declare(strict_types=1);

namespace App\Policies\Workflow;

use App\Enums\ApplicationRoleCode;
use App\Models\Form;
use App\Models\User;

/**
 * ==========================================================================
 * FormPolicy
 * ==========================================================================
 *
 * Meme principe que WorkflowPolicy (BR-26 applique par analogie aux
 * Forms : le même cycle de vie Draft/Published/Archived existe pour les
 * deux, cf. l'audit BR - un Form publie ne doit pas etre modifiable
 * directement non plus, pour la même raison : des Requests existantes
 * en dependent).
 * ==========================================================================
 */
class FormPolicy
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

    public function view(User $user, Form $form): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(ApplicationRoleCode::Administrator);
    }

    public function update(User $user, Form $form): bool
    {
        return $user->hasRole(ApplicationRoleCode::Administrator) && ! $form->isPublished();
    }

    /**
     * BR-76 : seul un Formulaire encore en brouillon peut être
     * définitivement supprimé - jamais un archivé, qui peut encore
     * être référencé par des Demandes historiques (contrairement à
     * !isPublished(), qui aurait aussi autorisé un Formulaire archivé).
     * Le Service vérifie en plus qu'aucune Demande ne le référence
     * déjà, en filet de sécurité.
     */
    public function delete(User $user, Form $form): bool
    {
        return $user->hasRole(ApplicationRoleCode::Administrator) && $form->isDraft();
    }

    public function publish(User $user, Form $form): bool
    {
        return $user->hasRole(ApplicationRoleCode::Administrator) && $form->isDraft();
    }

    /**
     * BR-16 : archiver retire un Form de la circulation (plus aucune
     * nouvelle Request ne peut en être créée) sans supprimer son
     * historique - possible depuis Draft (abandon) ou Published (fin
     * de vie normale), jamais depuis un Form déjà archivé.
     */
    public function archive(User $user, Form $form): bool
    {
        return $user->hasRole(ApplicationRoleCode::Administrator) && ! $form->isArchived();
    }
}
