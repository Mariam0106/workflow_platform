<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organisation\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organisation\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * ==========================================================================
 * AuthenticatedSessionController
 * ==========================================================================
 *
 * Jalon J1 (auth minimale) - login / logout.
 * ==========================================================================
 */
class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate(); 

        $request->session()->regenerate();

        // AJOUT (post Étape 12, demande client) : un User autorisé pour
        // plusieurs Application Roles (relation N-N) choisit son rôle
        // actif avant de continuer, plutôt que de conserver
        // silencieusement le dernier rôle actif enregistré. L'URL
        // "intended" (si une route protégée avait été visée avant le
        // login) reste en session et sera consommée par
        // RoleSelectionController::store() une fois le rôle choisi.
        if ($request->user()->mustChooseActiveRole()) {
            return redirect()->route('role-selection.create');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
