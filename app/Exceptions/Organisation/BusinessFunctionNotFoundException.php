<?php

declare(strict_types=1);

namespace App\Exceptions\Organisation;

/**
 * Thrown when a Business Function is looked up and does not exist, or is
 * archived (BR-09 - mirrors EntityNotFoundException/DepartmentNotFoundException).
 */
class BusinessFunctionNotFoundException extends OrganisationException
{
    public static function withId(int $id): self
    {
        return new self(
            message: "Fonction métier [{$id}] introuvable.",
            errorCode: 'business_function_not_found',
            context: ['business_function_id' => $id],
        );
    }

    public static function archived(int $businessFunctionId): self
    {
        return new self(
            message: "La fonction métier [{$businessFunctionId}] est archivée et ne peut pas être attribuée.",
            errorCode: 'business_function_archived',
            context: ['business_function_id' => $businessFunctionId],
            httpStatus: 422,
        );
    }

    protected function defaultHttpStatus(): int
    {
        return 404;
    }
}
