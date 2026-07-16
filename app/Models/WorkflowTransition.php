<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
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
 * Represents a possible path between two Workflow Steps.
 *
 * A Workflow Transition does not execute itself.
 * It is evaluated by the Workflow Engine, which determines whether the
 * transition can be executed based on its Conditions and Priority.
 *
 * Multiple transitions may exist for the same Workflow Step, but only one
 * transition can be selected during workflow execution.
 *
 * --------------------------------------------------------------------------
 * Responsibilities
 * --------------------------------------------------------------------------
 * • Connect two Workflow Steps (from_step_id -> to_step_id).
 * • Define execution priority.
 * • Own Transition Conditions.
 * • Provide execution metadata to the Workflow Engine.
 *
 * --------------------------------------------------------------------------
 * Business Rules
 * --------------------------------------------------------------------------
 * BR-20 Transitions define the next Workflow Step.
 * BR-21 Conditions are evaluated before execution.
 * BR-22 Only one Transition may be executed.
 * BR-23 Priority resolves transition conflicts.
 * BR-27 Validators are configured through Workflow configuration.
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

        'from_step_id',

        'to_step_id',

        'action_name',

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
     * Workflow Step from which the transition starts.
     */
    public function fromStep(): BelongsTo
    {
        return $this->belongsTo(
            WorkflowStep::class,
            'from_step_id'
        );
    }

    /**
     * Workflow Step reached after execution.
     */
    public function toStep(): BelongsTo
    {
        return $this->belongsTo(
            WorkflowStep::class,
            'to_step_id'
        );
    }

    /**
     * Conditions evaluated before executing this transition.
     */
    public function transitionConditions(): HasMany
    {
        return $this->hasMany(TransitionCondition::class)
                    ->orderBy('execution_order');
    }

    /**
     * Workflow executions that used this transition.
     */
    public function workflowStepHistories(): HasMany
    {
        return $this->hasMany(WorkflowStepHistory::class);
    }

    /*-------------------------------------------------------------------------
    | Query Scopes
    |------------------------------------------------------------------------*/

    /**
     * Active transitions.
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Default transition.
     */
    #[Scope]
    protected function default(Builder $query): void
    {
        $query->where('is_default', true);
    }

    /**
     * Sort transitions by execution priority.
     */
    #[Scope]
    protected function byPriority(Builder $query): void
    {
        $query->orderBy('priority');
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

    /**
     * Determine whether this transition contains execution conditions.
     */
    public function hasConditions(): bool
    {
        return $this->transitionConditions()->exists();
    }
}
