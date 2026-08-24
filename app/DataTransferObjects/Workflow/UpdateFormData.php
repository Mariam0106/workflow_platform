<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Workflow;

final readonly class UpdateFormData
{
    public function __construct(
        public int $id,
        public int $formCategoryId,
        public int $workflowId,
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
            formCategoryId: (int) $data['form_category_id'],
            workflowId: (int) $data['workflow_id'],
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
            'form_category_id' => $this->formCategoryId,
            'workflow_id' => $this->workflowId,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
