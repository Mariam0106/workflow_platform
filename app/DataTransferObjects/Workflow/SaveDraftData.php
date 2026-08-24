<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Workflow;

/**
 * ==========================================================================
 * SaveDraftData
 * ==========================================================================
 *
 * Ce que WorkflowEngineService::saveDraft() reçoit - délibérément
 * distinct de SubmitRequestData, dont le constructeur lève une
 * exception si "values" est vide (invariant correct pour une VRAIE
 * soumission, BR-28, mais faux pour un brouillon : l'utilisateur peut
 * très bien enregistrer un brouillon avant d'avoir rempli le moindre
 * champ, ou après les avoir tous effacés).
 * ==========================================================================
 */
final readonly class SaveDraftData
{
    /**
     * @param  array<int, string>  $values  [form_field_id => value], peut être vide
     */
    public function __construct(
        public int $formId,
        public int $requesterId,
        public array $values,
    ) {}
}
