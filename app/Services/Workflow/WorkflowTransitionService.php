<?php

declare(strict_types=1);

namespace App\Services\Workflow;

use App\DataTransferObjects\Workflow\WorkflowTransitionData;
use App\Exceptions\Workflow\WorkflowNotModifiableException;
use App\Models\Workflow;
use App\Models\WorkflowTransition;
use App\Services\AuditLogger;

/**
 * BR-20/21/22/23 : gère les Transitions d'un Workflow - toujours en
 * brouillon uniquement (BR-26). La validation "from_step_id/to_step_id
 * appartiennent bien à ce Workflow" est faite dans le FormRequest
 * (Rule::exists scopée), pas ici - ce Service fait confiance à son
 * appelant, comme le reste du domaine Workflow (voir FormFieldService).
 */
class WorkflowTransitionService
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
    ) {}

    public function addTransition(Workflow $workflow, WorkflowTransitionData $dto): WorkflowTransition
    {
        $this->assertDraft($workflow);

        $transition = $workflow->workflowTransitions()->create($dto->toArray());

        $this->auditLogger->log(auth()->id(), 'workflow_transition_created', 'WorkflowTransition', $transition->id, newValues: [
            'workflow_id' => $workflow->id, 'action_name' => $transition->action_name,
            'from_step_id' => $transition->from_step_id, 'to_step_id' => $transition->to_step_id,
        ]);

        return $transition;
    }

    public function updateTransition(Workflow $workflow, WorkflowTransition $transition, WorkflowTransitionData $dto): WorkflowTransition
    {
        $this->assertDraft($workflow);

        $oldValues = ['action_name' => $transition->action_name, 'priority' => $transition->priority, 'is_default' => $transition->is_default];

        $transition->fill($dto->toArray());
        $transition->save();

        $this->auditLogger->log(auth()->id(), 'workflow_transition_updated', 'WorkflowTransition', $transition->id, $oldValues);

        return $transition;
    }

    public function removeTransition(Workflow $workflow, WorkflowTransition $transition): void
    {
        $this->assertDraft($workflow);

        $transitionId = $transition->id;
        $actionName = $transition->action_name;

        $transition->delete();

        $this->auditLogger->log(auth()->id(), 'workflow_transition_deleted', 'WorkflowTransition', $transitionId, ['action_name' => $actionName, 'workflow_id' => $workflow->id]);
    }

    /**
     * @throws WorkflowNotModifiableException
     */
    private function assertDraft(Workflow $workflow): void
    {
        if (! $workflow->isDraft()) {
            throw WorkflowNotModifiableException::becausePublished($workflow);
        }
    }
}
