<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * ==========================================================================
 * RegistrationStatus Enum
 * ==========================================================================
 *
 * Cycle de vie d'une auto-inscription publique (formulaire d'inscription,
 * distinct de la création d'Utilisateur par un Administrateur, toujours
 * "Approved"). Pending -> Approved (le compte devient utilisable) ou
 * Pending -> Rejected (jamais utilisable, conservé pour trace/historique
 * plutôt que supprimé).
 * ==========================================================================
 */
enum RegistrationStatus: string
{
    case Pending = 'Pending';
    case Approved = 'Approved';
    case Rejected = 'Rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Approved => 'Approuvée',
            self::Rejected => 'Refusée',
        };
    }
}
