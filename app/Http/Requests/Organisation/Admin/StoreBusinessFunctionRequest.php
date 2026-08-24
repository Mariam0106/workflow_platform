<?php

declare(strict_types=1);

namespace App\Http\Requests\Organisation\Admin;

use App\Models\BusinessFunction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBusinessFunctionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', BusinessFunction::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150', Rule::unique(BusinessFunction::class, 'name')],
            'code' => ['required', 'string', 'max:20', Rule::unique(BusinessFunction::class, 'code')],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ];
    }
}
