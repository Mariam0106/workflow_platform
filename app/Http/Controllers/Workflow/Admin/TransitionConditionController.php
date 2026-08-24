<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workflow\Admin;

use App\DataTransferObjects\Workflow\TransitionConditionData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workflow\Admin\StoreTransitionConditionRequest;
use App\Models\TransitionCondition;
use App\Models\Workflow;
use App\Models\WorkflowTransition;
use App\Services\Workflow\TransitionConditionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TransitionConditionController extends Controller
{
    public function __construct(
        private readonly TransitionConditionService $transitionConditionService,
    ) {}

    public function store(StoreTransitionConditionRequest $request, Workflow $workflow, WorkflowTransition $transition): RedirectResponse
    {
        $dto = TransitionConditionData::fromArray($request->validated());

        $this->transitionConditionService->addCondition($workflow, $transition, $dto);

        return redirect()
            ->route('workflow.admin.workflows.transitions.edit', [$workflow, $transition])
            ->with('status', 'Condition ajoutée.');
    }

    public function destroy(Request $request, Workflow $workflow, WorkflowTransition $transition, TransitionCondition $condition): RedirectResponse
    {
        Gate::authorize('update', $workflow);

        $this->transitionConditionService->removeCondition($workflow, $condition);

        return redirect()
            ->route('workflow.admin.workflows.transitions.edit', [$workflow, $transition])
            ->with('status', 'Condition supprimée.');
    }
}
