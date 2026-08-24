<?php

declare(strict_types=1);

namespace App\Http\Requests\Workflow\Admin;

use App\Models\FormCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFormCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', FormCategory::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:30', Rule::unique(FormCategory::class, 'code')],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ];
    }
}
