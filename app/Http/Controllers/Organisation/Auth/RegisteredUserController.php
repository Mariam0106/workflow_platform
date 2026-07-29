<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organisation\Auth;

use App\DataTransferObjects\Organisation\CreateUserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organisation\Auth\RegisterUserRequest;
use App\Models\ApplicationRole;
use App\Models\BusinessFunction;
use App\Models\Department;
use App\Models\Entity;
use App\Models\User;
use App\Services\Organisation\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * ==========================================================================
 * RegisteredUserController
 * ==========================================================================
 *
 * Jalon J1 (auth minimale) - "Mise en place d'une interface d'inscription,
 * restriction des inscriptions aux utilisateurs disposant d'une adresse
 * @saint-gobain.com" (cahier des charges).
 *
 * MODIFIÉ (round 2 - demande client) : cette création de compte n'est
 * plus publique - elle est réservée aux Administrateurs (voir
 * RegisterUserRequest::authorize() et la gate `can:create` posée sur la
 * route). En conséquence :
 *  - passe désormais par UserService::createByAdmin(), exactement comme
 *    le futur UserController de l'Étape 13 (même méthode de Service que
 *    le BackOffice, pas de logique dupliquée) ;
 *  - ne connecte plus automatiquement le nouvel utilisateur (l'ancien
 *    Auth::login($user) aurait déconnecté l'Admin de sa propre session
 *    pour le compte qu'il vient de créer pour quelqu'un d'autre - c'était
 *    correct pour l'auto-inscription, plus du tout pour une création par
 *    un tiers) ;
 *  - redirige vers le formulaire lui-même (avec un message de succès),
 *    pour permettre à l'Admin d'enchaîner plusieurs créations - la vraie
 *    liste BackOffice (organisation.users.index) arrive à l'Étape 13.
 *
 * Deliberately basic UI - the polished Admin/BackOffice screens for user
 * management come later (Étape 13/14).
 * ==========================================================================
 */
class RegisteredUserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function create(): View
    {
        return view('auth.register', [
            'entities' => Entity::query()->active()->orderBy('name')->get(),
            'departments' => Department::query()->active()->orderBy('name')->get(),
            'businessFunctions' => BusinessFunction::query()->active()->orderBy('name')->get(),
            'applicationRoles' => ApplicationRole::query()->active()->orderBy('name')->get(),
            'managers' => User::query()->active()->orderBy('first_name')->get(),
        ]);
    }

    public function store(RegisterUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // AJOUT (round 2 - demande client) : le formulaire ne soumet plus
        // qu'un ensemble de rôles autorisés (role_ids[]), sans "rôle
        // actif" distinct - on le dérive ici (le plus petit id = le
        // premier rôle coché dans l'ordre affiché, cf. ApplicationRole::active()
        // ->orderBy('name')). Le nouvel utilisateur pourra basculer vers
        // n'importe lequel de ses rôles autorisés dès sa première
        // connexion (RoleSelectionController), donc ce choix initial n'a
        // pas besoin d'être significatif.
        $validated['application_role_id'] = min(array_map('intval', $validated['role_ids']));

        $dto = CreateUserData::fromArray($validated);

        $user = $this->userService->createByAdmin($dto, $request->user());

        return redirect()
            ->route('register')
            ->with('status', "Compte de « {$user->full_name} » créé avec succès.");
    }
}
