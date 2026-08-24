<?php

declare(strict_types=1);

namespace App\Http\Requests\Workflow\Admin;

use App\Models\Form;
use App\Models\Workflow;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * BR-11/12 (précision) : "repartir d'un Formulaire existant" pour un
 * nouveau Workflow crée toujours un Formulaire distinct (voir
 * FormService::duplicateForWorkflow()) - jamais une réassignation du
 * Formulaire source, qui continue de servir son propre Workflow sans
 * interruption. Nom/Code sont donc exigés ici comme à toute création de
 * Formulaire (BR-14), pas de "(copie)" auto-généré : l'Administrateur
 * choisit lui-même de quoi le distinguer.
 */
class StoreFormFromExistingRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Workflow $workflow */
        $workflow = $this->route('workflow');

        return $this->user()->can('update', $workflow) && $this->user()->can('create', Form::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'source_form_id' => ['required', 'integer', Rule::exists(Form::class, 'id')],
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:30', 'alpha_dash', Rule::unique(Form::class, 'code')],
        ];
    }
}
