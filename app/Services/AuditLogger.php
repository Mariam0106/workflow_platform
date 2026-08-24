<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;

/**
 * ==========================================================================
 * AuditLogger
 * ==========================================================================
 *
 * BR-69 : "Chaque action importante est enregistrée dans le Journal
 * d'Audit." - jusqu'ici, seuls 4 événements du cycle de vie d'une
 * Request (soumission/approbation/rejet/aboutissement, voir
 * App\Listeners\Workflow\CreateAuditLog) étaient journalisés. Toute
 * action de configuration (créer/modifier/archiver un Utilisateur, un
 * Département, un Formulaire, un Workflow...) n'était PAS enregistrée -
 * lacune corrigée en câblant ce Service dans chaque Service métier
 * plutôt que dans chaque Controller (un seul point d'appel par action,
 * jamais dupliqué entre la route web et une éventuelle API future).
 *
 * Volontairement neutre (App\Services, ni Organisation ni Workflow) :
 * l'audit est par nature transverse aux deux domaines - le seul
 * endroit de ce projet où un couplage cross-domaine est délibéré plutôt
 * que subi (voir la même exception documentée dans
 * DashboardController).
 *
 * BR-73 : les entrées ne sont jamais modifiées ni supprimées - ce
 * Service n'expose donc volontairement qu'une méthode d'écriture
 * (log), aucune de mise à jour ou de suppression.
 * ==========================================================================
 */
class AuditLogger
{
    /**
     * @param array<string, mixed>|null $oldValues état avant l'action (omis pour une création)
     * @param array<string, mixed>|null $newValues état après l'action
     */
    public function log(
        int $userId,
        string $action,
        string $entityType,
        int $entityId,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): void {
        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
