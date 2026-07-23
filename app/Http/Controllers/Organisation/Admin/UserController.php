<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organisation\Admin;

use App\DataTransferObjects\Organisation\CreateUserData;
use App\DataTransferObjects\Organisation\UpdateUserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organisation\Admin\StoreUserRequest;
use App\Http\Requests\Organisation\Admin\UpdateUserRequest;
use App\Models\ApplicationRole;
use App\Models\BusinessFunction;
use App\Models\Department;
use App\Models\Entity;
use App\Models\User;
use App\Services\Organisation\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * ==========================================================================
 * UserController (BackOffice - Étape 13)
 * ==========================================================================
 *
 * Thin by design : validation lives in the Form Requests, authorization
 * in UserPolicy (checked inside those same Form Requests via
 * `$this->user()->can(...)`), business rules in UserService. This
 * Controller only translates HTTP <-> Service calls.
 *
 * NOTE : the views rendered here (resources/views/organisation/users/*)
 * are intentionally minimal placeholders - the polished BackOffice UI is
 * Étape 13/14's job, not this one's. What matters at this step is that
 * every route/action is wired, authorized, and testable end-to-end.
 * ==========================================================================
 */
class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', User::class);

        $users = User::query()
            ->with(['department', 'entity', 'applicationRole'])
            ->orderBy('last_name')
            ->paginate(20);

        return view('organisation.users.index', ['users' => $users]);
    }

    public function create(): View
    {
        Gate::authorize('create', User::class);

        return view('organisation.users.create', $this->formOptions());
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        // Authorization already checked in StoreUserRequest::authorize().
        $dto = CreateUserData::fromArray($request->validated());

        $user = $this->userService->createByAdmin($dto, $request->user());

        return redirect()
            ->route('organisation.users.index')
            ->with('status', "Utilisateur « {$user->full_name} » créé.");
    }

    public function show(User $user): View
    {
        Gate::authorize('view', $user);

        return view('organisation.users.show', ['user' => $user->load(['department', 'entity', 'businessFunction', 'applicationRole', 'manager'])]);
    }

    public function edit(User $user): View
    {
        Gate::authorize('update', $user);

        return view('organisation.users.edit', [...$this->formOptions(), 'user' => $user]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        // Authorization already checked in UpdateUserRequest::authorize().
        $dto = UpdateUserData::fromArray($request->validated());

        // Le Controller choisit la bonne méthode de Service selon
        // l'acteur - la Policy autorise déjà les deux cas (Admin sur
        // n'importe qui, ou soi-même), mais chaque chemin applique des
        // règles métier différentes (voir UserService, Étape 8).
        if ($request->user()->id === $user->id) {
            $this->userService->updateOwnProfile($user->id, $dto, $request->user());
        } else {
            $this->userService->updateByAdmin($user->id, $dto, $request->user());
        }

        return redirect()
            ->route('organisation.users.index')
            ->with('status', "Utilisateur « {$user->full_name} » mis à jour.");
    }

    public function deactivate(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('deactivate', $user);

        $this->userService->deactivate($user->id, $request->user());

        return back()->with('status', "Utilisateur « {$user->full_name} » désactivé.");
    }

    public function activate(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('activate', $user);

        $this->userService->activate($user->id, $request->user());

        return back()->with('status', "Utilisateur « {$user->full_name} » réactivé.");
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(): array
    {
        return [
            'entities' => Entity::query()->active()->orderBy('name')->get(),
            'departments' => Department::query()->active()->orderBy('name')->get(),
            'businessFunctions' => BusinessFunction::query()->active()->orderBy('name')->get(),
            'applicationRoles' => ApplicationRole::query()->active()->orderBy('name')->get(),
            'managers' => User::query()->active()->orderBy('first_name')->get(),
        ];
    }
}
