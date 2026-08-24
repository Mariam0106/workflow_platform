<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Workflow;

use App\Enums\ValidatorType;

final readonly class WorkflowStepData
{
    public function __construct(
        public string $code,
        public string $name,
        public ValidatorType $validatorType,
        public ?int $validatorReference = null,
        public bool $isEnd = false,
        public ?string $description = null,
        public ?int $id = null,
    ) {}

    /**
     * @param array<string, mixed> $data typically $request->validated()
     */
    public static function fromArray(array $data): self
    {
        $validatorType = ValidatorType::from($data['validator_type']);

        // Un seul des deux champs de référence (rôle ou utilisateur) a
        // pu être rempli par l'écran, selon le type choisi - voir
        // resources/views/workflow/workflows/steps/{create,edit}.blade.php,
        // qui affiche deux <select> distincts plutôt qu'un seul champ
        // dynamique (pas de JS pour ce projet), et
        // StoreWorkflowStepRequest::withValidator() pour la validation
        // du bon champ selon le type.
        $reference = match ($validatorType) {
            ValidatorType::Role => $data['validator_reference_role'] ?? null,
            ValidatorType::BusinessFunction => $data['validator_reference_business_function'] ?? null,
            ValidatorType::User => $data['validator_reference_user'] ?? null,
            default => null,
        };

        return new self(
            code: $data['code'],
            name: $data['name'],
            validatorType: $validatorType,
            validatorReference: $reference !== null && $reference !== '' ? (int) $reference : null,
            isEnd: (bool) ($data['is_end'] ?? false),
            description: $data['description'] ?? null,
            id: isset($data['id']) ? (int) $data['id'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            // BR-59 : la Référence n'a de sens que pour Role/User -
            // toujours null pour N_PLUS_1/ENTITY_MANAGER/DEPARTMENT_MANAGER
            // (voir ValidatorResolverService, qui ignore de toute façon
            // validator_reference pour ces 3 types).
            'validator_type' => $this->validatorType->value,
            'validator_reference' => in_array($this->validatorType, [ValidatorType::Role, ValidatorType::BusinessFunction, ValidatorType::User], true)
                ? $this->validatorReference
                : null,
            'is_end' => $this->isEnd,
        ];
    }
}
