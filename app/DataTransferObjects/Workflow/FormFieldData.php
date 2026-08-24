<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Workflow;

final readonly class FormFieldData
{
    public function __construct(
        public string $label,
        public string $fieldType,
        public bool $isRequired = false,
        public ?string $sectionTitle = null,
        public ?string $placeholder = null,
        public ?string $defaultValue = null,
        public ?string $validationRules = null,
        public ?int $id = null,
    ) {}

    /**
     * @param array<string, mixed> $data typically $request->validated()
     */
    public static function fromArray(array $data): self
    {
        return new self(
            label: $data['label'],
            fieldType: $data['field_type'],
            isRequired: (bool) ($data['is_required'] ?? false),
            sectionTitle: $data['section_title'] ?? null,
            placeholder: $data['placeholder'] ?? null,
            defaultValue: $data['default_value'] ?? null,
            validationRules: $data['validation_rules'] ?? null,
            id: isset($data['id']) ? (int) $data['id'] : null,
        );
    }

    /**
     * Ne contient jamais "technical_name" : c'est un identifiant
     * interne généré une seule fois, à la création (voir
     * FormFieldService::addField()) - jamais exposé ni modifiable
     * depuis l'écran, pour ne pas imposer à un utilisateur non
     * technique de comprendre ce que c'est (voir aussi
     * FormFieldService::updateField(), qui ne le touche jamais).
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'field_type' => $this->fieldType,
            'is_required' => $this->isRequired,
            'section_title' => $this->sectionTitle,
            'placeholder' => $this->placeholder,
            'default_value' => $this->defaultValue,
            'validation_rules' => $this->validationRules,
        ];
    }
}
