<?php

declare(strict_types=1);

namespace App\Exceptions\Workflow;

use App\Models\Form;

class FormPublicationException extends WorkflowEngineException
{
    /**
     * BR-13 : "Chaque Formulaire contient au moins un Champ."
     */
    public static function noFields(Form $form): self
    {
        return new self(
            message: "Le formulaire \"{$form->code}\" ne peut pas être publié sans aucun champ actif.",
            errorCode: 'form_has_no_fields',
            context: ['form_id' => $form->id],
        );
    }

    /**
     * BR-30 : "Seuls les Workflows publiés peuvent être associés à des
     * Formulaires publiés."
     */
    public static function workflowNotPublished(Form $form): self
    {
        return new self(
            message: "Le Workflow associé au formulaire \"{$form->code}\" n'est pas publié - publiez-le d'abord.",
            errorCode: 'form_workflow_not_published',
            context: ['form_id' => $form->id, 'workflow_id' => $form->workflow_id],
        );
    }

    protected function defaultHttpStatus(): int
    {
        return 422;
    }
}
