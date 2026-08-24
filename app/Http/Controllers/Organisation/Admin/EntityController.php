<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organisation\Admin;

use App\DataTransferObjects\Organisation\EntityData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organisation\Admin\StoreEntityRequest;
use App\Http\Requests\Organisation\Admin\UpdateEntityRequest;
use App\Models\Entity;
use App\Models\User;
use App\Services\Organisation\EntityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class EntityController extends Controller
{
    public function __construct(
        private readonly EntityService $entityService,
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Entity::class);

        $search = $request->query('q');

        $entities = Entity::query()
            ->when($search, fn ($q) => $q->where(fn ($inner) => $inner
                ->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('organisation.entities.index', ['entities' => $entities, 'search' => $search]);
    }

    public function create(): View
    {
        Gate::authorize('create', Entity::class);

        return view('organisation.entities.create', [
            'users' => User::query()->where('is_active', true)->orderBy('first_name')->get(),
        ]);
    }

    public function store(StoreEntityRequest $request): RedirectResponse
    {
        $dto = EntityData::fromArray($request->validated());

        $entity = $this->entityService->create($dto, $request->user());

        return redirect()
            ->route('organisation.entities.index')
            ->with('status', "Entité « {$entity->name} » créée.");
    }

    public function edit(Entity $entity): View
    {
        Gate::authorize('update', $entity);

        return view('organisation.entities.edit', [
            'entity' => $entity,
            'users' => User::query()->where('is_active', true)->orderBy('first_name')->get(),
        ]);
    }

    public function update(UpdateEntityRequest $request, Entity $entity): RedirectResponse
    {
        $dto = EntityData::fromArray([...$request->validated(), 'id' => $entity->id]);

        $this->entityService->update($dto, $request->user());

        return redirect()
            ->route('organisation.entities.index')
            ->with('status', "Entité « {$entity->name} » mise à jour.");
    }

    public function archive(Request $request, Entity $entity): RedirectResponse
    {
        Gate::authorize('archive', $entity);

        $this->entityService->archive($entity->id, $request->user());

        return back()->with('status', "Entité « {$entity->name} » archivée.");
    }

    public function restore(Request $request, Entity $entity): RedirectResponse
    {
        Gate::authorize('restore', $entity);

        $this->entityService->restore($entity->id, $request->user());

        return back()->with('status', "Entité « {$entity->name} » réactivée.");
    }
}
