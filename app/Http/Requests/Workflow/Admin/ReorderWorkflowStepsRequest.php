<?php

declare(strict_types=1);

namespace App\Http\Requests\Workflow\Admin;

use App\Models\Workflow;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ReorderWorkflowStepsRequest extends FormRequest
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
            // Envoyé par le glisser-déposer comme une chaîne CSV
            // ("3,1,2") via un unique champ caché - plus simple côté JS
            // qu'à reconstruire un tableau ordered_ids[] à la volée.
            'ordered_ids' => ['required', 'string'],
        ];
    }

    /**
     * L'ensemble envoyé doit être EXACTEMENT celui des Étapes du
     * Workflow (BR-32 : aucune Étape oubliée, aucun doublon).
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var Workflow $workflow */
            $workflow = $this->route('workflow');

            $submitted = $this->parsedIds();
            $validIds = $workflow->workflowSteps()->pluck('id')->all();

            if (count(array_unique($submitted)) !== count($submitted)) {
                $validator->errors()->add('ordered_ids', 'La liste contient une étape en double.');
            } elseif (count($submitted) !== count($validIds) || array_diff($submitted, $validIds) !== []) {
                $validator->errors()->add('ordered_ids', 'La liste ne correspond pas à l\'ensemble des étapes du workflow.');
            }
        });
    }

    /**
     * @return list<int>
     */
    public function orderedIds(): array
    {
        return $this->parsedIds();
    }

    /**
     * @return list<int>
     */
    private function parsedIds(): array
    {
        $raw = (string) $this->input('ordered_ids', '');

        if ($raw === '') {
            return [];
        }

        return array_map('intval', explode(',', $raw));
    }
}
