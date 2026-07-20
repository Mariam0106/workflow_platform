<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organisation\Auth;

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
        $validated = $request->validated();

        $user = User::query()->create([
            'entity_id' => $validated['entity_id'],
            'department_id' => $validated['department_id'],
            'business_function_id' => $validated['business_function_id'],
            'application_role_id' => $validated['application_role_id'],
            'manager_id' => $validated['manager_id'] ?? null,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
