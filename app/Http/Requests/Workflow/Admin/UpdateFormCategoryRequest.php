<?php

declare(strict_types=1);

namespace App\Http\Requests\Workflow\Admin;

use App\Models\FormCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFormCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('form_category'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var FormCategory $target */
        $target = $this->route('form_category');

        return [
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:30', Rule::unique(FormCategory::class, 'code')->ignore($target->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ];
    }
}
