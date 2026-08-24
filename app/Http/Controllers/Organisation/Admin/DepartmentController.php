<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organisation\Admin;

use App\DataTransferObjects\Organisation\DepartmentData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organisation\Admin\StoreDepartmentRequest;
use App\Http\Requests\Organisation\Admin\UpdateDepartmentRequest;
use App\Models\Department;
use App\Models\Entity;
use App\Models\User;
use App\Services\Organisation\DepartmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function __construct(
        private readonly DepartmentService $departmentService,
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Department::class);

        $search = $request->query('q');

        $departments = Department::query()
            ->with('entity')
            ->when($search, fn ($q) => $q->where(fn ($inner) => $inner
                ->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('organisation.departments.index', ['departments' => $departments, 'search' => $search]);
    }

    public function create(): View
    {
        Gate::authorize('create', Department::class);

        return view('organisation.departments.create', [
            'entities' => Entity::query()->active()->orderBy('name')->get(),
            'users' => User::query()->where('is_active', true)->orderBy('first_name')->get(),
        ]);
    }

    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        $dto = DepartmentData::fromArray($request->validated());

        $department = $this->departmentService->create($dto, $request->user());

        return redirect()
            ->route('organisation.departments.index')
            ->with('status', "Département « {$department->name} » créé.");
    }

    public function edit(Department $department): View
    {
        Gate::authorize('update', $department);

        return view('organisation.departments.edit', [
            'department' => $department,
            'entities' => Entity::query()->active()->orderBy('name')->get(),
            'users' => User::query()->where('is_active', true)->orderBy('first_name')->get(),
        ]);
    }

    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        $dto = DepartmentData::fromArray([...$request->validated(), 'id' => $department->id]);

        $this->departmentService->update($dto, $request->user());

        return redirect()
            ->route('organisation.departments.index')
            ->with('status', "Département « {$department->name} » mis à jour.");
    }

    public function archive(Request $request, Department $department): RedirectResponse
    {
        Gate::authorize('archive', $department);

        $this->departmentService->archive($department->id, $request->user());

        return back()->with('status', "Département « {$department->name} » archivé.");
    }

    public function restore(Request $request, Department $department): RedirectResponse
    {
        Gate::authorize('restore', $department);

        $this->departmentService->restore($department->id, $request->user());

        return back()->with('status', "Département « {$department->name} » réactivé.");
    }
}
