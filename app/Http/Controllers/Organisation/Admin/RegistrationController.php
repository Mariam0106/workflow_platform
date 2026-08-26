<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organisation\Admin;

use App\DataTransferObjects\Organisation\UpdateUserData;
use App\Enums\RegistrationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organisation\Admin\ApproveRegistrationRequest;
use App\Models\ApplicationRole;
use App\Models\BusinessFunction;
use App\Models\Department;
use App\Models\Entity;
use App\Models\User;
use App\Services\Organisation\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * ==========================================================================
 * RegistrationController (BackOffice)
 * ==========================================================================
 *
 * Traite les demandes d'auto-inscription publique (voir
 * RegisteredUserController, UserService::register()) : un Administrateur
 * les approuve (en ajustant si besoin Entité/Département/Fonction/Rôle)
 * ou les refuse. Distinct de UserController (gestion des Utilisateurs déjà
 * actifs) - ici, le compte n'existe qu'à l'état "Pending", inutilisable
 * tant que cet écran ne l'a pas traité.
 * ==========================================================================
 */
class RegistrationController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function index(): View
    {
        Gate::authorize('manageRegistrations', User::class);

        $pending = User::query()
            ->where('registration_status', RegistrationStatus::Pending)
            ->with(['entity', 'department', 'businessFunction'])
            ->orderBy('created_at')
            ->get();

        return view('organisation.registrations.index', ['pending' => $pending]);
    }

    public function edit(User $user): View
    {
        Gate::authorize('manageRegistrations', User::class);

        abort_unless($user->isPendingRegistration(), 404);

        return view('organisation.registrations.edit', [
            'registration' => $user,
            'entities' => Entity::query()->active()->orderBy('name')->get(),
            'departments' => Department::query()->active()->orderBy('name')->get(),
            'businessFunctions' => BusinessFunction::query()->active()->orderBy('name')->get(),
            'applicationRoles' => ApplicationRole::query()->active()->orderBy('name')->get(),
        ]);
    }

    public function approve(ApproveRegistrationRequest $request, User $user): RedirectResponse
    {
        $dto = UpdateUserData::fromArray($request->validated());

        $this->userService->approveRegistration($user->id, $dto, $request->user());

        return redirect()
            ->route('organisation.registrations.index')
            ->with('status', "Inscription de « {$user->full_name} » approuvée - un e-mail lui a été envoyé.");
    }

    public function reject(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('manageRegistrations', User::class);

        $request->validate(['reason' => ['nullable', 'string', 'max:1000']]);

        $this->userService->rejectRegistration($user->id, $request->input('reason'), $request->user());

        return redirect()
            ->route('organisation.registrations.index')
            ->with('status', "Inscription de « {$user->full_name} » refusée.");
    }
}
