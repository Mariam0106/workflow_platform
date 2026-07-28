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
 * Forms : le meme cycle de vie Draft/Published/Archived existe pour les
 * deux, cf. l'audit BR - un Form publie ne doit pas etre modifiable
 * directement non plus, pour la meme raison : des Requests existantes
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

    public function delete(User $user, Form $form): bool
    {
        return $user->hasRole(ApplicationRoleCode::Administrator) && ! $form->isPublished();
    }

    public function publish(User $user, Form $form): bool
    {
        return $user->hasRole(ApplicationRoleCode::Administrator) && $form->isDraft();
    }
}
