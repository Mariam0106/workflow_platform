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
 * FormField Model
 * ==========================================================================
 *
 * Represents one configurable field belonging to a Form.
 *
 * A FormField describes WHAT information must be collected,
 * not the value entered by users.
 *
 * Values are stored separately in RequestValue.
 *
 * Example
 * -------
 *
 * Client Name
 * Amount
 * Country
 * Opening Date
 * Attachment
 *
 * --------------------------------------------------------------------------
 * Responsibilities
 * --------------------------------------------------------------------------
 * • Belong to one Form.
 * • Define one input element.
 * • Configure validation.
 * • Define display order.
 * • Generate Request Values.
 *
 * --------------------------------------------------------------------------
 * Business Rules
 * --------------------------------------------------------------------------
 * BR-13 Every Form contains at least one Field.
 * BR-14 Technical names are unique inside one Form.
 * BR-53 Configured by Administrators.
 * BR-56 Dynamic Forms.
 * BR-57 No source-code modification.
 * BR-58 Metadata-driven platform.
 * BR-60 Generic architecture.
 *
 * --------------------------------------------------------------------------
 * Module
 * --------------------------------------------------------------------------
 * Dynamic Form Builder
 * ==========================================================================
 */

class FormField extends Model
{
    use HasFactory;
    use SoftDeletes;

    /*-------------------------------------------------------------------------
    | Mass Assignment
    |------------------------------------------------------------------------*/

    protected $fillable = [

        'form_id',

        'label',

        'technical_name',

        'field_type',

        'placeholder',

        'default_value',

        'validation_rules',

        'display_order',

        'is_required',

        'is_active',

    ];

    /*-------------------------------------------------------------------------
    | Attribute Casting
    |------------------------------------------------------------------------*/

    protected function casts(): array
    {
        return [

            'display_order' => 'integer',

            'is_required' => 'boolean',

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
     * Form owning this field.
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    /*-------------------------------------------------------------------------
    | Runtime Relationships
    |------------------------------------------------------------------------*/

    /**
     * Values entered for this field.
     */
    public function requestValues(): HasMany
    {
        return $this->hasMany(RequestValue::class);
    }

    /**
     * Selectable options for this field (Select/Radio/Checkbox/
     * MultiSelect - replaces the previous JSON "options" column).
     */
    public function fieldOptions(): HasMany
    {
        return $this->hasMany(FieldOption::class)->orderBy('display_order');
    }

    /*-------------------------------------------------------------------------
    | Query Scopes
    |------------------------------------------------------------------------*/

    /**
     * Active fields.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Required fields.
     */
    public function scopeRequired(Builder $query): Builder
    {
        return $query->where('is_required', true);
    }

    /**
     * Ordered by display order.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('display_order');
    }

    /*-------------------------------------------------------------------------
    | Helper Methods
    |------------------------------------------------------------------------*/

    /**
     * Determine whether the field is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Determine whether the field is required.
     */
    public function isRequired(): bool
    {
        return $this->is_required;
    }

    /**
     * Determine whether the field is a select list.
     */
    public function isSelect(): bool
    {
        return $this->field_type === 'select';
    }

    /**
     * Determine whether the field is a file upload.
     */
    public function isFile(): bool
    {
        return $this->field_type === 'file';
    }

    /**
     * Determine whether the field is numeric.
     */
    public function isNumber(): bool
    {
        return $this->field_type === 'number';
    }

    /**
     * Determine whether the field is a date.
     */
    public function isDate(): bool
    {
        return $this->field_type === 'date';
    }

    /**
     * Determine whether the field is textual.
     */
    public function isText(): bool
    {
        return in_array($this->field_type, [
            'text',
            'textarea',
            'email',
            'password',
        ]);
    }
}