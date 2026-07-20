<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ValidatorType;
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
 * WorkflowStep Model
 * ==========================================================================
 *
 * Represents one validation step inside a Workflow.
 *
 * A Workflow Step defines:
 *
 * • the validator (via validator_type + validator_reference)
 * • the execution order
 * • whether it is the first step
 * • whether it is the last step
 *
 * It NEVER determines the next step.
 *
 * Navigation between steps is performed exclusively through
 * WorkflowTransition.
 *
 * --------------------------------------------------------------------------
 * Responsibilities
 * --------------------------------------------------------------------------
 * • Belongs to one Workflow.
 * • Owns outgoing Transitions.
 * • Receives incoming Transitions.
 * • Generates Validations.
 * • Generates Workflow History.
 *
 * --------------------------------------------------------------------------
 * Business Rules
 * --------------------------------------------------------------------------
 * BR-18 Every Workflow contains at least one Step.
 * BR-19 Every Step belongs to one Workflow.
 * BR-20 WorkflowStep never stores the next Step.
 * BR-21 Transition Conditions determine execution.
 * BR-22 Only one Transition is executed.
 * BR-23 Transition priority resolves conflicts.
 * BR-27 Validators are configured by administrators.
 * BR-59 Workflow Engine determines the next validator.
 *
 * --------------------------------------------------------------------------
 * Module
 * --------------------------------------------------------------------------
 * Workflow Engine
 * ==========================================================================
 */

class WorkflowStep extends Model
{
    use HasFactory;
    use SoftDeletes;

    /*-------------------------------------------------------------------------
    | Mass Assignment
    |------------------------------------------------------------------------*/

    protected $fillable = [

        'workflow_id',

        'code',

        'name',

        'description',

        'step_order',

        'is_start',

        'is_end',

        'validator_type',

        'validator_reference',

        'is_active',

        'created_by',
        'updated_by',
    ];

    /*-------------------------------------------------------------------------
    | Attribute Casting
    |------------------------------------------------------------------------*/

    protected function casts(): array
    {
        return [

            'step_order' => 'integer',

            'is_start' => 'boolean',

            'is_end' => 'boolean',

            'validator_type' => ValidatorType::class,

            'validator_reference' => 'integer',

            'is_active' => 'boolean',

            'created_at' => 'datetime',

            'updated_at' => 'datetime',

            'deleted_at' => 'datetime',

        ];
    }

    /*-------------------------------------------------------------------------
    | Workflow Relationships
    |------------------------------------------------------------------------*/

    /**
     * Workflow owning this Step.
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    /**
     * Outgoing transitions (this Step is the origin).
     */
    public function outgoingTransitions(): HasMany
    {
        return $this->hasMany(
            WorkflowTransition::class,
            'from_step_id'
        );
    }

    /**
     * Incoming transitions (this Step is the destination).
     */
    public function incomingTransitions(): HasMany
    {
        return $this->hasMany(
            WorkflowTransition::class,
            'to_step_id'
        );
    }

    /*-------------------------------------------------------------------------
    | Runtime Relationships
    |------------------------------------------------------------------------*/

    /**
     * Validations performed on this Step.
     */
    public function validations(): HasMany
    {
        return $this->hasMany(Validation::class);
    }

    /**
     * Workflow execution history.
     */
    public function workflowStepHistories(): HasMany
    {
        return $this->hasMany(WorkflowStepHistory::class);
    }

    /*-------------------------------------------------------------------------
    | Query Scopes
    |------------------------------------------------------------------------*/

    /**
     * Active Steps.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Start Step.
     */
    public function scopeStart(Builder $query): Builder
    {
        return $query->where('is_start', true);
    }

    /**
     * End Step.
     */
    public function scopeEnd(Builder $query): Builder
    {
        return $query->where('is_end', true);
    }

    /*-------------------------------------------------------------------------
    | Helper Methods
    |------------------------------------------------------------------------*/

    /**
     * Determine whether this is the first Step.
     */
    public function isStart(): bool
    {
        return $this->is_start;
    }

    /**
     * Determine whether this is the final Step.
     */
    public function isEnd(): bool
    {
        return $this->is_end;
    }

    /**
     * Determine whether this Step is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * User who created this configuration record.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User who last modified this configuration record.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

}
