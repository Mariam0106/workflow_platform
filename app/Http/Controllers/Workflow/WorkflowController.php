<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workflow\Admin;

use App\DataTransferObjects\Workflow\CreateWorkflowData;
use App\DataTransferObjects\Workflow\UpdateWorkflowData;
use App\Enums\FormStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workflow\Admin\StoreWorkflowRequest;
use App\Http\Requests\Workflow\Admin\UpdateWorkflowRequest;
use App\Models\BusinessFunction;
use App\Models\Form;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowCategory;
use App\Services\Workflow\WorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class WorkflowController extends Controller
{
    public function __construct(
        private readonly WorkflowService $workflowService,
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Workflow::class);

        $search = $request->query('q');

        $workflows = Workflow::query()
            ->with('workflowCategory')
            ->withCount(['workflowSteps', 'forms'])
            ->when($search, fn ($q) => $q->where(fn ($inner) => $inner
                ->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")))
            ->orderByDesc('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('workflow.workflows.index', ['workflows' => $workflows, 'search' => $search]);
    }

    public function create(): View
    {
        Gate::authorize('create', Workflow::class);

        return view('workflow.workflows.create', $this->formOptions());
    }

    public function store(StoreWorkflowRequest $request): RedirectResponse
    {
        $dto = CreateWorkflowData::fromArray($request->validated());

        $workflow = $this->workflowService->createDraft($dto, $request->user());

        return redirect()
            ->route('workflow.admin.workflows.edit', $workflow)
            ->with('status', "Workflow « {$workflow->name} » créé en brouillon - ajoutez-lui des étapes avant de le publier.");
    }

    public function edit(Workflow $workflow): View
    {
        Gate::authorize('view', $workflow);

        $workflow->load(['workflowSteps' => fn ($q) => $q->orderBy('step_order'), 'workflowSteps.outgoingTransitions.fromStep', 'workflowSteps.outgoingTransitions.toStep', 'workflowSteps.outgoingTransitions.transitionConditions', 'completionNotifications', 'forms']);
        $workflow->loadCount('forms');

        return view('workflow.workflows.edit', [
            ...$this->formOptions($workflow),
            'workflow' => $workflow,
            'businessFunctions' => BusinessFunction::query()->active()->orderBy('name')->get(),
            'users' => User::query()->active()->orderBy('first_name')->get(),
            'existingForms' => Form::query()->where('workflow_id', '!=', $workflow->id)->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateWorkflowRequest $request, Workflow $workflow): RedirectResponse
    {
        $dto = UpdateWorkflowData::fromArray([...$request->validated(), 'id' => $workflow->id]);

        $this->workflowService->updateDraft($dto, $request->user());

        return redirect()
            ->route('workflow.admin.workflows.edit', $workflow)
            ->with('status', "Workflow « {$workflow->name} » mis à jour.");
    }

    public function publish(Request $request, Workflow $workflow): RedirectResponse
    {
        Gate::authorize('publish', $workflow);

        $this->workflowService->publish($workflow->id, $request->user());

        $hasDraftForms = Form::query()->where('workflow_id', $workflow->id)->where('status', FormStatus::Draft)->exists();

        if ($hasDraftForms) {
            return redirect()
                ->route('workflow.admin.forms.index', ['workflow_id' => $workflow->id])
                ->with('status', "Workflow « {$workflow->name} » publié. Il reste au moins un formulaire en brouillon à publier ci-dessous pour qu'il soit utilisable.");
        }

        return redirect()
            ->route('workflow.admin.workflows.edit', $workflow)
            ->with('status', "Workflow « {$workflow->name} » publié - il peut désormais être associé à des formulaires.");
    }

    public function archive(Request $request, Workflow $workflow): RedirectResponse
    {
        Gate::authorize('archive', $workflow);

        $this->workflowService->archive($workflow->id, $request->user());

        return back()->with('status', "Workflow « {$workflow->name} » archivé.");
    }

    public function duplicate(Request $request, Workflow $workflow): RedirectResponse
    {
        Gate::authorize('create', Workflow::class);

        $copy = $this->workflowService->duplicate($workflow->id, $request->user());

        return redirect()
            ->route('workflow.admin.workflows.edit', $copy)
            ->with('status', "« {$workflow->name} » dupliqué en un nouveau workflow indépendant.");
    }

    public function destroy(Request $request, Workflow $workflow): RedirectResponse
    {
        Gate::authorize('delete', $workflow);

        $name = $workflow->name;

        $this->workflowService->delete($workflow->id, $request->user());

        return redirect()
            ->route('workflow.admin.workflows.index')
            ->with('status', "Workflow « {$name} » supprimé.");
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(?Workflow $current = null): array
    {
        $workflowCategories = WorkflowCategory::query()->active()->orderBy('name')->get();

        if ($current !== null && $current->workflowCategory !== null && ! $workflowCategories->contains('id', $current->workflowCategory->id)) {
            $workflowCategories->push($current->workflowCategory);
        }

        return ['workflowCategories' => $workflowCategories];
    }
}