<?php

declare(strict_types=1);

namespace App\Exceptions\Organisation;

/**
 * Thrown when a Department is looked up and does not exist, or when a
 * Department has no manager configured (needed for hierarchical
 * validation routing).
 */
class DepartmentNotFoundException extends OrganisationException
{
    public static function withId(int $id): self
    {
        return new self(
            message: "Département [{$id}] introuvable.",
            errorCode: 'department_not_found',
            status: 404,
            context: ['department_id' => $id],
        );
    }

    public static function managerNotConfigured(int $departmentId): self
    {
        return new self(
            message: "Le département [{$departmentId}] n'a pas de responsable configuré.",
            errorCode: 'department_manager_not_configured',
            status: 422,
            context: ['department_id' => $departmentId],
        );
    }
}
