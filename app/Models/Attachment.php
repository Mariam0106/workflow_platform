<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ==========================================================================
 * INFRASTRUCTURE MODEL
 * ==========================================================================
 *
 * Attachment Model
 * ==========================================================================
 *
 * Represents one document uploaded during the execution
 * of a Request.
 *
 * Attachments provide supporting evidence for business
 * processes and remain linked to the Request throughout
 * its lifecycle.
 *
 * Uploaded files are immutable once the Request leaves
 * Draft status.
 *
 * --------------------------------------------------------------------------
 * Responsibilities
 * --------------------------------------------------------------------------
 * • Belongs to one Request.
 * • Stores uploaded file metadata.
 * • Preserves business evidence.
 * • Supports historical traceability.
 *
 * --------------------------------------------------------------------------
 * Business Rules
 * --------------------------------------------------------------------------
 * BR-28 Attachment belongs to one Request.
 * BR-31 Submitted Requests become read-only.
 * BR-48 Business evidence is retained.
 * BR-49 Historical information is immutable.
 *
 * --------------------------------------------------------------------------
 * Module
 * --------------------------------------------------------------------------
 * Infrastructure
 * ==========================================================================
 */

class Attachment extends Model
{
    use HasFactory;

    /*-------------------------------------------------------------------------
    | Mass Assignment
    |------------------------------------------------------------------------*/

    protected $fillable = [

        'request_id',

        'original_name',

        'stored_name',

        'storage_path',

        'mime_type',

        'extension',

        'size',

        'uploaded_by',

    ];

    /*-------------------------------------------------------------------------
    | Attribute Casting
    |------------------------------------------------------------------------*/

    protected function casts(): array
    {
        return [

            'size' => 'integer',

            'created_at' => 'datetime',

            'updated_at' => 'datetime',

        ];
    }

    /*-------------------------------------------------------------------------
    | Runtime Relationships
    |------------------------------------------------------------------------*/

    /**
     * Request owning this attachment.
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    /*-------------------------------------------------------------------------
    | Organization Relationships
    |------------------------------------------------------------------------*/

    /**
     * User who uploaded the document.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /*-------------------------------------------------------------------------
    | Helper Methods
    |------------------------------------------------------------------------*/

    /**
     * Returns true if the attachment is a PDF.
     */
    public function isPdf(): bool
    {
        return strtolower($this->extension) === 'pdf';
    }

    /**
     * Returns true if the attachment is an image.
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Returns the file size in megabytes.
     */
    public function sizeInMb(): float
    {
        return round($this->size / 1024 / 1024, 2);
    }

    /**
     * Returns the complete storage location.
     */
    public function fullPath(): string
    {
        return $this->storage_path . '/' . $this->stored_name;
    }
}