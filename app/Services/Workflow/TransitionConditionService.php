<?php

declare(strict_types=1);

namespace App\Services\Workflow;

use App\DataTransferObjects\Workflow\TransitionConditionData;
use App\Exceptions\Workflow\WorkflowNotModifiableException;
use App\Models\TransitionCondition;
use App\Models\Workflow;
use App\Models\WorkflowTransition;
use App\Services\AuditLogger;

/**
 * BR-21/22/23 : conditions d'exécution d'une Transition - toujours
 * manipulées à travers son Workflow parent (brouillon uniquement,
 * BR-26), jamais isolément.
 */
class TransitionConditionService
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
    ) {}

    public function addCondition(Workflow $workflow, WorkflowTransition $transition, TransitionConditionData $dto): TransitionCondition
    {
        $this->assertDraft($workflow);

        $nextOrder = (int) ($transition->transitionConditions()->max('execution_order') ?? 0) + 1;

        $condition = $transition->transitionConditions()->create([
            'form_field_id' => $dto->formFieldId,
            'operator' => $dto->operator,
            'expected_value' => $dto->expectedValue,
            'logical_operator' => $dto->logicalOperator,
            'execution_order' => $nextOrder,
        ]);

        $this->auditLogger->log(auth()->id(), 'transition_condition_created', 'TransitionCondition', $condition->id, newValues: [
            'workflow_transition_id' => $transition->id, 'form_field_id' => $dto->formFieldId, 'operator' => $dto->operator->value,
        ]);

        return $condition;
    }

    public function removeCondition(Workflow $workflow, TransitionCondition $condition): void
    {
        $this->assertDraft($workflow);

        $conditionId = $condition->id;

        $condition->delete();

        $this->auditLogger->log(auth()->id(), 'transition_condition_deleted', 'TransitionCondition', $conditionId);
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
