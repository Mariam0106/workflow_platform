<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\App;

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
 * BR-06  Every User holds at least one Application Role and may hold
 *        several (see `applicationRoles()`). Exactly one of the User's
 *        authorized Roles is the ACTIVE Role for the current session
 *        (see `activeApplicationRole()` / SetActiveApplicationRole
 *        middleware) - that is what `hasRole()` checks, not "any
 *        assigned Role" (use `hasAssignedRole()` for that).
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
        'default_application_role_id',

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
     * Default Application Role.
     *
     * Proposed as the active Role on first login and used as a
     * non-nullable fallback (BR-06) - NOT necessarily the Role active
     * for the current session once the User has switched (see
     * `activeApplicationRole()`).
     *
     * NOTE : method kept named `applicationRole()` (not
     * `defaultApplicationRole()`) on purpose - every existing view/
     * controller written before multi-role support (BR-06) already
     * calls `->applicationRole`; renaming the relation would silently
     * break all of them for no behavioural gain.
     */
    public function applicationRole(): BelongsTo
    {
        return $this->belongsTo(ApplicationRole::class, 'default_application_role_id');
    }

    /**
     * All Application Roles this User is authorized to hold (BR-06).
     * Always contains at least one Role (the default one, at minimum).
     */
    public function applicationRoles(): BelongsToMany
    {
        return $this->belongsToMany(
            ApplicationRole::class,
            'user_application_roles',
        )->withTimestamps();
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
        return $this->hasMany(Notification::class, 'recipient_id');
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
     * The session key under which the ACTIVE Application Role's id is
     * stored (BR-06 : "Parmi les Rôles Applicatifs autorisés, un seul
     * est défini comme Rôle Applicatif actif pour la session courante").
     *
     * Deliberately session state, not a database column : it is
     * per-session by definition, and every Policy/PermissionService
     * check must reflect whatever the User is "acting as" right now,
     * not a persisted, sticky value.
     */
    public const ACTIVE_ROLE_SESSION_KEY = 'active_application_role_id';

    /**
     * Determines whether the User's ACTIVE Role for the current session
     * is the specified Application Role. This is what every Policy /
     * PermissionService authorization check uses (BR-06) - a User who
     * holds "Validator" among their authorized Roles but is currently
     * acting as "User" does NOT pass `hasRole(Validator)` until they
     * switch (see `setActiveApplicationRole()`).
     *
     * Falls back to the default Role whenever there is no HTTP session
     * to read from (queued Jobs, Artisan, tests that never switched
     * role) - this keeps every pre-multi-role caller of hasRole()
     * working unmodified for single-role Users.
     *
     * To check "does this User hold this Role at all, regardless of
     * what they're currently acting as" (e.g. building the role
     * switcher menu, or resolving eligible Validators for a
     * notification), use `hasAssignedRole()` instead.
     *
     * Example:
     * $user->hasRole(\App\Enums\ApplicationRoleCode::Administrator);
     */
    public function hasRole(\App\Enums\ApplicationRoleCode $role): bool
    {
        return $this->activeRoleCode() === $role;
    }

    /**
     * Determines whether the User is AUTHORIZED to hold the specified
     * Application Role (BR-06), independently of which Role is active
     * for the current session.
     */
    public function hasAssignedRole(\App\Enums\ApplicationRoleCode $role): bool
    {
        return $this->applicationRoles->contains(
            fn (ApplicationRole $assigned): bool => $assigned->code === $role,
        );
    }

    /**
     * The ApplicationRoleCode active for the current session, falling
     * back to the default Role when no session is available or no
     * active Role has been chosen yet.
     */
    public function activeRoleCode(): ?\App\Enums\ApplicationRoleCode
    {
        return $this->activeApplicationRole()?->code;
    }

    /**
     * The ApplicationRole model active for the current session.
     *
     * SetActiveApplicationRole middleware guarantees that whenever a
     * session value is present, it always points to one of the User's
     * authorized Roles - this method re-checks that anyway (defense in
     * depth for any caller that runs before the middleware, e.g. a Job
     * that hydrated the session manually).
     */
    public function activeApplicationRole(): ?ApplicationRole
    {
        $sessionRoleId = App::bound('session') && App::make('session')->isStarted()
            ? App::make('session')->get(self::ACTIVE_ROLE_SESSION_KEY)
            : null;

        if ($sessionRoleId !== null) {
            $active = $this->applicationRoles->firstWhere('id', $sessionRoleId);

            if ($active !== null) {
                return $active;
            }
        }

        return $this->applicationRole;
    }

    /**
     * Switches the ACTIVE Role for the current session (BR-06).
     *
     * @throws \App\Exceptions\Organisation\UnauthorizedActionException if
     *         the requested Role is not one of the User's authorized Roles.
     */
    public function setActiveApplicationRole(ApplicationRole $role): void
    {
        if (! $this->hasAssignedRole($role->code)) {
            throw \App\Exceptions\Organisation\UnauthorizedActionException::requiresRole(
                requiredRole: $role->code->value,
                actingUserId: $this->id,
            );
        }

        App::make('session')->put(self::ACTIVE_ROLE_SESSION_KEY, $role->id);
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