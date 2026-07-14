<?php

declare(strict_types=1);

namespace App\Models;

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
 * WorkflowTransition Model
 * ==========================================================================
 *
 * Represents a possible transition between two Workflow Steps.
 *
 * A transition never executes itself.
 *
 * The Workflow Engine evaluates all candidate transitions,
 * verifies their conditions,
 * applies priority,
 * then selects ONE transition.
 *
 * This design makes the workflow completely configurable
 * without modifying the source code.
 *
 * --------------------------------------------------------------------------
 * Responsibilities
 * --------------------------------------------------------------------------
 * • Connect two Workflow Steps.
 * • Define execution priority.
 * • Own Transition Conditions.
 * • Be evaluated by WorkflowService.
 *
 * --------------------------------------------------------------------------
 * Business Rules
 * --------------------------------------------------------------------------
 * BR-20 Transitions define the next Step.
 * BR-21 Conditions are evaluated before execution.
 * BR-22 Only one Transition may execute.
 * BR-23 Priority resolves conflicts.
 * BR-27 Validators are configured by administrators.
 * BR-58 Business logic is database driven.
 * BR-59 Workflow Engine determines the next validator.
 * BR-60 Generic and reusable architecture.
 *
 * --------------------------------------------------------------------------
 * Module
 * --------------------------------------------------------------------------
 * Workflow Engine
 * ==========================================================================
 */

class WorkflowTransition extends Model
{
    use HasFactory;
    use SoftDeletes;

    /*-------------------------------------------------------------------------
    | Mass Assignment
    |------------------------------------------------------------------------*/

    protected $fillable = [

        'workflow_id',

        'current_step_id',

        'next_step_id',

        'name',

        'description',

        'priority',

        'is_default',

        'is_active',

    ];

    /*-------------------------------------------------------------------------
    | Attribute Casting
    |------------------------------------------------------------------------*/

    protected function casts(): array
    {
        return [

            'priority' => 'integer',

            'is_default' => 'boolean',

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
     * Workflow owning this transition.
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    /**
     * Current Workflow Step.
     */
    public function currentStep(): BelongsTo
    {
        return $this->belongsTo(
            WorkflowStep::class,
            'current_step_id'
        );
    }

    /**
     * Destination Workflow Step.
     */
    public function nextStep(): BelongsTo
    {
        return $this->belongsTo(
            WorkflowStep::class,
            'next_step_id'
        );
    }

    /**
     * Conditions attached to this transition.
     */
    public function transitionConditions(): HasMany
    {
        return $this->hasMany(TransitionCondition::class)
                    ->orderBy('execution_order');
    }

    /*-------------------------------------------------------------------------
    | Query Scopes
    |------------------------------------------------------------------------*/

    /**
     * Active transitions.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Default transition.
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    /**
     * Order by priority.
     */
    public function scopeByPriority(Builder $query): Builder
    {
        return $query->orderBy('priority');
    }

    /*-------------------------------------------------------------------------
    | Helper Methods
    |------------------------------------------------------------------------*/

    /**
     * Determine whether this transition is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Determine whether this transition is the default one.
     */
    public function isDefault(): bool
    {
        return $this->is_default;
    }
}