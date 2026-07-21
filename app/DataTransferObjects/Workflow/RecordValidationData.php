<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Workflow;

use App\Enums\ValidationDecision;
use App\Exceptions\Workflow\ValidationException;
use Illuminate\Http\Request as HttpRequest;

/**
 * ==========================================================================
 * RecordValidationData
 * ==========================================================================
 *
 * Ce que WorkflowEngineService::recordValidation() (Etape 9) recoit.
 *
 * BR-40 (le commentaire de rejet est obligatoire) est deja applique ICI,
 * a la construction du DTO - pas besoin d'attendre le Service ou la
 * base de donnees pour le detecter : c'est une regle purement
 * structurelle (aucune requete necessaire pour la verifier).
 * ==========================================================================
 */
final readonly class RecordValidationData
{
    public function __construct(
        public int $requestId,
        public int $validatorId,
        public ValidationDecision $decision,
        public ?string $comment = null,
    ) {
        if ($this->decision === ValidationDecision::Rejected && ($this->comment === null || trim($this->comment) === '')) {
            throw ValidationException::missingRejectComment();
        }
    }

    /**
     * @param  array{request_id: int, validator_id: int, decision: string, comment?: ?string}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            requestId: (int) $data['request_id'],
            validatorId: (int) $data['validator_id'],
            decision: $data['decision'] instanceof ValidationDecision
                ? $data['decision']
                : ValidationDecision::from($data['decision']),
            comment: $data['comment'] ?? null,
        );
    }

    public static function fromRequest(HttpRequest $request): self
    {
        return self::fromArray($request->validated() + ['validator_id' => $request->user()?->id]);
    }

    /**
     * @return array{request_id: int, validator_id: int, decision: string, comment: ?string}
     */
    public function toArray(): array
    {
        return [
            'request_id' => $this->requestId,
            'validator_id' => $this->validatorId,
            'decision' => $this->decision->value,
            'comment' => $this->comment,
        ];
    }
}
