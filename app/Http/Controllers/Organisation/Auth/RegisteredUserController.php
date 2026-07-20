<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organisation\Auth;

use App\DataTransferObjects\Organisation\CreateUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organisation\Auth\RegisterUserRequest;
use App\Models\ApplicationRole;
use App\Models\BusinessFunction;
use App\Models\Department;
use App\Models\Entity;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
 * management come later (Étape 13/14). This only has to be functionally
 * correct so Lali can build Policies/Controllers against a real,
 * authenticated User (Jalon J2/J3).
 *
 * NOTE (Étape 6) : building goes through CreateUserDTO now instead of a
 * raw array, so the day UserService::register() exists (Étape 8), this
 * store() method shrinks to a single call - only the DTO travels down,
 * never $request->validated() directly.
 * ==========================================================================
 */
class RegisteredUserController extends Controller
{
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
        $dto = CreateUserDTO::fromArray($request->validated());

        // TODO (Étape 8) : remplacer par UserService::register($dto), qui
        // encapsulera le Hash::make() et la création en un seul endroit
        // partagé (Admin "create user" utilisera le même Service).
        $user = User::query()->create([
            ...$dto->toArray(),
            'password' => Hash::make($dto->password),
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
