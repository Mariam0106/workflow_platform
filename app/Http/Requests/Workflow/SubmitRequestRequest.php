<?php

declare(strict_types=1);

namespace App\Http\Requests\Workflow;

use App\DataTransferObjects\Workflow\SubmitRequestData;
use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================================
 * SubmitRequestRequest
 * ==========================================================================
 *
 * Validation structurelle minimale (BR-56 : la validation fine par champ
 * de formulaire dynamique - obligatoire/type/regles - releve du
 * DynamicFormRuleBuilder, Etape "Formulaires dynamiques", pas d'ici).
 * Convertit l'input HTTP valide en SubmitRequestData (Etape 6) - le
 * Controller ne voit jamais un tableau brut.
 * ==========================================================================
 */
class SubmitRequestRequest extends FormRequest
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
            'form_id' => ['required', 'integer', 'exists:forms,id'],
            'values' => ['required', 'array', 'min:1'],
            'values.*' => ['required'],
        ];
    }

    public function toDto(): SubmitRequestData
    {
        return SubmitRequestData::fromArray([
            'form_id' => $this->validated('form_id'),
            'requester_id' => $this->user()->id,
            'values' => $this->validated('values'),
        ]);
    }
}
