<?php

declare(strict_types=1);

namespace App\Http\Requests\Organisation\Admin;

use App\Models\Entity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEntityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('entity'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Entity $entity */
        $entity = $this->route('entity');

        return [
            'name' => ['required', 'string', 'max:150', Rule::unique(Entity::class, 'name')->ignore($entity->id)],
            'code' => ['required', 'string', 'max:20', Rule::unique(Entity::class, 'code')->ignore($entity->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
            'manager_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
