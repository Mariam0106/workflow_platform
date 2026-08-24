<?php

declare(strict_types=1);

namespace App\Http\Requests\Workflow\Admin;

use App\Enums\ApplicationRoleCode;
use App\Enums\ValidatorType;
use App\Models\ApplicationRole;
use App\Models\BusinessFunction;
use App\Models\User as UserModel;
use App\Models\Workflow;
use App\Models\WorkflowStep;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreWorkflowStepRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Workflow $workflow */
        $workflow = $this->route('workflow');

        return $this->user()->can('update', $workflow);
    }

    /**
     * Type "Rôle Applicatif" (BR-06) : n'a de sens métier que pour le
     * Rôle Validator - laisser le choix entre Administrator/User/
     * Validator n'ajoutait qu'une confusion sans cas d'usage réel (un
     * Validateur d'étape "par Rôle Utilisateur" n'a pas de sens). Résolu
     * automatiquement ici plutôt que demandé à l'écran.
     */
    protected function prepareForValidation(): void
    {
        if ($this->input('validator_type') === ValidatorType::Role->value) {
            $this->merge([
                'validator_reference_role' => ApplicationRole::query()
                    ->where('code', ApplicationRoleCode::Validator->value)
                    ->value('id'),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Workflow $workflow */
        $workflow = $this->route('workflow');

        return [
            'name' => ['required', 'string', 'max:150'],
            // BR-32 : unique au sein de ce Workflow uniquement.
            'code' => [
                'required', 'string', 'max:30', 'alpha_dash',
                Rule::unique(WorkflowStep::class, 'code')->where('workflow_id', $workflow->id),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'validator_type' => ['required', Rule::enum(ValidatorType::class)],
            'validator_reference_role' => ['nullable', 'integer'],
            'validator_reference_business_function' => ['nullable', 'integer'],
            'validator_reference_user' => ['nullable', 'integer'],
            'is_end' => ['boolean'],
        ];
    }

    /**
     * BR-29 : "La Référence du Validateur doit correspondre au Type de
     * Validateur sélectionné." - l'écran affiche deux <select> distincts
     * (Rôle / Utilisateur) plutôt qu'un seul champ dynamique dépendant
     * du type (pas de JS ajouté pour ce projet) ; seul celui qui
     * correspond au type choisi est donc validé ici.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $type = $this->input('validator_type');

            if ($type === ValidatorType::Role->value) {
                if (blank($this->input('validator_reference_role'))) {
                    $validator->errors()->add('validator_type', 'Le Rôle Applicatif « Validator » est introuvable - contactez un développeur.');
                }
            } elseif ($type === ValidatorType::BusinessFunction->value) {
                $reference = $this->input('validator_reference_business_function');

                if (blank($reference)) {
                    $validator->errors()->add('validator_reference_business_function', 'Sélectionnez la Fonction Métier validatrice.');
                } elseif (! BusinessFunction::query()->where('id', $reference)->where('is_active', true)->exists()) {
                    $validator->errors()->add('validator_reference_business_function', 'Cette Fonction Métier est introuvable ou archivée.');
                }
            } elseif ($type === ValidatorType::User->value) {
                $reference = $this->input('validator_reference_user');

                if (blank($reference)) {
                    $validator->errors()->add('validator_reference_user', 'Sélectionnez l\'Utilisateur validateur.');
                } elseif (! UserModel::query()->where('id', $reference)->where('is_active', true)->exists()) {
                    $validator->errors()->add('validator_reference_user', 'Cet Utilisateur est introuvable ou inactif.');
                }
            }
        });
    }
}
