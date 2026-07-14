<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * ==========================================================================
 * MASTER CONFIGURATION MODEL
 * ==========================================================================
 *
 * WorkflowCategory
 *
 * ==========================================================================
 *
 * Represents a business classification used to organize Workflow
 * definitions inside the Workflow Engine.
 *
 * A Workflow Category groups Workflows that belong to the same
 * business domain.
 *
 * Examples
 * --------
 * • Customer Management
 * • Human Resources
 * • Finance
 * • Purchasing
 *
 * The category has no execution logic.
 * It exists only to organize Workflow definitions.
 *
 * --------------------------------------------------------------------------
 * Responsibilities
 * --------------------------------------------------------------------------
 * • Organize Workflow definitions.
 * • Improve administration.
 * • Simplify Workflow discovery.
 * • Separate business domains.
 *
 * --------------------------------------------------------------------------
 * Business Rules
 * --------------------------------------------------------------------------
 * BR-24 Workflow supports versioning.
 *
 * BR-53 Administrators configure Workflows.
 *
 * BR-57 New Workflows can be added without changing source code.
 *
 * --------------------------------------------------------------------------
 * Module
 * --------------------------------------------------------------------------
 * Workflow Engine
 *
 * ==========================================================================
 */

class WorkflowCategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    /*-------------------------------------------------------------------------
    | Mass Assignment
    |------------------------------------------------------------------------*/

    protected $fillable = [

        'name',
        'description',
        'is_active',

    ];

    /*-------------------------------------------------------------------------
    | Attribute Casting
    |------------------------------------------------------------------------*/

    protected function casts(): array
    {
        return [

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
     * Workflows belonging to this category.
     */
    public function workflows(): HasMany
    {
        return $this->hasMany(Workflow::class);
    }

    /*-------------------------------------------------------------------------
    | Query Scopes
    |------------------------------------------------------------------------*/

    /**
     * Active categories.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Archived categories.
     */
    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /*-------------------------------------------------------------------------
    | Helper Methods
    |------------------------------------------------------------------------*/

    /**
     * Determine whether the category is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Determine whether the category is archived.
     */
    public function isArchived(): bool
    {
        return ! $this->is_active;
    }
}