<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workflow\Admin;

use App\DataTransferObjects\Workflow\WorkflowCategoryData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workflow\Admin\StoreWorkflowCategoryRequest;
use App\Http\Requests\Workflow\Admin\UpdateWorkflowCategoryRequest;
use App\Models\WorkflowCategory;
use App\Services\Workflow\WorkflowCategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class WorkflowCategoryController extends Controller
{
    public function __construct(
        private readonly WorkflowCategoryService $workflowCategoryService,
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', WorkflowCategory::class);

        $search = $request->query('q');

        return view('workflow.workflow-categories.index', [
            'workflowCategories' => WorkflowCategory::query()
                ->withCount('workflows')
                ->when($search, fn ($q) => $q->where(fn ($inner) => $inner
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")))
                ->orderBy('name')->get(),
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', WorkflowCategory::class);

        return view('workflow.workflow-categories.create');
    }

    public function store(StoreWorkflowCategoryRequest $request): RedirectResponse
    {
        $dto = WorkflowCategoryData::fromArray($request->validated());

        $workflowCategory = $this->workflowCategoryService->create($dto, $request->user());

        return redirect()
            ->route('workflow.admin.workflow-categories.index')
            ->with('status', "Catégorie « {$workflowCategory->name} » créée.");
    }

    public function edit(WorkflowCategory $workflow_category): View
    {
        Gate::authorize('update', $workflow_category);

        return view('workflow.workflow-categories.edit', ['workflowCategory' => $workflow_category]);
    }

    public function update(UpdateWorkflowCategoryRequest $request, WorkflowCategory $workflow_category): RedirectResponse
    {
        $dto = WorkflowCategoryData::fromArray([...$request->validated(), 'id' => $workflow_category->id]);

        $this->workflowCategoryService->update($dto, $request->user());

        return redirect()
            ->route('workflow.admin.workflow-categories.index')
            ->with('status', "Catégorie « {$workflow_category->name} » mise à jour.");
    }

    public function archive(Request $request, WorkflowCategory $workflow_category): RedirectResponse
    {
        Gate::authorize('archive', $workflow_category);

        $this->workflowCategoryService->archive($workflow_category->id, $request->user());

        return back()->with('status', "Catégorie « {$workflow_category->name} » archivée.");
    }

    public function restore(Request $request, WorkflowCategory $workflow_category): RedirectResponse
    {
        Gate::authorize('restore', $workflow_category);

        $this->workflowCategoryService->restore($workflow_category->id, $request->user());

        return back()->with('status', "Catégorie « {$workflow_category->name} » réactivée.");
    }
}
