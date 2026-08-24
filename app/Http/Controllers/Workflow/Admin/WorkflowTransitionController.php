<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workflow\Admin;

use App\DataTransferObjects\Workflow\WorkflowTransitionData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workflow\Admin\StoreWorkflowTransitionRequest;
use App\Http\Requests\Workflow\Admin\UpdateWorkflowTransitionRequest;
use App\Models\Form;
use App\Models\Workflow;
use App\Models\WorkflowTransition;
use App\Services\Workflow\WorkflowTransitionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class WorkflowTransitionController extends Controller
{
    public function __construct(
        private readonly WorkflowTransitionService $workflowTransitionService,
    ) {}

    public function create(Workflow $workflow): View
    {
        Gate::authorize('update', $workflow);

        $workflow->loadMissing('workflowSteps');

        return view('workflow.workflows.transitions.create', ['workflow' => $workflow]);
    }

    public function store(StoreWorkflowTransitionRequest $request, Workflow $workflow): RedirectResponse
    {
        $dto = WorkflowTransitionData::fromArray($request->validated());

        $transition = $this->workflowTransitionService->addTransition($workflow, $dto);

        return redirect()
            ->route('workflow.admin.workflows.transitions.edit', [$workflow, $transition])
            ->with('status', 'Transition ajoutée - vous pouvez maintenant lui ajouter des conditions.');
    }

    public function edit(Workflow $workflow, WorkflowTransition $transition): View
    {
        Gate::authorize('update', $workflow);

        $workflow->loadMissing('workflowSteps');
        $transition->load('transitionConditions.formField');

        return view('workflow.workflows.transitions.edit', [
            'workflow' => $workflow,
            'transition' => $transition,
            'availableFields' => $this->availableConditionFields($workflow),
        ]);
    }

    public function update(UpdateWorkflowTransitionRequest $request, Workflow $workflow, WorkflowTransition $transition): RedirectResponse
    {
        $dto = WorkflowTransitionData::fromArray([...$request->validated(), 'id' => $transition->id]);

        $this->workflowTransitionService->updateTransition($workflow, $transition, $dto);

        return redirect()
            ->route('workflow.admin.workflows.transitions.edit', [$workflow, $transition])
            ->with('status', 'Transition mise à jour.');
    }

    public function destroy(Request $request, Workflow $workflow, WorkflowTransition $transition): RedirectResponse
    {
        Gate::authorize('update', $workflow);

        $this->workflowTransitionService->removeTransition($workflow, $transition);

        return redirect()
            ->route('workflow.admin.workflows.edit', $workflow)
            ->with('status', "Transition « {$transition->action_name} » supprimée.");
    }

    /**
     * Champs disponibles pour une Condition de Transition (BR-12 : un
     * Workflow peut être réutilisé par plusieurs Formulaires) - chaque
     * entrée porte le nom du Formulaire d'origine pour lever toute
     * ambiguïté quand plusieurs Formulaires utilisent ce Workflow.
     *
     * @return Collection<int, array{form_field_id: int, label: string}>
     */
    private function availableConditionFields(Workflow $workflow): Collection
    {
        return Form::query()
            ->where('workflow_id', $workflow->id)
            ->with('formFields')
            ->get()
            ->flatMap(fn ($form) => $form->formFields->map(fn ($field) => [
                'form_field_id' => $field->id,
                'label' => "{$form->name} — {$field->label}",
            ]));
    }
}
