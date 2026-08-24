<?php

declare(strict_types=1);

namespace App\Exceptions\Workflow;

class WorkflowCategoryNotFoundException extends WorkflowEngineException
{
    public static function withId(?int $id): self
    {
        return new self(
            message: "Catégorie de workflow [{$id}] introuvable.",
            errorCode: 'workflow_category_not_found',
            context: ['workflow_category_id' => $id],
        );
    }

    protected function defaultHttpStatus(): int
    {
        return 404;
    }
}
