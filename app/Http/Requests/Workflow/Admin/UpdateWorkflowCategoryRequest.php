<?php

declare(strict_types=1);

namespace App\Http\Requests\Workflow\Admin;

use App\Models\WorkflowCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWorkflowCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('workflow_category'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var WorkflowCategory $target */
        $target = $this->route('workflow_category');

        return [
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:30', Rule::unique(WorkflowCategory::class, 'code')->ignore($target->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ];
    }
}
