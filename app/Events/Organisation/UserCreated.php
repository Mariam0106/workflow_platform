<?php

declare(strict_types=1);

namespace App\Events\Organisation;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * ==========================================================================
 * UserCreated
 * ==========================================================================
 *
 * Fired once a User has been successfully persisted - by UserService,
 * never by the Repository directly (a Repository is a pure data-access
 * layer, it doesn't know about domain events).
 *
 * Fired for BOTH creation paths (self-registration via Jalon J1, and
 * Admin-initiated creation - Étape 13) since both go through
 * UserService::createUser() internally.
 *
 * Consumers (Étape 9, Workflow side - Lali) : typically a welcome email
 * Listener, and/or an audit log entry. This class only carries data, it
 * has no opinion on what happens next.
 * ==========================================================================
 */
class UserCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly User $user,
    ) {}
}
