<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * ==========================================================================
 * ApplicationRoleCode Enum
 * ==========================================================================
 *
 * The 3 fixed Application Roles of the platform (BR-06).
 * A User has exactly one Application Role - never several.
 *
 * These values match application_roles.code exactly.
 */
enum ApplicationRoleCode: string
{
    case Administrator = 'ADMIN';
    case User = 'USER';
    case Validator = 'VALIDATOR';

    public function label(): string
    {
        return match ($this) {
            self::Administrator => 'Administrator',
            self::User => 'User',
            self::Validator => 'Validator',
        };
    }
}
