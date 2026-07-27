<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Organisation;

/**
 * ==========================================================================
 * EntityData
 * ==========================================================================
 *
 * Mirrors DepartmentData exactly - an Entity is the top of the hierarchy,
 * so it has no parent FK to carry (unlike Department, which references
 * entity_id).
 *
 * NOTE : neither this class nor EntityService existed before Étape 11 -
 * the roadmap listed EntityController at this step but never scheduled
 * EntityService/EntityData at Étape 6/8. Added now to keep the
 * Controller -> Service -> Repository layering consistent (no Controller
 * talks to a Repository directly in this codebase).
 * ==========================================================================
 */
final readonly class EntityData
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
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'is_active' => $this->isActive,
        ];
    }
}
