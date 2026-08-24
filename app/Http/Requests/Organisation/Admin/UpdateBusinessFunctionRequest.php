<?php

declare(strict_types=1);

namespace App\Http\Requests\Organisation\Admin;

use App\Models\BusinessFunction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBusinessFunctionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('business_function'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var BusinessFunction $target */
        $target = $this->route('business_function');

        return [
            'name' => ['required', 'string', 'max:150', Rule::unique(BusinessFunction::class, 'name')->ignore($target->id)],
            'code' => ['required', 'string', 'max:20', Rule::unique(BusinessFunction::class, 'code')->ignore($target->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ];
    }
}
