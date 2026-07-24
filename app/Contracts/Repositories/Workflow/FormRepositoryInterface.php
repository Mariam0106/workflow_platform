<?php

declare(strict_types=1);

namespace App\Contracts\Repositories\Workflow;

use App\Models\Form;

/**
 * ==========================================================================
 * FormRepositoryInterface
 * ==========================================================================
 */
interface FormRepositoryInterface
{
    public function findById(int $id): ?Form;

    /**
     * Charge le Form avec ses FormFields (et leurs FieldOptions) - evite
     * le N+1 quand le moteur de rendu dynamique (Etape 14) construit le
     * formulaire complet.
     */
    public function findWithFields(int $id): ?Form;

    /**
     * BR-25 (cote Form) : derniere version PUBLIEE d'un Form partageant
     * ce code.
     */
    public function findLatestPublishedVersion(string $code): ?Form;
}
