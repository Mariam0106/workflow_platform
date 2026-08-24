<?php

declare(strict_types=1);

namespace App\Http\Requests\Workflow;

use App\DataTransferObjects\Workflow\SaveDraftData;
use App\Models\Form;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Volontairement permissive - contrairement à SubmitRequestRequest, un
 * Brouillon n'a pas à être complet (BR-13 "au moins un champ" ne
 * s'applique qu'à la Requête réellement soumise, pas à ses versions
 * intermédiaires sauvegardées automatiquement).
 */
class SaveRequestDraftRequest extends FormRequest
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
            'values' => ['nullable', 'array'],
            'values.*' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function toDto(Form $form): SaveDraftData
    {
        return new SaveDraftData(
            formId: $form->id,
            requesterId: $this->user()->id,
            values: $this->validated('values') ?? [],
        );
    }
}
