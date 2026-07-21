<?php

declare(strict_types=1);

namespace App\Exceptions\Workflow;

use App\Exceptions\DomainException;
use App\Models\Workflow;

/**
 * ==========================================================================
 * WorkflowException
 * ==========================================================================
 *
 * Violations liees au cycle de vie du Workflow lui-meme (pas a une
 * Request en particulier - voir RequestException pour ca).
 * ==========================================================================
 */
class WorkflowException extends DomainException
{
    protected function defaultHttpStatus(): int
    {
        return 422;
    }

    /**
     * BR-26 : Published Workflows cannot be modified directly.
     * A new version must be created instead.
     */
    public static function cannotModifyPublished(Workflow $workflow): self
    {
        return new self(
            "Workflow \"{$workflow->code}\" (v{$workflow->version}) is published and cannot be modified directly. Create a new version instead.",
            'workflow.published_immutable',
            ['workflow_id' => $workflow->id, 'code' => $workflow->code, 'version' => $workflow->version],
        );
    }

    /**
     * BR-15 : Only Published Workflows can be used to create new Requests.
     */
    public static function notPublished(Workflow $workflow): self
    {
        return new self(
            "Workflow \"{$workflow->code}\" is not published (current status: {$workflow->status->value}).",
            'workflow.not_published',
            ['workflow_id' => $workflow->id, 'status' => $workflow->status->value],
        );
    }

    /**
     * BR-18 : Every Workflow must contain at least one Step before it
     * can be published.
     */
    public static function hasNoSteps(Workflow $workflow): self
    {
        return new self(
            "Workflow \"{$workflow->code}\" cannot be published: it has no Steps.",
            'workflow.no_steps',
            ['workflow_id' => $workflow->id],
        );
    }
}
