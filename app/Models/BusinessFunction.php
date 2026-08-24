<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ==========================================================================
 * BusinessFunction Model
 * ==========================================================================
 *
 * Represents the business responsibility assigned to a User.
 *
 * A Business Function describes WHAT a user does inside the
 * organization independently from:
 *
 * - Department
 * - Application Role
 * - Workflow Permissions
 *
 * Examples
 * --------
 * - Commercial
 * - Credit Client
 * - DAF
 * - DG
 * - Accounting
 *
 * Responsibilities
 * ----------------
 * - Defines the user's business responsibility.
 * - Groups Users sharing the same business function.
 * - Standardizes organizational responsibilities.
 *
 * Business Rules
 * --------------
 * BR-05
 * Every User has exactly one Business Function.
 *
 * BR-53
 * Administrators configure Business Functions without
 * modifying the application code.
 *
 * Model Type
 * ----------
 * Master Configuration
 *
 * Module
 * ------
 * Organization Configuration
 * ==========================================================================
 */
class BusinessFunction extends Model
{
    use HasFactory;

    /*-------------------------------------------------------------------------
    | Mass Assignment
    |------------------------------------------------------------------------*/

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    /*-------------------------------------------------------------------------
    | Hidden Attributes
    |------------------------------------------------------------------------*/

    protected $hidden = [];

    /*-------------------------------------------------------------------------
    | Attribute Casting
    |------------------------------------------------------------------------*/

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /*-------------------------------------------------------------------------
    | Organization Relationships
    |------------------------------------------------------------------------*/

    /**
     * Users assigned to this Business Function.
     *
     * Business Rule:
     * BR-05 - Every User has exactly one Business Function.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /*-------------------------------------------------------------------------
    | Workflow Relationships
    |------------------------------------------------------------------------*/
    // None

    /*-------------------------------------------------------------------------
    | Runtime Relationships
    |------------------------------------------------------------------------*/
    // None

    /*-------------------------------------------------------------------------
    | Query Scopes
    |------------------------------------------------------------------------*/

    /**
     * Scope a query to active Business Functions.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to archived Business Functions.
     */
    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /*-------------------------------------------------------------------------
    | Helper Methods
    |------------------------------------------------------------------------*/

    /**
     * Determine whether the Business Function is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Determine whether the Business Function is archived.
     */
    public function isArchived(): bool
    {
        return ! $this->is_active;
    }
}