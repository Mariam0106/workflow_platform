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
 * FormCategory Model
 * ==========================================================================
 *
 * Represents a business category used to organize dynamic Forms.
 *
 * Examples
 * --------
 * - Customer Management
 * - Human Resources
 * - Finance
 * - Purchasing
 *
 * Every Form belongs to exactly one Form Category.
 *
 * --------------------------------------------------------------------------
 * Responsibilities
 * --------------------------------------------------------------------------
 * • Organize Forms.
 * • Improve administration.
 * • Simplify Form search.
 * • Separate business domains.
 *
 * --------------------------------------------------------------------------
 * Business Rules
 * --------------------------------------------------------------------------
 * BR-10 Every Form belongs to one Category.
 * BR-53 Categories are configurable by Administrators.
 * BR-54 Categories cannot be deleted if historical data exists.
 *
 * --------------------------------------------------------------------------
 * Module
 * --------------------------------------------------------------------------
 * Dynamic Form Builder
 * ==========================================================================
 */
class FormCategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    /*-------------------------------------------------------------------------
    | Mass Assignment
    |------------------------------------------------------------------------*/

    protected $fillable = [

        'code',
        'name',
        'description',
        'icon',
        'display_order',
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

            'is_active' => 'boolean',

            'created_at' => 'datetime',

            'updated_at' => 'datetime',

            'deleted_at' => 'datetime',

        ];
    }

    /*-------------------------------------------------------------------------
    | Form Builder Relationships
    |------------------------------------------------------------------------*/

    /**
     * Forms belonging to this category.
     */
    public function forms(): HasMany
    {
        return $this->hasMany(Form::class);
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