<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Workflow;

final readonly class CreateWorkflowData
{
    public function __construct(
        public int $workflowCategoryId,
        public string $code,
        public string $name,
        public ?string $description = null,
    ) {}

    /**
     * @param array<string, mixed> $data typically $request->validated()
     */
    public static function fromArray(array $data): self
    {
        return new self(
            workflowCategoryId: (int) $data['workflow_category_id'],
            code: $data['code'],
            name: $data['name'],
            description: $data['description'] ?? null,
        );
    }
}
