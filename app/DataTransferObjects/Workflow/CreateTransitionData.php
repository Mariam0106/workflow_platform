<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Workflow;

use App\Enums\WorkflowPriority;
use Illuminate\Http\Request as HttpRequest;
use InvalidArgumentException;

/**
 * ==========================================================================
 * CreateTransitionData
 * ==========================================================================
 *
 * Ce qu'un futur WorkflowConfigurationService recevra pour ajouter une
 * Transition entre deux Steps, avec ses Conditions embarquees.
 * ==========================================================================
 */
final readonly class CreateTransitionData
{
    /**
     * @param  list<array{form_field_id: int, operator: string, expected_value: string, logical_operator?: string, execution_order?: int}>  $conditions
     */
    public function __construct(
        public int $workflowId,
        public int $fromStepId,
        public int $toStepId,
        public string $actionName,
        public ?string $description = null,
        public int $priority = WorkflowPriority::Medium->value,
        public bool $isDefault = false,
        public array $conditions = [],
    ) {
        if ($this->fromStepId === $this->toStepId) {
            throw new InvalidArgumentException('A Transition cannot connect a Step to itself.');
        }

        // BR-22 : une Transition par defaut n'a pas besoin de conditions
        // (elle sert justement de filet de securite quand aucune autre
        // ne s'applique) - avoir les deux a la fois est un signal
        // probable d'erreur de configuration.
        if ($this->isDefault && $this->conditions !== []) {
            throw new InvalidArgumentException('A default Transition should not carry Conditions - it is meant to apply when no other Transition matches.');
        }
    }

    /**
     * @param  array{workflow_id: int, from_step_id: int, to_step_id: int, action_name: string, description?: ?string, priority?: int, is_default?: bool, conditions?: array}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            workflowId: (int) $data['workflow_id'],
            fromStepId: (int) $data['from_step_id'],
            toStepId: (int) $data['to_step_id'],
            actionName: $data['action_name'],
            description: $data['description'] ?? null,
            priority: (int) ($data['priority'] ?? WorkflowPriority::Medium->value),
            isDefault: (bool) ($data['is_default'] ?? false),
            conditions: $data['conditions'] ?? [],
        );
    }

    public static function fromRequest(HttpRequest $request): self
    {
        return self::fromArray($request->validated());
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'workflow_id' => $this->workflowId,
            'from_step_id' => $this->fromStepId,
            'to_step_id' => $this->toStepId,
            'action_name' => $this->actionName,
            'description' => $this->description,
            'priority' => $this->priority,
            'is_default' => $this->isDefault,
            'conditions' => $this->conditions,
        ];
    }
}
