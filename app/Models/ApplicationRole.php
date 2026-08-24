<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ==========================================================================
 * ApplicationRole Model
 * ==========================================================================
 *
 * Represents an application access role assigned to a User.
 *
 * An Application Role defines WHAT a user is allowed to do
 * inside the platform independently from:
 *
 * - Department
 * - Business Function
 * - Organizational Position
 *
 * Examples
 * --------
 * - Administrator
 * - Validator
 * - User
 *
 * Responsibilities
 * ----------------
 * - Defines the user's application permissions.
 * - Groups Users sharing the same access level.
 * - Supports authorization mechanisms.
 *
 * Business Rules
 * --------------
 * BR-06
 * Every User has exactly one Application Role.
 *
 * BR-52
 * Only Administrators may access the BackOffice.
 *
 * BR-53
 * Administrators configure Application Roles
 * without modifying the application code.
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
class ApplicationRole extends Model
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
            'code' => \App\Enums\ApplicationRoleCode::class,
            'is_active' => 'boolean',
        ];
    }

    /*-------------------------------------------------------------------------
    | Organization Relationships
    |------------------------------------------------------------------------*/

    /**
     * Users for whom this is the DEFAULT Application Role (BR-06).
     * See `assignedUsers()` for every User authorized to hold this Role
     * (a superset - a Role can be a User's default AND appear again in
     * that same User's `assignedUsers()` list, they refer to the same
     * relationship from two angles).
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'default_application_role_id');
    }

    /**
     * Every User authorized to hold this Application Role (BR-06),
     * regardless of whether it is their default Role or their active
     * Role for the current session.
     */
    public function assignedUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'user_application_roles',
        )->withTimestamps();
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
     * Scope a query to active Application Roles.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to archived Application Roles.
     */
    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /*-------------------------------------------------------------------------
    | Helper Methods
    |------------------------------------------------------------------------*/

    /**
     * Determine whether the Application Role is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Determine whether the Application Role is archived.
     */
    public function isArchived(): bool
    {
        return ! $this->is_active;
    }
}