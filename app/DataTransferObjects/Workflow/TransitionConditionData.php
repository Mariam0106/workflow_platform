<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Workflow;

use App\Enums\TransitionLogicalOperator;
use App\Enums\TransitionOperator;

final readonly class TransitionConditionData
{
    public function __construct(
        public int $formFieldId,
        public TransitionOperator $operator,
        public ?string $expectedValue,
        public TransitionLogicalOperator $logicalOperator = TransitionLogicalOperator::And,
    ) {}

    /**
     * @param array<string, mixed> $data typically $request->validated()
     */
    public static function fromArray(array $data): self
    {
        return new self(
            formFieldId: (int) $data['form_field_id'],
            operator: TransitionOperator::from($data['operator']),
            expectedValue: $data['expected_value'] ?? null,
            logicalOperator: TransitionLogicalOperator::from($data['logical_operator'] ?? 'AND'),
        );
    }
}
