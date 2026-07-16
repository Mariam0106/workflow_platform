<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\WorkflowStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * ==========================================================================
 * DYNAMIC CONFIGURATION MODEL
 * ==========================================================================
 *
 * Workflow Model
 * ==========================================================================
 *
 * Represents a configurable business workflow executed by the Workflow Engine.
 *
 * A Workflow defines:
 *
 * • the approval process
 * • validation sequence
 * • business transitions
 * • execution logic
 *
 * The Workflow itself never executes business logic.
 * It stores only the configuration.
 *
 * Execution is delegated to WorkflowService.
 *
 * --------------------------------------------------------------------------
 * Responsibilities
 * --------------------------------------------------------------------------
 * • Define a business process.
 * • Own Workflow Steps.
 * • Own Workflow Transitions.
 * • Be assigned to Forms.
 * • Be referenced by Requests.
 * • Support versioning.
 * • Support publication lifecycle.
 *
 * --------------------------------------------------------------------------
 * Business Rules
 * --------------------------------------------------------------------------
 * BR-11  A Form uses exactly one Workflow.
 * BR-12  A Workflow may be reused by multiple Forms.
 * BR-18  Every Workflow contains at least one Step.
 * BR-24  Workflows support versioning.
 * BR-25  Running Requests keep their Workflow version.
 * BR-26  Published Workflows cannot be modified.
 * BR-27  Validators are configured, not hardcoded.
 * BR-57  New Workflows require no code modification.
 * BR-58  Business logic is database driven.
 * BR-59  Next validator is determined dynamically.
 * BR-60  Architecture must remain generic.
 *
 * --------------------------------------------------------------------------
 * Module
 * --------------------------------------------------------------------------
 * Workflow Engine
 * ==========================================================================
 */

class Workflow extends Model
{
    use HasFactory;
    use SoftDeletes;

    /*-------------------------------------------------------------------------
    | Mass Assignment
    |------------------------------------------------------------------------*/

    protected $fillable = [

        'workflow_category_id',

        'code',

        'name',

        'description',

        'version',

        'status',

        'published_at',

        'is_default',

        'is_active',

    ];

    /*-------------------------------------------------------------------------
    | Attribute Casting
    |------------------------------------------------------------------------*/

    protected function casts(): array
    {
        return [

            'version' => 'integer',

            'status' => WorkflowStatus::class,

            'is_default' => 'boolean',

            'is_active' => 'boolean',

            'published_at' => 'datetime',

            'created_at' => 'datetime',

            'updated_at' => 'datetime',

            'deleted_at' => 'datetime',

        ];
    }

    /*-------------------------------------------------------------------------
    | Configuration Relationships
    |------------------------------------------------------------------------*/

    /**
     * Category owning this Workflow.
     */
    public function workflowCategory(): BelongsTo
    {
        return $this->belongsTo(WorkflowCategory::class);
    }

    /**
     * Forms using this Workflow.
     */
    public function forms(): HasMany
    {
        return $this->hasMany(Form::class);
    }

    /**
     * Workflow Steps.
     */
    public function workflowSteps(): HasMany
    {
        return $this->hasMany(WorkflowStep::class)
                    ->orderBy('step_order');
    }

    /*-------------------------------------------------------------------------
    | Runtime Relationships
    |------------------------------------------------------------------------*/

    /**
     * Requests executed using this Workflow.
     */
    public function requests(): HasMany
    {
        return $this->hasMany(Request::class);
    }

    /*-------------------------------------------------------------------------
    | Query Scopes
    |------------------------------------------------------------------------*/

    /**
     * Published Workflows.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', WorkflowStatus::Published);
    }

    /**
     * Draft Workflows.
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', WorkflowStatus::Draft);
    }

    /**
     * Archived Workflows.
     */
    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('status', WorkflowStatus::Archived);
    }

    /**
     * Active Workflows.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /*-------------------------------------------------------------------------
    | Helper Methods
    |------------------------------------------------------------------------*/

    /**
     * Determine whether the Workflow is published.
     */
    public function isPublished(): bool
    {
        return $this->status === WorkflowStatus::Published;
    }

    /**
     * Determine whether the Workflow is a draft.
     */
    public function isDraft(): bool
    {
        return $this->status === WorkflowStatus::Draft;
    }

    /**
     * Determine whether the Workflow is archived.
     */
    public function isArchived(): bool
    {
        return $this->status === WorkflowStatus::Archived;
    }

    /**
     * Determine whether the Workflow is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Return the display version.
     *
     * Example:
     * v1
     * v2
     * v3
     */
    public function displayVersion(): string
    {
        return 'v' . $this->version;
    }
}