<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organisation\Admin;

use App\DataTransferObjects\Organisation\CreateUserData;
use App\DataTransferObjects\Organisation\UpdateUserData;
use App\Enums\ApplicationRoleCode;
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
use Illuminate\Support\Facades\Gate;

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

        $search = $request->query('q');
        $sort = $request->query('sort', 'last_name');
        $direction = $request->query('direction', 'asc') === 'desc' ? 'desc' : 'asc';

        $users = User::query()
            ->with(['department', 'entity', 'applicationRole', 'applicationRoles'])
            ->when($search, fn ($q) => $q->where(fn ($inner) => $inner
                ->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")))
            ->when(
                $sort === 'last_login_at',
                fn ($q) => $q->orderByRaw('last_login_at IS NULL, last_login_at ' . $direction),
                fn ($q) => $q->orderBy('last_name', $direction),
            )
            ->paginate(20)
            ->withQueryString();

        return view('organisation.users.index', [
            'users' => $users,
            'search' => $search,
            'sort' => $sort,
            'direction' => $direction,
        ]);
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

        return view('organisation.users.show', ['user' => $user->load(['department', 'entity', 'businessFunction', 'applicationRole', 'applicationRoles', 'manager'])]);
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
        // Le chemin "self-service" restreint (updateOwnProfile) existe
        // pour empêcher un Utilisateur SANS droits d'Administrateur de
        // s'auto-attribuer des rôles/changer sa hiérarchie via l'édition
        // de son propre profil. Un Administrateur qui édite SON PROPRE
        // compte depuis cet écran a déjà tous les droits (avant() de
        // UserPolicy le confirme) - lui appliquer cette même
        // restriction ignorait silencieusement tout changement de rôle
        // qu'il faisait sur lui-même (ex. s'ajouter aussi Validateur),
        // tout en affichant malgré tout "mis à jour" comme si ça avait
        // fonctionné.
        if ($request->user()->id === $user->id && ! $request->user()->hasRole(ApplicationRoleCode::Administrator)) {
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