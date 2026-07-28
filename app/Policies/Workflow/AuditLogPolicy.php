<?php

declare(strict_types=1);

namespace App\Policies\Workflow;

use App\Enums\ApplicationRoleCode;
use App\Models\AuditLog;
use App\Models\User;

/**
 * ==========================================================================
 * AuditLogPolicy
 * ==========================================================================
 *
 * BR-49 : l'historique d'audit n'est JAMAIS modifiable, sans exception
 * (meme logique que ValidationPolicy - BR-42). La consultation reste
 * reservee aux Administrateurs (BR-51 : donnees sensibles - IP,
 * navigateur, anciennes/nouvelles valeurs).
 * ==========================================================================
 */
class AuditLogPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if (in_array($ability, ['view', 'viewAny'], true) && $user->hasRole(ApplicationRoleCode::Administrator)) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole(ApplicationRoleCode::Administrator);
    }

    public function view(User $user, AuditLog $auditLog): bool
    {
        return $user->hasRole(ApplicationRoleCode::Administrator);
    }

    /**
     * BR-49 : jamais, pour personne, sans exception.
     */
    public function update(User $user, AuditLog $auditLog): bool
    {
        return false;
    }

    /**
     * BR-49 : jamais, pour personne, sans exception.
     */
    public function delete(User $user, AuditLog $auditLog): bool
    {
        return false;
    }
}
