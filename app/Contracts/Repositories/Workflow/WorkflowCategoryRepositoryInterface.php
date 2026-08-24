<?php

declare(strict_types=1);

namespace App\Contracts\Repositories\Workflow;

use App\Models\WorkflowCategory;
use Illuminate\Database\Eloquent\Collection;

interface WorkflowCategoryRepositoryInterface
{
    public function findById(int $id): ?WorkflowCategory;

    /**
     * @return Collection<int, WorkflowCategory>
     */
    public function findActive(): Collection;

    public function save(WorkflowCategory $workflowCategory): WorkflowCategory;
}
