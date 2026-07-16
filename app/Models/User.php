<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * ==========================================================================
 * User Model
 * ==========================================================================
 *
 * Represents an authenticated employee of the Workflow Platform.
 *
 * The User is the central identity of the application and participates in
 * every business process executed by the Workflow Engine.
 *
 * Every User belongs to exactly one:
 *
 * • Entity
 * • Department
 * • Business Function
 * • Application Role
 *
 * Responsibilities
 * --------------------------------------------------------------------------
 * • Authenticate into the platform.
 * • Create Requests.
 * • Perform Workflow Validations.
 * • Receive Notifications.
 * • Generate Audit Logs.
 * • Participate in Workflow Execution History.
 *
 * Business Rules
 * --------------------------------------------------------------------------
 * BR-03  Every User belongs to exactly one Entity.
 * BR-04  Every User belongs to exactly one Department.
 * BR-05  Every User has exactly one Business Function.
 * BR-06  Every User has exactly one Application Role.
 * BR-07  Only active Users may access the platform.
 * BR-08  Company email is mandatory.
 *
 * Module
 * --------------------------------------------------------------------------
 * Organization
 *
 * ==========================================================================
 */
class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    /*-------------------------------------------------------------------------
    | Mass Assignment
    |------------------------------------------------------------------------*/

    protected $fillable = [

        // Organization

        'entity_id',
        'department_id',
        'business_function_id',
        'application_role_id',

        // Hierarchical manager (N+1)

        'manager_id',

        // Identity

        'first_name',
        'last_name',
        'email',
        'phone',

        // Authentication

        'password',

        // Employee Information

        'employee_number',
        'job_title',

        // Status

        'is_active',
    ];

    /*-------------------------------------------------------------------------
    | Hidden Attributes
    |------------------------------------------------------------------------*/

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*-------------------------------------------------------------------------
    | Attribute Casting
    |------------------------------------------------------------------------*/

    protected function casts(): array
    {
        return [

            'email' => \App\ValueObjects\CompanyEmail::class,

            'phone' => \App\ValueObjects\PhoneNumber::class,

            'password' => 'hashed',

            'is_active' => 'boolean',

            'created_at' => 'datetime',

            'updated_at' => 'datetime',

            'deleted_at' => 'datetime',
        ];
    }

    /*-------------------------------------------------------------------------
    | Accessors
    |------------------------------------------------------------------------*/

    /**
     * User full name.
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => trim($this->first_name . ' ' . $this->last_name)
        );
    }

    /*-------------------------------------------------------------------------
    | Organization Relationships
    |------------------------------------------------------------------------*/

    /**
     * Organization Entity.
     */
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    /**
     * Department.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Business Function.
     */
    public function businessFunction(): BelongsTo
    {
        return $this->belongsTo(BusinessFunction::class);
    }

    /**
     * Application Role.
     */
    public function applicationRole(): BelongsTo
    {
        return $this->belongsTo(ApplicationRole::class);
    }

    /**
     * Direct hierarchical manager (N+1).
     *
     * Nullable: the top of the hierarchy has no manager.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Direct reports of this User (N-1 relationship, inverse of manager()).
     */
    public function subordinates(): HasMany
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    /*-------------------------------------------------------------------------
    | Runtime Relationships
    |------------------------------------------------------------------------*/

    /**
     * Requests created by the User.
     */
    public function requests(): HasMany
    {
        return $this->hasMany(Request::class);
    }

    /**
     * Workflow validations performed by the User.
     */
    public function validations(): HasMany
    {
        return $this->hasMany(Validation::class, 'validator_id');
    }

    /**
     * Notifications received by the User.
     */
    public function notificationsHistory(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /*-------------------------------------------------------------------------
    | Infrastructure Relationships
    |------------------------------------------------------------------------*/

    /**
     * Audit logs generated by the User.
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

    /**
     * Active Users.
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Inactive Users.
     */
    #[Scope]
    protected function inactive(Builder $query): void
    {
        $query->where('is_active', false);
    }

    /*-------------------------------------------------------------------------
    | Helper Methods
    |------------------------------------------------------------------------*/

    /**
     * Determines whether the account is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Determines whether the account is inactive.
     */
    public function isInactive(): bool
    {
        return ! $this->is_active;
    }

    /**
     * Determines whether the User may authenticate.
     */
    public function canLogin(): bool
    {
        return $this->is_active && $this->deleted_at === null;
    }

    /**
     * Determines whether the User owns the specified Application Role.
     *
     * Example:
     * $user->hasRole(\App\Enums\ApplicationRoleCode::Administrator);
     *
     * NOTE (Etape 3) : accepts the ApplicationRoleCode Enum, not a plain
     * string - since Etape 1, ApplicationRole::code is Enum-cast, so a
     * loose string comparison would never match.
     */
    public function hasRole(\App\Enums\ApplicationRoleCode $role): bool
    {
        return $this->applicationRole?->code === $role;
    }

    /**
     * Route notifications for the mail channel.
     *
     * NOTE (Etape 3) : email is now cast to the CompanyEmail Value Object -
     * Notifiable's default routeNotificationForMail() would otherwise hand
     * that object straight to the mailer instead of a plain string.
     */
    public function routeNotificationForMail(): string
    {
        return (string) $this->email;
    }
}