<?php

declare(strict_types=1);

namespace App\Exceptions\Workflow;

use App\Models\Workflow;

/**
 * AJOUT au-dela des 6 noms prevus par le roadmap partage : BR-26
 * ("Published Workflows cannot be modified directly - a new version
 * must be created") n'avait pas de classe dediee dans la liste
 * d'origine. Ajoutee ici en suivant exactement le meme pattern que les
 * autres (nom explicite, extends WorkflowEngineException) plutot que de
 * forcer ce cas dans une des 6 classes existantes ou il n'aurait pas eu
 * sa place semantiquement.
 */
class WorkflowNotModifiableException extends WorkflowEngineException
{
    /**
     * BR-26 : Published Workflows cannot be modified directly.
     */
    public static function becausePublished(Workflow $workflow): self
    {
        return new self(
            message: "Le Workflow \"{$workflow->code}\" (v{$workflow->version}) est publié et ne peut pas être modifié directement. Créez une nouvelle version.",
            errorCode: 'workflow_published_immutable',
            context: ['workflow_id' => $workflow->id, 'code' => $workflow->code, 'version' => $workflow->version],
        );
    }

    protected function defaultHttpStatus(): int
    {
        return 422;
    }

    /**
     * Suppression définitive refusée : au moins un Formulaire (brouillon
     * ou publié) référence encore ce Workflow. BR-76 : une
     * Configuration Métier référencée par des Données historiques ne
     * peut pas être supprimée, elle doit être archivée.
     */
    public static function becauseInUse(Workflow $workflow): self
    {
        return new self(
            message: "Le Workflow \"{$workflow->code}\" ne peut pas être supprimé : au moins un formulaire l'utilise encore. Archivez-le à la place.",
            errorCode: 'workflow_in_use',
            context: ['workflow_id' => $workflow->id, 'code' => $workflow->code],
        );
    }
}
