<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Workflow;

final readonly class WorkflowTransitionData
{
    public function __construct(
        public int $fromStepId,
        public int $toStepId,
        public string $actionName,
        public int $priority = 50,
        public bool $isDefault = false,
        public ?string $description = null,
        public ?int $id = null,
    ) {}

    /**
     * @param array<string, mixed> $data typically $request->validated()
     */
    public static function fromArray(array $data): self
    {
        return new self(
            fromStepId: (int) $data['from_step_id'],
            toStepId: (int) $data['to_step_id'],
            actionName: $data['action_name'],
            priority: (int) ($data['priority'] ?? 50),
            isDefault: (bool) ($data['is_default'] ?? false),
            description: $data['description'] ?? null,
            id: isset($data['id']) ? (int) $data['id'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'from_step_id' => $this->fromStepId,
            'to_step_id' => $this->toStepId,
            'action_name' => $this->actionName,
            'priority' => $this->priority,
            'is_default' => $this->isDefault,
            'description' => $this->description,
        ];
    }
}
