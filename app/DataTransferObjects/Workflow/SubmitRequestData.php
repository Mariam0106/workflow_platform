<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Workflow;

use Illuminate\Http\Request as HttpRequest;
use InvalidArgumentException;

/**
 * ==========================================================================
 * SubmitRequestData
 * ==========================================================================
 *
 * Ce que WorkflowEngineService::submit() (Etape 9) recoit - jamais un
 * `array $data` brut. Chaque cle est nommee et typee : impossible
 * d'oublier un champ ou de se tromper de nom sans que PHP ne le signale
 * immediatement (contrairement a un tableau associatif classique).
 *
 * "values" est la liste des reponses du formulaire dynamique, sous forme
 * [form_field_id => valeur] - reste generique quel que soit le
 * formulaire (BR-56), sans jamais nommer un champ metier specifique ici.
 * ==========================================================================
 */
final readonly class SubmitRequestData
{
    /**
     * @param  array<int, string>  $values  [form_field_id => value]
     */
    public function __construct(
        public int $formId,
        public int $requesterId,
        public array $values,
    ) {
        if ($this->values === []) {
            throw new InvalidArgumentException('A Request must contain at least one field value.');
        }
    }

    /**
     * @param  array{form_id: int, requester_id: int, values: array<int, string>}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            formId: (int) $data['form_id'],
            requesterId: (int) $data['requester_id'],
            values: $data['values'] ?? [],
        );
    }

    /**
     * Builds this DTO from an already-validated HTTP Request (Etape 12
     * will call this from a Form Request's validated() array).
     */
    public static function fromRequest(HttpRequest $request): self
    {
        return self::fromArray($request->validated() + ['requester_id' => $request->user()?->id]);
    }

    /**
     * @return array{form_id: int, requester_id: int, values: array<int, string>}
     */
    public function toArray(): array
    {
        return [
            'form_id' => $this->formId,
            'requester_id' => $this->requesterId,
            'values' => $this->values,
        ];
    }
}
