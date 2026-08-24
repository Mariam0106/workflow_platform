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

class UpdateWorkflowStepRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Workflow $workflow */
        $workflow = $this->route('workflow');

        return $this->user()->can('update', $workflow);
    }

    /**
     * @see StoreWorkflowStepRequest::prepareForValidation() - même logique.
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
        /** @var WorkflowStep $step */
        $step = $this->route('step');

        return [
            'name' => ['required', 'string', 'max:150'],
            'code' => [
                'required', 'string', 'max:30', 'alpha_dash',
                Rule::unique(WorkflowStep::class, 'code')->where('workflow_id', $workflow->id)->ignore($step->id),
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
     * @see StoreWorkflowStepRequest::withValidator() - même logique.
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
