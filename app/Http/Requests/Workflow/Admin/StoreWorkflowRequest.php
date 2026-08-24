<?php

declare(strict_types=1);

namespace App\Http\Requests\Workflow\Admin;

use App\Models\Workflow;
use App\Models\WorkflowCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWorkflowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Workflow::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'workflow_category_id' => ['required', 'integer', Rule::exists(WorkflowCategory::class, 'id')->where('is_active', true)],
            'name' => ['required', 'string', 'max:150', Rule::unique(Workflow::class, 'name')],
            // BR-24/25 : unique tant que cette version du Workflow
            // Designer ne gère pas encore le versionnement d'un
            // Workflow existant (voir WorkflowService).
            'code' => ['required', 'string', 'max:30', 'alpha_dash', Rule::unique(Workflow::class, 'code')],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
