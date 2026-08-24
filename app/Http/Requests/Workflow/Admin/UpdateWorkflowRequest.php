<?php

declare(strict_types=1);

namespace App\Http\Requests\Workflow\Admin;

use App\Models\Workflow;
use App\Models\WorkflowCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWorkflowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('workflow'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Workflow $target */
        $target = $this->route('workflow');

        return [
            'workflow_category_id' => ['required', 'integer', Rule::exists(WorkflowCategory::class, 'id')->where('is_active', true)],
            'name' => ['required', 'string', 'max:150', Rule::unique(Workflow::class, 'name')->ignore($target->id)],
            'code' => ['required', 'string', 'max:30', 'alpha_dash', Rule::unique(Workflow::class, 'code')->ignore($target->id)],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
