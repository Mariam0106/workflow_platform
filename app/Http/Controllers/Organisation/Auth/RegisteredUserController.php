<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organisation\Auth;

use App\DataTransferObjects\Organisation\CreateUserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organisation\Auth\RegisterUserRequest;
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
 * Interface d'inscription publique, restreinte aux adresses
 * @saint-gobain.com. Le compte créé reste "Pending" et inutilisable
 * tant qu'un Administrateur ne l'a pas approuvé (voir UserService::
 * register()/approveRegistration(), RegistrationController du
 * BackOffice) - aucune connexion automatique ici.
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
            'managers' => User::query()->active()->orderBy('first_name')->get(),
        ]);
    }

    public function store(RegisterUserRequest $request): RedirectResponse
    {
        $dto = CreateUserData::fromArray($request->validated());

        $this->userService->register($dto);

        return redirect()->route('registration.pending');
    }
}
