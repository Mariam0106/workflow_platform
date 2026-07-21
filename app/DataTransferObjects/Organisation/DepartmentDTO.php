<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Organisation;

/**
 * ==========================================================================
 * DepartmentDTO
 * ==========================================================================
 *
 * Unlike CreateUserDTO/UpdateUserDTO, a Department has few enough fields
 * that a single DTO covers both create and update: `id` is null on
 * creation, set on update (the Service/Repository decides which query to
 * run based on its presence - see DepartmentService, Étape 8).
 *
 * Business Rules covered
 * --------------------------------------------------------------------------
 * Departments belong to exactly one Entity (BR-04 depends on this link
 * being valid).
 * ==========================================================================
 */
final readonly class DepartmentDTO
{
    public function __construct(
        public int $entityId,
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
            entityId: (int) $data['entity_id'],
            name: $data['name'],
            code: $data['code'],
            description: $data['description'] ?? null,
            isActive: (bool) ($data['is_active'] ?? true),
            id: isset($data['id']) ? (int) $data['id'] : null,
        );
    }

    public function isCreation(): bool
    {
        return $this->id === null;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'entity_id' => $this->entityId,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'is_active' => $this->isActive,
        ];
    }
}
