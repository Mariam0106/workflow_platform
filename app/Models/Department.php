<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ==========================================================================
 * Department Model
 * ==========================================================================
 *
 * Represents an organizational department within an Entity.
 *
 * A Department groups users according to their organizational
 * responsibilities (HR, Finance, Sales, IT, etc.).
 *
 * Responsibilities
 * ----------------
 * - Belongs to one Entity.
 * - Groups Users.
 * - Defines the departmental structure.
 *
 * Business Rules
 * --------------
 * BR-02
 * Each Department belongs to exactly one Entity.
 *
 * BR-04
 * Each User belongs to exactly one Department.
 *
 * BR-09
 * Archived Departments cannot receive new Users.
 *
 * Module
 * ------
 * Organization Configuration
 * ==========================================================================
 */
class Department extends Model
{
    use HasFactory;

    /*-------------------------------------------------------------------------
    | Mass Assignment
    |------------------------------------------------------------------------*/

    protected $fillable = [
        'entity_id',
        'manager_id',
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
     * Organizational Entity that owns this Department.
     *
     * Business Rule:
     * BR-02 - Each Department belongs to exactly one Entity.
     */
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    /**
     * Users assigned to this Department.
     *
     * Business Rule:
     * BR-04 - Each User belongs to exactly one Department.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Responsable désigné du Département - permet aux Étapes de
     * Workflow configurées en DEPARTMENT_MANAGER de router vers un
     * vrai Utilisateur (voir OrganisationManagerResolver). Nullable :
     * un Département peut exister sans Responsable désigné encore.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
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
     * Scope a query to active Departments.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to archived Departments.
     */
    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /*-------------------------------------------------------------------------
    | Helper Methods
    |------------------------------------------------------------------------*/

    /**
     * Determine whether the Department is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Determine whether the Department is archived.
     */
    public function isArchived(): bool
    {
        return ! $this->is_active;
    }
}