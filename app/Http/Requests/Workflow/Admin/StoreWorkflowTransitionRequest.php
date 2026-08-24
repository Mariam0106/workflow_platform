<?php

declare(strict_types=1);

namespace App\Http\Requests\Workflow\Admin;

use App\Models\Workflow;
use App\Models\WorkflowStep;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWorkflowTransitionRequest extends FormRequest
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
            // BR-20 : from_step_id/to_step_id doivent appartenir à CE
            // Workflow - jamais à un autre (une Transition ne peut pas
            // relier deux Étapes de Workflows différents).
            'from_step_id' => ['required', 'integer', Rule::exists(WorkflowStep::class, 'id')->where('workflow_id', $workflow->id)],
            'to_step_id' => ['required', 'integer', Rule::exists(WorkflowStep::class, 'id')->where('workflow_id', $workflow->id)],
            'action_name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            // Niveaux prédéfinis plutôt qu'un nombre libre (0-1000) -
            // plus intuitif pour un employé non technique. Voir les
            // options du <select> "priority" dans
            // resources/views/workflow/workflows/transitions/{create,edit}.blade.php.
            'priority' => ['required', 'integer', Rule::in([50, 80, 100])],
            'is_default' => ['boolean'],
        ];
    }
}
