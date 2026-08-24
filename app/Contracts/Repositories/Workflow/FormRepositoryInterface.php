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

    /**
     * Existe-t-il déjà un Form (toute version confondue) portant ce
     * code ? Utilisé par StoreFormRequest pour garder "code" unique
     * tant que cette version du Form Builder ne gère pas encore la
     * création de nouvelles versions d'un Form existant (voir
     * FormService - hors périmètre de cette phase).
     */
    public function existsByCode(string $code): bool;

    public function save(Form $form): Form;
}
