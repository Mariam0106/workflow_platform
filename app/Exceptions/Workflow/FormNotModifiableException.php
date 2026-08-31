<?php

declare(strict_types=1);

namespace App\Exceptions\Workflow;

use App\Models\Form;

/**
 * BR-18 : "Une Version de Formulaire publiée devient immuable." - mirrors
 * WorkflowNotModifiableException exactly, same reasoning (Requests may
 * already référence this exact version).
 */
class FormNotModifiableException extends WorkflowEngineException
{
    public static function becausePublished(Form $form): self
    {
        return new self(
            message: "Le formulaire \"{$form->code}\" (v{$form->version}, statut {$form->status->value}) n'est plus modifiable - seul un Formulaire en brouillon peut être édité.",
            errorCode: 'form_not_modifiable',
            context: ['form_id' => $form->id, 'code' => $form->code, 'version' => $form->version, 'status' => $form->status->value],
        );
    }

    protected function defaultHttpStatus(): int
    {
        return 422;
    }

    /**
     * Suppression définitive refusée : au moins une Demande référence
     * déjà ce Formulaire. En pratique ne devrait jamais se produire
     * pour un Formulaire encore en brouillon (BR-15 : seuls les
     * Formulaires publiés peuvent être utilisés pour créer une
     * Demande) - vérifié malgré tout par le Service avant suppression,
     * en filet de sécurité (BR-76 : une Configuration Métier
     * référencée par des Données historiques ne peut pas être
     * supprimée, elle doit être archivée).
     */
    public static function becauseInUse(Form $form): self
    {
        return new self(
            message: "Le formulaire \"{$form->code}\" ne peut pas être supprimé : au moins une demande le référence déjà. Archivez-le à la place.",
            errorCode: 'form_in_use',
            context: ['form_id' => $form->id, 'code' => $form->code],
        );
    }
}
