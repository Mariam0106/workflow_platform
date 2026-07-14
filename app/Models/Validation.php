<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ==========================================================================
 * RUNTIME BUSINESS MODEL
 * ==========================================================================
 *
 * Validation Model
 * ==========================================================================
 *
 * Represents one validation decision taken during the execution
 * of a Workflow.
 *
 * A Validation NEVER changes the Request directly.
 *
 * Its responsibility is only to record the validator's decision.
 *
 * The Workflow Engine interprets this decision and determines
 * the next transition.
 *
 * --------------------------------------------------------------------------
 * Responsibilities
 * --------------------------------------------------------------------------
 * • Belongs to one Request.
 * • Belongs to one Workflow Step.
 * • Stores validator decision.
 * • Stores validation timestamp.
 * • Preserves immutable history.
 *
 * --------------------------------------------------------------------------
 * Business Rules
 * --------------------------------------------------------------------------
 * BR-36 Only assigned validator may validate.
 * BR-37 Validation is timestamped.
 * BR-38 Decision = Approve or Reject.
 * BR-39 Reject immediately ends workflow.
 * BR-40 Reject comment mandatory.
 * BR-41 Approval triggers Transition.
 * BR-42 Validation history immutable.
 *
 * --------------------------------------------------------------------------
 * Module
 * --------------------------------------------------------------------------
 * Validation Engine
 * ==========================================================================
 */

class Validation extends Model
{
    use HasFactory;

    /*-------------------------------------------------------------------------
    | Decision Constants
    |------------------------------------------------------------------------*/

    public const APPROVED = 'Approved';

    public const REJECTED = 'Rejected';

    /*-------------------------------------------------------------------------
    | Mass Assignment
    |------------------------------------------------------------------------*/

    protected $fillable = [

        'request_id',

        'workflow_step_id',

        'validator_id',

        'decision',

        'comment',

        'validated_at',

    ];

    /*-------------------------------------------------------------------------
    | Attribute Casting
    |------------------------------------------------------------------------*/

    protected function casts(): array
    {
        return [

            'validated_at' => 'datetime',

            'created_at' => 'datetime',

            'updated_at' => 'datetime',

        ];
    }

    /*-------------------------------------------------------------------------
    | Runtime Relationships
    |------------------------------------------------------------------------*/

    /**
     * Request being validated.
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    /**
     * Workflow step validated.
     */
    public function workflowStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class);
    }

    /*-------------------------------------------------------------------------
    | Organization Relationships
    |------------------------------------------------------------------------*/

    /**
     * User who performed the validation.
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validator_id');
    }

    /*-------------------------------------------------------------------------
    | Query Scopes
    |------------------------------------------------------------------------*/

    /**
     * Approved validations.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('decision', self::APPROVED);
    }

    /**
     * Rejected validations.
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('decision', self::REJECTED);
    }

    /*-------------------------------------------------------------------------
    | Helper Methods
    |------------------------------------------------------------------------*/

    /**
     * Determine whether the validation is approved.
     */
    public function isApproved(): bool
    {
        return $this->decision === self::APPROVED;
    }

    /**
     * Determine whether the validation is rejected.
     */
    public function isRejected(): bool
    {
        return $this->decision === self::REJECTED;
    }

    /**
     * Determine whether a rejection comment exists.
     */
    public function hasComment(): bool
    {
        return filled($this->comment);
    }
}