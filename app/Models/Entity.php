<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ==========================================================================
 * Entity Model
 * ==========================================================================
 *
 * Represents a legal entity within the organization.
 *
 * An Entity is the highest organizational level of the platform.
 * It typically corresponds to a company, subsidiary, branch,
 * or business unit.
 *
 * Responsibilities
 * ----------------
 * - Owns Departments.
 * - Owns Users.
 * - Owns Workflows.
 * - Defines the organizational boundary.
 *
 * Business Rules
 * --------------
 * BR-01
 * Each Entity represents one company or business unit.
 *
 * BR-02
 * Every Department belongs to exactly one Entity.
 *
 * BR-03
 * Every User belongs to exactly one Entity.
 *
 * BR-09
 * Archived Entities cannot receive new Users.
 *
 * Module
 * ------
 * Organization Configuration
 * ==========================================================================
 */
class Entity extends Model
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
     * Departments belonging to this Entity.
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Users working inside this Entity.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /*-------------------------------------------------------------------------
    | Workflow Relationships
    |------------------------------------------------------------------------*/

    /**
     * Workflows owned by this Entity.
     */
    public function workflows(): HasMany
    {
        return $this->hasMany(Workflow::class);
    }

    /*-------------------------------------------------------------------------
    | Runtime Relationships
    |------------------------------------------------------------------------*/
    // None

    /*-------------------------------------------------------------------------
    | Query Scopes
    |------------------------------------------------------------------------*/

    /**
     * Scope a query to active entities.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to archived entities.
     */
    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /*-------------------------------------------------------------------------
    | Helper Methods
    |------------------------------------------------------------------------*/

    /**
     * Determine whether the Entity is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Determine whether the Entity is archived.
     */
    public function isArchived(): bool
    {
        return ! $this->is_active;
    }
}