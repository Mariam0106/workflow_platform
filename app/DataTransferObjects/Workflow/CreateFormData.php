<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Workflow;

use App\Enums\FormPriority;

final readonly class CreateFormData
{
    public function __construct(
        public int $formCategoryId,
        public int $workflowId,
        public string $code,
        public string $name,
        public ?string $description = null,
        public FormPriority $priority = FormPriority::Normal,
    ) {}

    /**
     * @param array<string, mixed> $data typically $request->validated()
     */
    public static function fromArray(array $data): self
    {
        return new self(
            formCategoryId: (int) $data['form_category_id'],
            workflowId: (int) $data['workflow_id'],
            code: $data['code'],
            name: $data['name'],
            description: $data['description'] ?? null,
            priority: isset($data['priority']) ? FormPriority::from($data['priority']) : FormPriority::Normal,
        );
    }
}
