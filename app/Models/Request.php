<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RequestStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * ==========================================================================
 * RUNTIME BUSINESS MODEL
 * ==========================================================================
 *
 * Request Model
 * ==========================================================================
 *
 * Represents one execution of a business process.
 *
 * A Request is created from a published Form and follows
 * one Workflow until completion or rejection.
 *
 * The Request stores only process metadata.
 *
 * Dynamic field values are stored separately
 * inside RequestValue.
 *
 * --------------------------------------------------------------------------
 * Responsibilities
 * --------------------------------------------------------------------------
 * • Created from one Form.
 * • Submitted by one User.
 * • Executes one Workflow.
 * • Stores Workflow Version.
 * • Owns Request Values.
 * • Owns Validations.
 * • Owns Attachments.
 * • Generates Notifications.
 * • Generates Audit Logs.
 *
 * --------------------------------------------------------------------------
 * Business Rules
 * --------------------------------------------------------------------------
 * BR-28 One Request originates from one Published Form.
 * BR-29 Unique Reference Number.
 * BR-30 Drafts are editable.
 * BR-31 Submitted Requests become read-only.
 * BR-32 Rejected Requests cannot be modified.
 * BR-33 Returned for correction is not supported.
 * BR-34 Stores Workflow Version.
 * BR-35 Executes exactly one Workflow.
 *
 * --------------------------------------------------------------------------
 * Module
 * --------------------------------------------------------------------------
 * Runtime Engine
 * ==========================================================================
 */

class Request extends Model
{
    use HasFactory;
    use SoftDeletes;

    /*-------------------------------------------------------------------------
    | Mass Assignment
    |------------------------------------------------------------------------*/

    protected $fillable = [

        'reference_number',

        'form_id',

        'workflow_id',

        'workflow_version',

        'requester_id',

        'current_step_id',

        'status',

        'submitted_at',

        'completed_at',

    ];

    /*-------------------------------------------------------------------------
    | Attribute Casting
    |------------------------------------------------------------------------*/

    protected function casts(): array
    {
        return [

            'reference_number' => \App\ValueObjects\RequestReference::class,

            'workflow_version' => 'integer',

            'status' => \App\Enums\RequestStatus::class,

            'submitted_at' => 'datetime',

            'completed_at' => 'datetime',

            'created_at' => 'datetime',

            'updated_at' => 'datetime',

            'deleted_at' => 'datetime',

        ];
    }

    /*-------------------------------------------------------------------------
    | Configuration Relationships
    |------------------------------------------------------------------------*/

    /**
     * Form used to create this Request.
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * Workflow executed by this Request.
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    /**
     * Current workflow step.
     */
    public function currentStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'current_step_id');
    }

    /*-------------------------------------------------------------------------
    | Organization Relationships
    |------------------------------------------------------------------------*/

    /**
     * User who submitted the Request.
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    /*-------------------------------------------------------------------------
    | Runtime Relationships
    |------------------------------------------------------------------------*/

    /**
     * Dynamic values entered by the requester.
     */
    public function requestValues(): HasMany
    {
        return $this->hasMany(RequestValue::class);
    }

    /**
     * Validation history.
     */
    public function validations(): HasMany
    {
        return $this->hasMany(Validation::class);
    }

    /**
     * Attached documents.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    /**
     * Notifications generated for this Request.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Audit trail.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Workflow execution history.
     */
    public function workflowStepHistories(): HasMany
    {
        return $this->hasMany(WorkflowStepHistory::class);
    }

    /*-------------------------------------------------------------------------
    | Query Scopes
    |------------------------------------------------------------------------*/

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', RequestStatus::Draft);
    }

    public function scopeSubmitted(Builder $query): Builder
    {
        return $query->where('status', RequestStatus::Submitted);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'Approved');
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', RequestStatus::Rejected);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', RequestStatus::Completed);
    }

    /*-------------------------------------------------------------------------
    | Helper Methods
    |------------------------------------------------------------------------*/

    public function isDraft(): bool
    {
        return $this->status === RequestStatus::Draft;
    }

    public function isSubmitted(): bool
    {
        return $this->status === RequestStatus::Submitted;
    }

    public function isRejected(): bool
    {
        return $this->status === RequestStatus::Rejected;
    }

    public function isCompleted(): bool
    {
        return $this->status === RequestStatus::Completed;
    }

    public function isEditable(): bool
    {
        return $this->status === RequestStatus::Draft;
    }
}