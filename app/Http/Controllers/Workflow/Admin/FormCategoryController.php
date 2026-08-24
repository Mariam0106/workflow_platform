<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workflow\Admin;

use App\DataTransferObjects\Workflow\FormCategoryData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workflow\Admin\StoreFormCategoryRequest;
use App\Http\Requests\Workflow\Admin\UpdateFormCategoryRequest;
use App\Models\FormCategory;
use App\Services\Workflow\FormCategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class FormCategoryController extends Controller
{
    public function __construct(
        private readonly FormCategoryService $formCategoryService,
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', FormCategory::class);

        $search = $request->query('q');

        return view('workflow.form-categories.index', [
            'formCategories' => FormCategory::query()
                ->withCount('forms')
                ->when($search, fn ($q) => $q->where(fn ($inner) => $inner
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")))
                ->orderBy('display_order')->orderBy('name')->get(),
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', FormCategory::class);

        return view('workflow.form-categories.create');
    }

    public function store(StoreFormCategoryRequest $request): RedirectResponse
    {
        $dto = FormCategoryData::fromArray($request->validated());

        $formCategory = $this->formCategoryService->create($dto, $request->user());

        return redirect()
            ->route('workflow.admin.form-categories.index')
            ->with('status', "Catégorie « {$formCategory->name} » créée.");
    }

    public function edit(FormCategory $form_category): View
    {
        Gate::authorize('update', $form_category);

        return view('workflow.form-categories.edit', ['formCategory' => $form_category]);
    }

    public function update(UpdateFormCategoryRequest $request, FormCategory $form_category): RedirectResponse
    {
        $dto = FormCategoryData::fromArray([...$request->validated(), 'id' => $form_category->id]);

        $this->formCategoryService->update($dto, $request->user());

        return redirect()
            ->route('workflow.admin.form-categories.index')
            ->with('status', "Catégorie « {$form_category->name} » mise à jour.");
    }

    public function archive(Request $request, FormCategory $form_category): RedirectResponse
    {
        Gate::authorize('archive', $form_category);

        $this->formCategoryService->archive($form_category->id, $request->user());

        return back()->with('status', "Catégorie « {$form_category->name} » archivée.");
    }

    public function restore(Request $request, FormCategory $form_category): RedirectResponse
    {
        Gate::authorize('restore', $form_category);

        $this->formCategoryService->restore($form_category->id, $request->user());

        return back()->with('status', "Catégorie « {$form_category->name} » réactivée.");
    }
}
