<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * ==========================================================================
 * DYNAMIC CONFIGURATION MODEL
 * ==========================================================================
 *
 * TransitionCondition Model
 * ==========================================================================
 *
 * Represents a single business condition attached to a Workflow Transition.
 *
 * The Workflow Engine evaluates every condition before deciding whether
 * a transition may be executed.
 *
 * Conditions are completely configurable and stored in the database.
 * No business rule should be hardcoded inside the source code.
 *
 * Example
 * -------
 *
 * field_name  : amount
 * operator    : >=
 * value       : 100000
 *
 * If TRUE
 * ↓
 * Transition may execute.
 *
 * --------------------------------------------------------------------------
 * Responsibilities
 * --------------------------------------------------------------------------
 * • Store one business rule.
 * • Belong to one Workflow Transition.
 * • Be evaluated by WorkflowService.
 * • Support dynamic workflows.
 *
 * --------------------------------------------------------------------------
 * Business Rules
 * --------------------------------------------------------------------------
 * BR-21 Conditions are evaluated before execution.
 * BR-22 Only one Transition executes.
 * BR-23 Transition priority resolves conflicts.
 * BR-58 Business logic is stored in database.
 * BR-59 Workflow Engine determines execution.
 * BR-60 Architecture remains generic.
 *
 * --------------------------------------------------------------------------
 * Module
 * --------------------------------------------------------------------------
 * Workflow Engine
 * ==========================================================================
 */

class TransitionCondition extends Model
{
    use HasFactory;
    use SoftDeletes;

    /*-------------------------------------------------------------------------
    | Mass Assignment
    |------------------------------------------------------------------------*/

    protected $fillable = [

        'workflow_transition_id',

        'field_name',

        'operator',

        'expected_value',

        'logical_operator',

        'execution_order',

        'is_active',

    ];

    /*-------------------------------------------------------------------------
    | Attribute Casting
    |------------------------------------------------------------------------*/

    protected function casts(): array
    {
        return [

            'execution_order' => 'integer',

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
     * Transition owning this condition.
     */
    public function workflowTransition(): BelongsTo
    {
        return $this->belongsTo(WorkflowTransition::class);
    }

    /*-------------------------------------------------------------------------
    | Query Scopes
    |------------------------------------------------------------------------*/

    /**
     * Active conditions.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Ordered execution.
     */
    public function scopeExecutionOrder(Builder $query): Builder
    {
        return $query->orderBy('execution_order');
    }

    /*-------------------------------------------------------------------------
    | Helper Methods
    |------------------------------------------------------------------------*/

    /**
     * Determine whether this condition is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Determine whether the condition uses AND.
     */
    public function usesAnd(): bool
    {
        return strtoupper($this->logical_operator) === 'AND';
    }

    /**
     * Determine whether the condition uses OR.
     */
    public function usesOr(): bool
    {
        return strtoupper($this->logical_operator) === 'OR';
    }
}