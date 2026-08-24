<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workflow\Admin;

use App\DataTransferObjects\Workflow\WorkflowStepData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workflow\Admin\ReorderWorkflowStepsRequest;
use App\Http\Requests\Workflow\Admin\StoreWorkflowStepRequest;
use App\Http\Requests\Workflow\Admin\UpdateWorkflowStepRequest;
use App\Models\BusinessFunction;
use App\Models\Department;
use App\Models\Entity;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowStep;
use App\Services\Workflow\WorkflowStepService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class WorkflowStepController extends Controller
{
    public function __construct(
        private readonly WorkflowStepService $workflowStepService,
    ) {}

    public function create(Workflow $workflow): View
    {
        Gate::authorize('update', $workflow);

        return view('workflow.workflows.steps.create', ['workflow' => $workflow, ...$this->validatorOptions()]);
    }

    public function store(StoreWorkflowStepRequest $request, Workflow $workflow): RedirectResponse
    {
        $dto = WorkflowStepData::fromArray($request->validated());

        $this->workflowStepService->addStep($workflow, $dto);

        return redirect()
            ->route('workflow.admin.workflows.edit', $workflow)
            ->with('status', 'Étape ajoutée.');
    }

    public function edit(Workflow $workflow, WorkflowStep $step): View
    {
        Gate::authorize('update', $workflow);

        return view('workflow.workflows.steps.edit', ['workflow' => $workflow, 'step' => $step, ...$this->validatorOptions()]);
    }

    public function update(UpdateWorkflowStepRequest $request, Workflow $workflow, WorkflowStep $step): RedirectResponse
    {
        $dto = WorkflowStepData::fromArray([...$request->validated(), 'id' => $step->id]);

        $this->workflowStepService->updateStep($workflow, $step, $dto);

        return redirect()
            ->route('workflow.admin.workflows.edit', $workflow)
            ->with('status', 'Étape mise à jour.');
    }

    public function destroy(Request $request, Workflow $workflow, WorkflowStep $step): RedirectResponse
    {
        Gate::authorize('update', $workflow);

        $this->workflowStepService->removeStep($workflow, $step);

        return redirect()
            ->route('workflow.admin.workflows.edit', $workflow)
            ->with('status', "Étape « {$step->name} » supprimée.");
    }

    public function setAsStart(Request $request, Workflow $workflow, WorkflowStep $step): RedirectResponse
    {
        Gate::authorize('update', $workflow);

        $this->workflowStepService->setAsStart($workflow, $step);

        return back()->with('status', "« {$step->name} » définie comme étape de début.");
    }

    public function moveUp(Request $request, Workflow $workflow, WorkflowStep $step): RedirectResponse
    {
        Gate::authorize('update', $workflow);

        $this->workflowStepService->moveUp($workflow, $step);

        return back()->with('status', 'Ordre des étapes mis à jour.');
    }

    public function moveDown(Request $request, Workflow $workflow, WorkflowStep $step): RedirectResponse
    {
        Gate::authorize('update', $workflow);

        $this->workflowStepService->moveDown($workflow, $step);

        return back()->with('status', 'Ordre des étapes mis à jour.');
    }

    /**
     * Glisser-déposer (Workflow Designer) - reçoit l'ordre complet des
     * Étapes en un seul envoi plutôt qu'une succession de moveUp()/
     * moveDown(). Les boutons monter/descendre restent disponibles en
     * parallèle (accessibilité clavier) - le glisser-déposer s'ajoute,
     * il ne les remplace pas.
     */
    public function reorder(ReorderWorkflowStepsRequest $request, Workflow $workflow): RedirectResponse
    {
        $this->workflowStepService->reorder($workflow, $request->orderedIds());

        return back()->with('status', 'Ordre des étapes mis à jour.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatorOptions(): array
    {
        return [
            'businessFunctions' => BusinessFunction::query()->active()->orderBy('name')->get(),
            'users' => User::query()->active()->with(['businessFunction', 'department', 'entity'])->orderBy('first_name')->get(),
            'entities' => Entity::query()->active()->with('manager')->orderBy('name')->get(),
            'departments' => Department::query()->active()->with('manager')->orderBy('name')->get(),
        ];
    }
}
