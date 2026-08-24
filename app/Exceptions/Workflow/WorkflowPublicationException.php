<?php

declare(strict_types=1);

namespace App\Exceptions\Workflow;

use App\Models\Workflow;

class WorkflowPublicationException extends WorkflowEngineException
{
    /**
     * BR-18 : "Chaque Workflow contient au moins une Étape."
     */
    public static function noSteps(Workflow $workflow): self
    {
        return new self(
            message: "Le workflow \"{$workflow->code}\" ne peut pas être publié sans aucune étape active.",
            errorCode: 'workflow_has_no_steps',
            context: ['workflow_id' => $workflow->id],
        );
    }

    /**
     * BR-33 : "Chaque Workflow possède exactement une Étape de début."
     */
    public static function noStartStep(Workflow $workflow): self
    {
        return new self(
            message: "Le workflow \"{$workflow->code}\" doit avoir une étape de début désignée avant d'être publié.",
            errorCode: 'workflow_has_no_start_step',
            context: ['workflow_id' => $workflow->id],
        );
    }

    /**
     * BR-34 : "Chaque Workflow possède au moins une Étape de fin."
     */
    public static function noEndStep(Workflow $workflow): self
    {
        return new self(
            message: "Le workflow \"{$workflow->code}\" doit avoir au moins une étape de fin désignée avant d'être publié.",
            errorCode: 'workflow_has_no_end_step',
            context: ['workflow_id' => $workflow->id],
        );
    }

    protected function defaultHttpStatus(): int
    {
        return 422;
    }
}
