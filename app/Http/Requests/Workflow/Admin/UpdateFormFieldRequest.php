<?php

declare(strict_types=1);

namespace App\Http\Requests\Workflow\Admin;

use App\Models\Form;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFormFieldRequest extends FormRequest
{
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
            'field_type' => ['required', Rule::in(StoreFormFieldRequest::FIELD_TYPES)],
            'is_required' => ['boolean'],
            'placeholder' => ['nullable', 'string', 'max:255'],
            'default_value' => ['nullable', 'string', 'max:255'],
            'validation_rules' => ['nullable', 'string', 'max:500'],
        ];
    }
}
