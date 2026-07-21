<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Organisation\Concerns;

use App\Enums\ApplicationRoleCode;
use Illuminate\Database\Eloquent\Builder;

/**
 * ==========================================================================
 * OrdersByRolePriority
 * ==========================================================================
 *
 * Provides a single, explicit definition of "which Application Role
 * outranks which" for manager-resolution fallbacks (DepartmentRepository
 * and EntityRepository - Jalon J2).
 *
 * Why this exists
 * --------------------------------------------------------------------------
 * `application_roles` has NO priority/level column - only `id`, `code`,
 * `name`. Sorting on `application_role_id` (the previous implementation)
 * silently assumes ADMIN was seeded first, which is an insertion-order
 * accident, not a real hierarchy. If seed order ever changes, the wrong
 * "manager" gets resolved with no error at all - a serious risk for
 * Lali's validator routing (BR-45/46, escalades).
 *
 * This trait makes the ranking explicit, in one place, and generates
 * SQL that works identically on MySQL (used in dev/prod) and SQLite
 * (used by the test suite - see phpunit.xml) - `FIELD()` is MySQL-only,
 * so a portable `CASE WHEN` is used instead.
 * ==========================================================================
 */
trait OrdersByRolePriority
{
    /**
     * Rank, lowest = highest authority. Keep in sync with
     * App\Enums\ApplicationRoleCode - there are only 3 roles (BR-06),
     * so this is intentionally a flat, explicit list rather than a
     * generated one.
     *
     * @var array<string, int>
     */
    private const ROLE_PRIORITY = [
        ApplicationRoleCode::Administrator->value => 0,
        ApplicationRoleCode::Validator->value => 1,
        ApplicationRoleCode::User->value => 2,
    ];

    /**
     * Joins `application_roles` and orders the query so the
     * highest-authority active User comes first. Callers must still
     * scope the query (department_id / entity_id / is_active) before
     * calling this.
     */
    private function orderByRolePriority(Builder $query): Builder
    {
        $cases = collect(self::ROLE_PRIORITY)
            ->map(fn (int $priority, string $code): string => "WHEN '{$code}' THEN {$priority}")
            ->implode(' ');

        return $query
            ->join('application_roles', 'users.application_role_id', '=', 'application_roles.id')
            ->select('users.*')
            ->orderByRaw("CASE application_roles.code {$cases} ELSE 99 END");
    }
}
