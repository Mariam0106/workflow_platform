<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organisation\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organisation\Auth\SelectRoleRequest;
use App\Services\Organisation\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * ==========================================================================
 * RoleSelectionController
 * ==========================================================================
 *
 * AJOUT (post Étape 12, demande client) : un User peut désormais être
 * autorisé pour plusieurs Application Roles (relation N-N, cf.
 * User::authorizedRoles()). Ce Controller affiche, immédiatement après
 * l'authentification, l'écran lui permettant de choisir son rôle ACTIF
 * pour la session (ex : "User" ou "User et Validateur").
 *
 * Le rôle actif reste porté par users.application_role_id, exactement
 * comme avant cette fonctionnalité - c'est donc la SEULE colonne lue par
 * tout le reste du code déjà écrit (Policies, ValidatorResolverService,
 * PermissionService, User::hasRole()...), qui n'a besoin d'aucune
 * modification.
 * ==========================================================================
 */
class RoleSelectionController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $roles = $user->authorizedRoles()->active()->orderBy('name')->get();

        // Garde-fou : si le User n'a en réalité qu'un seul rôle autorisé
        // (ou est arrivé ici par une URL directe), inutile de lui
        // imposer un choix - on le laisse simplement continuer.
        if ($roles->count() <= 1) {
            return redirect()->intended(route('dashboard'));
        }

        return view('auth.select-role', ['roles' => $roles, 'currentRoleId' => $user->application_role_id]);
    }

    public function store(SelectRoleRequest $request): RedirectResponse
    {
        $this->userService->switchActiveRole(
            $request->user(),
            (int) $request->validated('application_role_id'),
        );

        return redirect()->intended(route('dashboard'));
    }
}
