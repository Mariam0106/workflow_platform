<?php

declare(strict_types=1);

namespace App\Exceptions\Workflow;

use App\Models\Form;
use App\Models\Request;

/**
 * ==========================================================================
 * RequestException
 * ==========================================================================
 *
 * Violations liees au cycle de vie d'une Request (soumission,
 * modification, etc.) - pas au Workflow lui-meme.
 * ==========================================================================
 */
class RequestException extends WorkflowException
{
    /**
     * BR-28 : Every Request is created from one Published Form.
     */
    public static function formNotPublished(Form $form): self
    {
        return new self(
            "Form \"{$form->code}\" is not published (current status: {$form->status->value}) - no Request can be created from it.",
            'request.form_not_published',
            ['form_id' => $form->id, 'status' => $form->status->value],
            httpStatus: 422,
        );
    }

    /**
     * BR-31 : after Submission, the Request becomes read-only.
     * BR-32 : Rejected Requests cannot be modified.
     */
    public static function notEditable(Request $request): self
    {
        return new self(
            "Request \"{$request->reference_number}\" is not editable (current status: {$request->status->value}).",
            'request.not_editable',
            ['request_id' => $request->id, 'status' => $request->status->value],
            httpStatus: 403,
        );
    }
}
