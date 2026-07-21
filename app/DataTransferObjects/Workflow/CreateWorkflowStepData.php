<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Workflow;

use App\Enums\ValidatorType;
use Illuminate\Http\Request as HttpRequest;
use InvalidArgumentException;

/**
 * ==========================================================================
 * CreateWorkflowStepData
 * ==========================================================================
 *
 * Ce qu'un futur WorkflowConfigurationService (BackOffice, Etape 12/15)
 * recevra pour ajouter un Step a un Workflow.
 * ==========================================================================
 */
final readonly class CreateWorkflowStepData
{
    public function __construct(
        public int $workflowId,
        public string $code,
        public string $name,
        public int $stepOrder,
        public ValidatorType $validatorType,
        public ?string $description = null,
        public bool $isStart = false,
        public bool $isEnd = false,
        public ?int $validatorReference = null,
    ) {
        if ($this->isStart && $this->isEnd) {
            throw new InvalidArgumentException('A Workflow Step cannot be both the start and the end Step.');
        }

        // ValidatorType::Role/User exigent une reference precise
        // (l'id du role ou de l'utilisateur) ; NPlus1/EntityManager/
        // DepartmentManager la resolvent dynamiquement et n'en ont pas
        // besoin (BR-59).
        if (in_array($this->validatorType, [ValidatorType::Role, ValidatorType::User], true) && $this->validatorReference === null) {
            throw new InvalidArgumentException(
                "validator_reference is required when validator_type is \"{$this->validatorType->value}\"."
            );
        }
    }

    /**
     * @param  array{workflow_id: int, code: string, name: string, step_order: int, validator_type: string, description?: ?string, is_start?: bool, is_end?: bool, validator_reference?: ?int}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            workflowId: (int) $data['workflow_id'],
            code: $data['code'],
            name: $data['name'],
            stepOrder: (int) $data['step_order'],
            validatorType: $data['validator_type'] instanceof ValidatorType
                ? $data['validator_type']
                : ValidatorType::from($data['validator_type']),
            description: $data['description'] ?? null,
            isStart: (bool) ($data['is_start'] ?? false),
            isEnd: (bool) ($data['is_end'] ?? false),
            validatorReference: isset($data['validator_reference']) ? (int) $data['validator_reference'] : null,
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
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'step_order' => $this->stepOrder,
            'is_start' => $this->isStart,
            'is_end' => $this->isEnd,
            'validator_type' => $this->validatorType->value,
            'validator_reference' => $this->validatorReference,
        ];
    }
}
