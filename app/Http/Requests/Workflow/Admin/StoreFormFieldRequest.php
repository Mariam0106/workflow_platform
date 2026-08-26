<?php

declare(strict_types=1);

namespace App\Http\Requests\Workflow\Admin;

use App\Models\Form;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFormFieldRequest extends FormRequest
{
    /**
     * Types de champs supportés par le moteur de rendu dynamique -
     * voir FormField::isText()/isSelect()/isFile()/isNumber()/isDate().
     */
    public const FIELD_TYPES = ['text', 'textarea', 'email', 'password', 'number', 'montant', 'date', 'select', 'file'];

    public function authorize(): bool
    {
        /** @var Form $form */
        $form = $this->route('form');

        return $this->user()->can('update', $form);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:150'],
            'section_title' => ['nullable', 'string', 'max:150'],
            'field_type' => ['required', Rule::in(self::FIELD_TYPES)],
            'is_required' => ['boolean'],
            'placeholder' => ['nullable', 'string', 'max:255'],
            'default_value' => ['nullable', 'string', 'max:255'],
            'validation_rules' => ['nullable', 'string', 'max:500'],
            // Options saisies directement sur cet écran plutôt que
            // d'obliger un aller-retour par l'écran de modification une
            // fois le Champ créé - uniquement pertinent si field_type
            // est "select", ignoré sinon (FormFieldController::store()).
            'options' => ['nullable', 'array'],
            'options.*' => ['nullable', 'string', 'max:255'],
            'include_other_option' => ['boolean'],
        ];
    }
}
