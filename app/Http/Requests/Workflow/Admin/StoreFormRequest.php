<?php

declare(strict_types=1);

namespace App\Http\Requests\Workflow\Admin;

use App\Models\Form;
use App\Models\FormCategory;
use App\Models\Workflow;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Form::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'form_category_id' => ['required', 'integer', Rule::exists(FormCategory::class, 'id')->where('is_active', true)],
            'workflow_id' => ['required', 'integer', Rule::exists(Workflow::class, 'id')->where('is_active', true)],
            'name' => ['required', 'string', 'max:150', Rule::unique(Form::class, 'name')],
            // BR-14/17 : unique tant que cette version du Form Builder
            // ne gère pas encore le versionnement d'un Form existant
            // (voir FormService).
            'code' => ['required', 'string', 'max:30', 'alpha_dash', Rule::unique(Form::class, 'code')],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
