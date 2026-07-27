<?php

declare(strict_types=1);

namespace App\Http\Requests\Organisation\Admin;

use App\Models\Department;
use App\Models\Entity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('department'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Department $department */
        $department = $this->route('department');

        return [
            'entity_id' => ['required', 'integer', Rule::exists(Entity::class, 'id')->where('is_active', true)],
            'name' => ['required', 'string', 'max:150'],
            'code' => [
                'required', 'string', 'max:20',
                Rule::unique(Department::class, 'code')
                    ->where('entity_id', $this->input('entity_id'))
                    ->ignore($department->id),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
