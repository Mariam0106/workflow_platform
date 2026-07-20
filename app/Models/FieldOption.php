<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ==========================================================================
 * DYNAMIC CONFIGURATION MODEL
 * ==========================================================================
 *
 * FieldOption Model
 * ==========================================================================
 *
 * One selectable option of a Select / Radio / Checkbox / MultiSelect
 * Form Field. Replaces the previous free-text JSON "options" column on
 * form_fields : an administrator can enable/disable, reorder, or add an
 * option without a code change (BR-56/57), which a JSON blob does not
 * allow cleanly.
 *
 * --------------------------------------------------------------------------
 * Business Rules
 * --------------------------------------------------------------------------
 * • A FieldOption belongs to exactly one FormField.
 * • A FormField may have several FieldOptions.
 * • FieldOptions are only meaningful for select/radio/checkbox/multiselect
 *   Form Fields - enforced at the application layer (Etape 12 Form
 *   Requests), not at the database level.
 * • At most one FieldOption per FormField should be marked is_default
 *   (enforced at the application layer - see FieldOption::markAsDefault()).
 * ==========================================================================
 */
class FieldOption extends Model
{
    use HasFactory;

    protected $fillable = [

        'form_field_id',

        'value',

        'label',

        'display_order',

        'is_default',

        'is_active',

        'created_by',

        'updated_by',

    ];

    protected function casts(): array
    {
        return [

            'display_order' => 'integer',

            'is_default' => 'boolean',

            'is_active' => 'boolean',

            'created_at' => 'datetime',

            'updated_at' => 'datetime',

        ];
    }

    /*-------------------------------------------------------------------------
    | Relationships
    |------------------------------------------------------------------------*/

    public function formField(): BelongsTo
    {
        return $this->belongsTo(FormField::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /*-------------------------------------------------------------------------
    | Query Scopes
    |------------------------------------------------------------------------*/

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('display_order');
    }

    /*-------------------------------------------------------------------------
    | Helper Methods
    |------------------------------------------------------------------------*/

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isDefault(): bool
    {
        return $this->is_default;
    }

    /**
     * Mark this option as the default for its Form Field, and unmark
     * every other option of the same field - guarantees at most one
     * default per FormField (a DB-level constraint cannot express this
     * cleanly, so it is enforced here).
     */
    public function markAsDefault(): void
    {
        static::where('form_field_id', $this->form_field_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }
}
