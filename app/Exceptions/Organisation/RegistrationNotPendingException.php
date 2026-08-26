<?php

declare(strict_types=1);

namespace App\Exceptions\Organisation;

use App\Models\User;

/**
 * Thrown when an Administrator tries to approve/reject a registration
 * that isn't Pending anymore (already processed by someone else, or not
 * a self-registration at all).
 */
class RegistrationNotPendingException extends OrganisationException
{
    public static function forUser(User $user): self
    {
        return new self(
            message: "Cette demande d'inscription a déjà été traitée (statut actuel : {$user->registration_status->label()}).",
            errorCode: 'registration_not_pending',
            context: ['user_id' => $user->id, 'registration_status' => $user->registration_status->value],
        );
    }

    protected function defaultHttpStatus(): int
    {
        return 422;
    }
}
