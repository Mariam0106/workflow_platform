<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ==========================================================================
 * RUNTIME BUSINESS MODEL
 * ==========================================================================
 *
 * RequestValue Model
 * ==========================================================================
 *
 * Represents one value entered by a user for one Form Field
 * during the execution of one Request.
 *
 * This model is the cornerstone of the platform's dynamic
 * architecture.
 *
 * Instead of creating database columns for every business form,
 * all user-entered values are stored generically here.
 *
 * Example
 * -------
 *
 * Request #152
 * -------------------------
 * Client Name  -> John Doe
 * Amount       -> 50000
 * Country      -> Morocco
 *
 * Tomorrow a new field is added:
 *
 * Passport Number
 *
 * No database redesign.
 * No source-code modification.
 * Only a new FormField.
 *
 * --------------------------------------------------------------------------
 * Responsibilities
 * --------------------------------------------------------------------------
 * • Belong to one Request.
 * • Belong to one Form Field.
 * • Store one user-entered value.
 * • Preserve historical business data.
 *
 * --------------------------------------------------------------------------
 * Business Rules
 * --------------------------------------------------------------------------
 * BR-13 Form contains dynamic fields.
 * BR-28 Values belong to one Request.
 * BR-30 Draft values remain editable.
 * BR-31 Submitted Requests become read-only.
 * BR-56 Support new Forms without redesign.
 * BR-57 Support new Workflows without code changes.
 * BR-58 Business metadata stored in database.
 * BR-60 Generic reusable architecture.
 *
 * --------------------------------------------------------------------------
 * Module
 * --------------------------------------------------------------------------
 * Runtime Engine
 * ==========================================================================
 */

class RequestValue extends Model
{
    use HasFactory;

    /*-------------------------------------------------------------------------
    | Mass Assignment
    |------------------------------------------------------------------------*/

    protected $fillable = [

        'request_id',

        'form_field_id',

        'value',

    ];

    /*-------------------------------------------------------------------------
    | Attribute Casting
    |------------------------------------------------------------------------*/

    protected function casts(): array
    {
        return [

            'created_at' => 'datetime',

            'updated_at' => 'datetime',

        ];
    }

    /*-------------------------------------------------------------------------
    | Runtime Relationships
    |------------------------------------------------------------------------*/

    /**
     * Request owning this value.
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    /**
     * Form Field describing this value.
     */
    public function formField(): BelongsTo
    {
        return $this->belongsTo(FormField::class);
    }

    /*-------------------------------------------------------------------------
    | Helper Methods
    |------------------------------------------------------------------------*/

    /**
     * Determine whether the value is empty.
     */
    public function isEmpty(): bool
    {
        return blank($this->value);
    }

    /**
     * Return the value as string.
     */
    public function asString(): string
    {
        return (string) $this->value;
    }

    /**
     * Return the value as integer.
     */
    public function asInteger(): int
    {
        return (int) $this->value;
    }

    /**
     * Return the value as float.
     */
    public function asFloat(): float
    {
        return (float) $this->value;
    }

    /**
     * Return the value as boolean.
     */
    public function asBoolean(): bool
    {
        return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
    }
}