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
use Illuminate\Support\Facades\Auth;
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
 * Deliberately basic UI - the polished Admin/BackOffice screens for user
 * management come later (Étape 13/14).
 *
 * (Étape 8) : passe maintenant par UserService::register(), qui centralise
 * le hash du mot de passe et les garde-fous métier (BR-09 : Entité/
 * Département actifs). Le Controller ne fait plus que la validation HTTP
 * et la redirection.
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
        $dto = CreateUserData::fromArray($request->validated());

        $user = $this->userService->register($dto);

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
