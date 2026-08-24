<?php

declare(strict_types=1);

namespace App\Exceptions\Workflow;

use App\Models\Form;

/**
 * Thrown when a Request cannot be created from a Form - either the Form
 * does not exist, or it exists but is not Published (BR-28).
 */
class FormNotPublishedException extends WorkflowEngineException
{
    public static function notFound(int $formId): self
    {
        return new self(
            message: "Formulaire [{$formId}] introuvable.",
            errorCode: 'form_not_found',
            context: ['form_id' => $formId],
            httpStatus: 404,
        );
    }

    /**
     * BR-28 : Every Request is created from one Published Form.
     */
    public static function notPublished(Form $form): self
    {
        return new self(
            message: "Le formulaire \"{$form->code}\" n'est pas publié (statut actuel : {$form->status->value}) - aucune demande ne peut être créée à partir de lui.",
            errorCode: 'form_not_published',
            context: ['form_id' => $form->id, 'status' => $form->status->value],
            httpStatus: 422,
        );
    }

    protected function defaultHttpStatus(): int
    {
        return 422;
    }
}
