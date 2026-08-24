<?php

declare(strict_types=1);

namespace App\Exceptions\Workflow;

use App\Models\Form;
use App\Models\Request;
use App\Models\Workflow;
use App\Models\WorkflowTransition;

/**
 * Thrown when a Workflow is looked up (by id or by code) and does not
 * exist, or no PUBLISHED version of it exists (BR-25 : new Requests
 * must use the latest published version).
 */
class WorkflowNotFoundException extends WorkflowEngineException
{
    public static function withId(int $id): self
    {
        return new self(
            message: "Workflow [{$id}] introuvable.",
            errorCode: 'workflow_not_found',
            context: ['workflow_id' => $id],
        );
    }

    /**
     * BR-25 : aucune version PUBLIEE de ce Workflow (identifie par son
     * code) n'existe.
     */
    public static function noPublishedVersion(string $code): self
    {
        return new self(
            message: "Aucune version publiée du Workflow \"{$code}\" n'a été trouvée.",
            errorCode: 'workflow_no_published_version',
            context: ['code' => $code],
        );
    }

    protected function defaultHttpStatus(): int
    {
        return 404;
    }

    /**
     * BR-54 : archiver (soft delete) une Step au lieu de la supprimer
     * est le comportement attendu - mais si une Request est encore
     * positionnee dessus au moment ou elle est archivee, la relation
     * currentStep() renvoie silencieusement null (SoftDeletes exclut
     * les enregistrements archives des relations belongsTo standard).
     * Sans cette exception, le moteur plantait avec une TypeError brute
     * au lieu d'un echec metier propre.
     */
    public static function currentStepUnavailable(Request $request): self
    {
        return new self(
            message: "L'étape actuelle de la demande \"{$request->reference_number}\" n'est plus disponible (probablement archivée).",
            errorCode: 'workflow_current_step_unavailable',
            context: ['request_id' => $request->id, 'current_step_id' => $request->current_step_id],
        );
    }

    /**
     * Meme risque que ci-dessus, mais sur l'etape de DESTINATION d'une
     * Transition (WorkflowTransition::toStep()).
     */
    public static function transitionTargetUnavailable(WorkflowTransition $transition): self
    {
        return new self(
            message: "L'étape de destination de la transition \"{$transition->action_name}\" n'est plus disponible (probablement archivée).",
            errorCode: 'workflow_transition_target_unavailable',
            context: ['workflow_transition_id' => $transition->id, 'to_step_id' => $transition->to_step_id],
        );
    }

    /**
     * Meme risque que ci-dessus, mais sur le Workflow associe a un Form
     * (Form::workflow()) - si ce Workflow est archive.
     */
    public static function workflowOfFormUnavailable(Form $form): self
    {
        return new self(
            message: "Le Workflow associé au formulaire \"{$form->code}\" n'est plus disponible.",
            errorCode: 'workflow_of_form_unavailable',
            context: ['form_id' => $form->id],
        );
    }
}
