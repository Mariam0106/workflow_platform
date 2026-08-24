<?php

declare(strict_types=1);

namespace App\Http\Requests\Workflow\Admin;

use App\Enums\TransitionLogicalOperator;
use App\Enums\TransitionOperator;
use App\Models\Form;
use App\Models\FormField;
use App\Models\Workflow;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransitionConditionRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Workflow $workflow */
        $workflow = $this->route('workflow');

        return $this->user()->can('update', $workflow);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Workflow $workflow */
        $workflow = $this->route('workflow');

        return [
            // Le champ doit appartenir à un Formulaire qui utilise
            // CE Workflow (BR-12 : un Workflow peut être réutilisé par
            // plusieurs Formulaires - le champ peut donc venir de
            // n'importe lequel d'entre eux, jamais d'un Formulaire sans
            // rapport avec ce Workflow).
            'form_field_id' => [
                'required', 'integer',
                Rule::exists(FormField::class, 'id')->where(
                    fn ($query) => $query->whereIn('form_id', Form::query()->where('workflow_id', $workflow->id)->pluck('id')),
                ),
            ],
            'operator' => ['required', Rule::enum(TransitionOperator::class)],
            'expected_value' => ['nullable', 'string', 'max:255'],
            'logical_operator' => ['required', Rule::enum(TransitionLogicalOperator::class)],
        ];
    }
}
