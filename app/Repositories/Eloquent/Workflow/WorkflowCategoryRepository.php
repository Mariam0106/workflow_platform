<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Workflow;

use App\Contracts\Repositories\Workflow\WorkflowCategoryRepositoryInterface;
use App\Models\WorkflowCategory;
use Illuminate\Database\Eloquent\Collection;

class WorkflowCategoryRepository implements WorkflowCategoryRepositoryInterface
{
    public function __construct(
        private readonly WorkflowCategory $model,
    ) {}

    public function findById(int $id): ?WorkflowCategory
    {
        return $this->model->newQuery()->find($id);
    }

    public function findActive(): Collection
    {
        return $this->model->newQuery()->active()->orderBy('name')->get();
    }

    public function save(WorkflowCategory $workflowCategory): WorkflowCategory
    {
        $workflowCategory->save();

        return $workflowCategory;
    }
}
