<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organisation\Admin;

use App\DataTransferObjects\Organisation\BusinessFunctionData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organisation\Admin\StoreBusinessFunctionRequest;
use App\Http\Requests\Organisation\Admin\UpdateBusinessFunctionRequest;
use App\Models\BusinessFunction;
use App\Services\Organisation\BusinessFunctionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class BusinessFunctionController extends Controller
{
    public function __construct(
        private readonly BusinessFunctionService $businessFunctionService,
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', BusinessFunction::class);

        $search = $request->query('q');

        $businessFunctions = BusinessFunction::query()
            ->when($search, fn ($q) => $q->where(fn ($inner) => $inner
                ->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('organisation.business-functions.index', ['businessFunctions' => $businessFunctions, 'search' => $search]);
    }

    public function create(): View
    {
        Gate::authorize('create', BusinessFunction::class);

        return view('organisation.business-functions.create');
    }

    public function store(StoreBusinessFunctionRequest $request): RedirectResponse
    {
        $dto = BusinessFunctionData::fromArray($request->validated());

        $businessFunction = $this->businessFunctionService->create($dto, $request->user());

        return redirect()
            ->route('organisation.business-functions.index')
            ->with('status', "Fonction métier « {$businessFunction->name} » créée.");
    }

    public function edit(BusinessFunction $business_function): View
    {
        Gate::authorize('update', $business_function);

        return view('organisation.business-functions.edit', ['businessFunction' => $business_function]);
    }

    public function update(UpdateBusinessFunctionRequest $request, BusinessFunction $business_function): RedirectResponse
    {
        $dto = BusinessFunctionData::fromArray([...$request->validated(), 'id' => $business_function->id]);

        $this->businessFunctionService->update($dto, $request->user());

        return redirect()
            ->route('organisation.business-functions.index')
            ->with('status', "Fonction métier « {$business_function->name} » mise à jour.");
    }

    public function archive(Request $request, BusinessFunction $business_function): RedirectResponse
    {
        Gate::authorize('archive', $business_function);

        $this->businessFunctionService->archive($business_function->id, $request->user());

        return back()->with('status', "Fonction métier « {$business_function->name} » archivée.");
    }

    public function restore(Request $request, BusinessFunction $business_function): RedirectResponse
    {
        Gate::authorize('restore', $business_function);

        $this->businessFunctionService->restore($business_function->id, $request->user());

        return back()->with('status', "Fonction métier « {$business_function->name} » réactivée.");
    }
}
