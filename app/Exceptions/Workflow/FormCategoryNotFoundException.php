<?php

declare(strict_types=1);

namespace App\Exceptions\Workflow;

class FormCategoryNotFoundException extends WorkflowEngineException
{
    public static function withId(?int $id): self
    {
        return new self(
            message: "Catégorie de formulaire [{$id}] introuvable.",
            errorCode: 'form_category_not_found',
            context: ['form_category_id' => $id],
        );
    }

    protected function defaultHttpStatus(): int
    {
        return 404;
    }
}
