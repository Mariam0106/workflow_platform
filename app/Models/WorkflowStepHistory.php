<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ==========================================================================
 * RUNTIME BUSINESS MODEL
 * ==========================================================================
 *
 * WorkflowStepHistory Model
 * ==========================================================================
 *
 * Represents one traversal of a Workflow Step by a Request.
 *
 * Every time a Request enters a Step, a new history row is created.
 * When the Request leaves that Step (via a Transition), the row is
 * closed by setting left_at.
 *
 * This model is purely an audit trail. It never drives workflow logic
 * itself — it only records what the Workflow Engine already decided.
 *
 * --------------------------------------------------------------------------
 * Responsibilities
 * --------------------------------------------------------------------------
 * • Belongs to one Request.
 * • Belongs to one Workflow Step.
 * • Optionally records which Transition caused the move into this Step.
 * • Preserves an immutable execution trail per Request.
 *
 * --------------------------------------------------------------------------
 * Business Rules
 * --------------------------------------------------------------------------
 * BR-25 Existing Requests keep their original Workflow Version — this
 *       history trail lets you reconstruct exactly which Steps a given
 *       Request travelled through, under which Workflow Version.
 * BR-48 Every important action is logged.
 *
 * --------------------------------------------------------------------------
 * Module
 * --------------------------------------------------------------------------
 * Workflow Engine
 * ==========================================================================
 */
class WorkflowStepHistory extends Model
{
    use HasFactory;

    /*-------------------------------------------------------------------------
    | Mass Assignment
    |------------------------------------------------------------------------*/

    protected $fillable = [

        'request_id',

        'workflow_step_id',

        'workflow_transition_id',

        'entered_at',

        'left_at',

    ];

    /*-------------------------------------------------------------------------
    | Attribute Casting
    |------------------------------------------------------------------------*/

    protected function casts(): array
    {
        return [

            'entered_at' => 'datetime',

            'left_at' => 'datetime',

            'created_at' => 'datetime',

            'updated_at' => 'datetime',

        ];
    }

    /*-------------------------------------------------------------------------
    | Runtime Relationships
    |------------------------------------------------------------------------*/

    /**
     * Request that traversed this Step.
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    /**
     * Workflow Step that was traversed.
     */
    public function workflowStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class);
    }

    /**
     * Transition that caused the Request to enter this Step.
     *
     * Nullable: the very first Step of a Workflow is entered directly
     * at submission time, not through a Transition.
     */
    public function workflowTransition(): BelongsTo
    {
        return $this->belongsTo(WorkflowTransition::class);
    }

    /*-------------------------------------------------------------------------
    | Query Scopes
    |------------------------------------------------------------------------*/

    /**
     * History rows still open (the Request is currently on this Step).
     */
    #[Scope]
    protected function current(Builder $query): void
    {
        $query->whereNull('left_at');
    }

    /**
     * History rows already closed (the Request has since moved on).
     */
    #[Scope]
    protected function closed(Builder $query): void
    {
        $query->whereNotNull('left_at');
    }

    /*-------------------------------------------------------------------------
    | Helper Methods
    |------------------------------------------------------------------------*/

    /**
     * Determine whether the Request is still on this Step.
     */
    public function isCurrent(): bool
    {
        return $this->left_at === null;
    }
}