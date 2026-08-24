<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\ApplicationRole;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * ==========================================================================
 * ActiveRoleController
 * ==========================================================================
 *
 * BR-06 : lets a multi-Role User pick which of their authorized
 * Application Roles is active for the current session - the equivalent
 * of a Microsoft 365 / Azure "switch role" picker.
 *
 * Deliberately a single-purpose Controller, not folded into
 * UserController : this action is about the CURRENT session of the
 * CURRENT User, never about managing another User's account, so it has
 * no Policy of its own - `$user->setActiveApplicationRole()` already
 * enforces "only among your own authorized Roles".
 * ==========================================================================
 */
class ActiveRoleController extends Controller
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
    ) {}

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'application_role_id' => [
                'required',
                'integer',
                Rule::exists(ApplicationRole::class, 'id')->where('is_active', true),
            ],
        ]);

        $role = ApplicationRole::query()->findOrFail($data['application_role_id']);

        // Throws UnauthorizedActionException (-> HTTP 403 via the
        // DomainException render() mapping, Étape 5) if the User is
        // not authorized for this Role - no separate Policy needed.
        $request->user()->setActiveApplicationRole($role);

        // BR-69 : un changement de rôle actif modifie le périmètre
        // d'accès de l'Utilisateur pour le reste de sa session (BR-06) -
        // un événement qu'un Administrateur qui relit le Journal
        // d'Audit doit pouvoir retrouver, particulièrement pour un
        // passage vers "Administrateur".
        $this->auditLogger->log(
            $request->user()->id, 'active_role_switched', 'User', $request->user()->id,
            newValues: ['role' => $role->name],
        );

        return back()->with('status', "Rôle actif changé pour « {$role->name} ».");
    }
}
