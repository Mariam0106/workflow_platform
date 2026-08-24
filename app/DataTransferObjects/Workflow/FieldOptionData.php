<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Workflow;

final readonly class FieldOptionData
{
    public function __construct(
        public string $value,
        public string $label,
        public bool $isDefault = false,
        public ?int $id = null,
    ) {}

    /**
     * @param array<string, mixed> $data typically $request->validated()
     */
    public static function fromArray(array $data): self
    {
        return new self(
            value: $data['value'],
            label: $data['label'],
            isDefault: (bool) ($data['is_default'] ?? false),
            id: isset($data['id']) ? (int) $data['id'] : null,
        );
    }
}
