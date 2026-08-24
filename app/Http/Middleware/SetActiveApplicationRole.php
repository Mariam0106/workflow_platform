<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ==========================================================================
 * SetActiveApplicationRole
 * ==========================================================================
 *
 * BR-06 : "Parmi les Rôles Applicatifs autorisés, un seul est défini
 * comme Rôle Applicatif actif pour la session courante de l'Utilisateur."
 *
 * Runs once per request, after the session is available (web
 * middleware group) :
 *
 *  - No active Role in session yet -> seeds it with the User's default
 *    Role, so every controller/policy always has one to read
 *    (User::activeApplicationRole() already falls back to the default
 *    on its own, but seeding it here means the session and the model
 *    agree from the very first request, which matters once the
 *    Blade role-switcher needs to highlight "the" current selection).
 *
 *  - An active Role IS present, but it is no longer one of the User's
 *    authorized Roles (an Administrator revoked it between two
 *    requests of the same session) -> silently falls back to the
 *    default Role rather than leaving a dangling, unauthorized value
 *    in session that `hasRole()` would otherwise keep trusting.
 *
 * Deliberately NOT in charge of authorization itself - it only keeps
 * session state honest; PermissionService/Policies still do the actual
 * gating via User::hasRole().
 * ==========================================================================
 */
class SetActiveApplicationRole
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = $request->user();

        if ($user !== null && $user->relationLoaded('applicationRoles') === false) {
            $user->load('applicationRoles');
        }

        if ($user !== null) {
            $sessionRoleId = $request->session()->get(User::ACTIVE_ROLE_SESSION_KEY);
            $stillAuthorized = $sessionRoleId !== null
                && $user->applicationRoles->contains('id', $sessionRoleId);

            if (! $stillAuthorized && $user->default_application_role_id !== null) {
                $request->session()->put(User::ACTIVE_ROLE_SESSION_KEY, $user->default_application_role_id);
            }
        }

        return $next($request);
    }
}
