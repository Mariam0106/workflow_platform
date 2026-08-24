<?php

declare(strict_types=1);

namespace App\Http\Requests\Workflow\Admin;

use App\Models\Form;
use App\Models\FormCategory;
use App\Models\Workflow;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('form'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Form $target */
        $target = $this->route('form');

        return [
            'form_category_id' => ['required', 'integer', Rule::exists(FormCategory::class, 'id')->where('is_active', true)],
            'workflow_id' => ['required', 'integer', Rule::exists(Workflow::class, 'id')->where('is_active', true)],
            'name' => ['required', 'string', 'max:150', Rule::unique(Form::class, 'name')->ignore($target->id)],
            'code' => ['required', 'string', 'max:30', 'alpha_dash', Rule::unique(Form::class, 'code')->ignore($target->id)],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
