<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Workflow;

final readonly class FormCategoryData
{
    public function __construct(
        public string $name,
        public string $code,
        public ?string $description = null,
        public bool $isActive = true,
        public ?int $id = null,
    ) {}

    /**
     * @param array<string, mixed> $data typically $request->validated()
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            code: $data['code'],
            description: $data['description'] ?? null,
            isActive: (bool) ($data['is_active'] ?? true),
            id: isset($data['id']) ? (int) $data['id'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'is_active' => $this->isActive,
        ];
    }
}
