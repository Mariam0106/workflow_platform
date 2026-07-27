<?php

declare(strict_types=1);

namespace App\Http\Requests\Organisation\Admin;

use App\Models\Department;
use App\Models\Entity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Department::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'entity_id' => ['required', 'integer', Rule::exists(Entity::class, 'id')->where('is_active', true)],
            'name' => ['required', 'string', 'max:150'],
            // BR : code unique par Entité (contrainte DB : unique(['entity_id','code'])).
            'code' => ['required', 'string', 'max:20', Rule::unique(Department::class, 'code')->where('entity_id', $this->input('entity_id'))],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ];
    }
}
