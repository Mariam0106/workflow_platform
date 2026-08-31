<?php

declare(strict_types=1);

namespace App\Http\Requests\Workflow;

use App\DataTransferObjects\Workflow\RecordValidationData;
use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * RecordValidationRequest
 * ==========================================================================
 *
 * BR-40 : le commentaire de rejet est deja impose au niveau du DTO
 *. La règle required_if ci-dessous fait
 * la MEME verification, plus tot, pour renvoyer une erreur de
 * validation HTTP standard (422, format Laravel classique) plutôt
 * qu'une DomainException - meilleure experience pour un formulaire web,
 * defense en profondeur pour une API.
 * ==========================================================================
 */
class RecordValidationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'decision' => ['required', 'string', 'in:Approved,Rejected'],
            'comment' => ['nullable', 'string', 'required_if:decision,Rejected'],
        ];
    }

    public function toDto(int $requestId): RecordValidationData
    {
        return RecordValidationData::fromArray([
            'request_id' => $requestId,
            'validator_id' => $this->user()->id,
            'decision' => $this->validated('decision'),
            'comment' => $this->validated('comment'),
        ]);
    }
}
