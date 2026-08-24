<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * ==========================================================================
 * FormPriority Enum
 * ==========================================================================
 *
 * Urgence métier d'un Formulaire (visible par les Validateurs, voir
 * migration 2026_08_04_150000_add_priority_to_forms_table.php) - à ne
 * jamais confondre avec `WorkflowTransition::priority`, qui sert au
 * moteur (BR-23) et n'est jamais montré à un Validateur.
 */
enum FormPriority: string
{
    case Normal = 'Normal';
    case High = 'High';
    case Urgent = 'Urgent';

    public function label(): string
    {
        return match ($this) {
            self::Normal => 'Normale',
            self::High => 'Élevée',
            self::Urgent => 'Urgente',
        };
    }

    /**
     * Utilisé pour trier la file d'attente d'un Validateur (Urgent
     * d'abord) - voir MyValidationController::index().
     */
    public function sortWeight(): int
    {
        return match ($this) {
            self::Urgent => 3,
            self::High => 2,
            self::Normal => 1,
        };
    }
}
