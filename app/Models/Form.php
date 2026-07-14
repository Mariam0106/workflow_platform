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
 * Form Model
 * ==========================================================================
 *
 * Represents a dynamic business Form configured by administrators.
 *
 * A Form defines:
 *
 * • the information to collect
 * • the Workflow to execute
 * • the dynamic Fields composing the Form
 *
 * Forms contain no business logic.
 * They are metadata interpreted by the Form Engine.
 *
 * --------------------------------------------------------------------------
 * Responsibilities
 * --------------------------------------------------------------------------
 * • Belong to one Form Category.
 * • Use one Workflow.
 * • Own Form Fields.
 * • Generate Requests.
 * • Support publication lifecycle.
 *
 * --------------------------------------------------------------------------
 * Business Rules
 * --------------------------------------------------------------------------
 * BR-10 Form belongs to one Category.
 * BR-11 Form uses one Workflow.
 * BR-12 Workflow reusable by multiple Forms.
 * BR-13 Form contains at least one Field.
 * BR-15 Only Published Forms can be used.
 * BR-16 Archived Forms remain available for historical Requests.
 * BR-17 Duplicating creates a new Form.
 * BR-53 Configured by Administrators.
 * BR-56 New Forms require no database redesign.
 *
 * --------------------------------------------------------------------------
 * Module
 * --------------------------------------------------------------------------
 * Dynamic Form Builder
 * ==========================================================================
 */

class Form extends Model
{
    use HasFactory;
    use SoftDeletes;

    /*-------------------------------------------------------------------------
    | Mass Assignment
    |------------------------------------------------------------------------*/

    protected $fillable = [

        'form_category_id',

        'workflow_id',

        'name',

        'description',

        'version',

        'status',

        'is_active',

    ];

    /*-------------------------------------------------------------------------
    | Attribute Casting
    |------------------------------------------------------------------------*/

    protected function casts(): array
    {
        return [

            'version' => 'integer',

            'is_active' => 'boolean',

            'created_at' => 'datetime',

            'updated_at' => 'datetime',

            'deleted_at' => 'datetime',

        ];
    }

    /*-------------------------------------------------------------------------
    | Configuration Relationships
    |------------------------------------------------------------------------*/

    /**
     * Category owning this Form.
     */
    public function formCategory(): BelongsTo
    {
        return $this->belongsTo(FormCategory::class);
    }

    /**
     * Workflow executed by this Form.
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    /**
     * Dynamic fields composing this Form.
     */
    public function formFields(): HasMany
    {
        return $this->hasMany(FormField::class)
                    ->orderBy('display_order');
    }

    /*-------------------------------------------------------------------------
    | Runtime Relationships
    |------------------------------------------------------------------------*/

    /**
     * Requests created from this Form.
     */
    public function requests(): HasMany
    {
        return $this->hasMany(Request::class);
    }

    /*-------------------------------------------------------------------------
    | Query Scopes
    |------------------------------------------------------------------------*/

    /**
     * Published Forms.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'Published');
    }

    /**
     * Draft Forms.
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'Draft');
    }

    /**
     * Archived Forms.
     */
    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('status', 'Archived');
    }

    /**
     * Active Forms.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /*-------------------------------------------------------------------------
    | Helper Methods
    |------------------------------------------------------------------------*/

    /**
     * Determine whether this Form is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'Published';
    }

    /**
     * Determine whether this Form is a draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'Draft';
    }

    /**
     * Determine whether this Form is archived.
     */
    public function isArchived(): bool
    {
        return $this->status === 'Archived';
    }

    /**
     * Determine whether this Form is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Human-readable version.
     *
     * Example:
     * v1
     * v2
     */
    public function displayVersion(): string
    {
        return 'v' . $this->version;
    }
}