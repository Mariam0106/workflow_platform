<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Workflow;

final readonly class UpdateWorkflowData
{
    public function __construct(
        public int $id,
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
            id: (int) $data['id'],
            workflowCategoryId: (int) $data['workflow_category_id'],
            code: $data['code'],
            name: $data['name'],
            description: $data['description'] ?? null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'workflow_category_id' => $this->workflowCategoryId,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
