<?php

declare(strict_types=1);

namespace App\Services\Workflow;

use App\Contracts\Repositories\Workflow\WorkflowCategoryRepositoryInterface;
use App\DataTransferObjects\Workflow\WorkflowCategoryData;
use App\Exceptions\Workflow\WorkflowCategoryNotFoundException;
use App\Models\User;
use App\Models\WorkflowCategory;
use App\Services\AuditLogger;
use Illuminate\Support\Arr;

class WorkflowCategoryService
{
    public function __construct(
        private readonly WorkflowCategoryRepositoryInterface $workflowCategories,
        private readonly AuditLogger $auditLogger,
    ) {}

    public function create(WorkflowCategoryData $dto, User $actor): WorkflowCategory
    {
        $workflowCategory = new WorkflowCategory([
            ...$dto->toArray(),
            'created_by' => $actor->id,
        ]);

        $workflowCategory = $this->workflowCategories->save($workflowCategory);

        $this->auditLogger->log($actor->id, 'workflow_category_created', 'WorkflowCategory', $workflowCategory->id, newValues: $dto->toArray());

        return $workflowCategory;
    }

    public function update(WorkflowCategoryData $dto, User $actor): WorkflowCategory
    {
        $workflowCategory = $this->findOrFail($dto->id);
        $oldValues = Arr::only($workflowCategory->getAttributes(), ['name', 'code', 'description', 'is_active']);

        $workflowCategory->fill([
            ...$dto->toArray(),
            'updated_by' => $actor->id,
        ]);
        $workflowCategory = $this->workflowCategories->save($workflowCategory);

        $this->auditLogger->log($actor->id, 'workflow_category_updated', 'WorkflowCategory', $workflowCategory->id, $oldValues, $dto->toArray());

        return $workflowCategory;
    }

    public function archive(int $id, User $actor): WorkflowCategory
    {
        $workflowCategory = $this->findOrFail($id);
        $workflowCategory->is_active = false;
        $workflowCategory->updated_by = $actor->id;
        $workflowCategory = $this->workflowCategories->save($workflowCategory);

        $this->auditLogger->log($actor->id, 'workflow_category_archived', 'WorkflowCategory', $workflowCategory->id);

        return $workflowCategory;
    }

    public function restore(int $id, User $actor): WorkflowCategory
    {
        $workflowCategory = $this->findOrFail($id);
        $workflowCategory->is_active = true;
        $workflowCategory->updated_by = $actor->id;
        $workflowCategory = $this->workflowCategories->save($workflowCategory);

        $this->auditLogger->log($actor->id, 'workflow_category_restored', 'WorkflowCategory', $workflowCategory->id);

        return $workflowCategory;
    }

    private function findOrFail(?int $id): WorkflowCategory
    {
        $workflowCategory = $id !== null ? $this->workflowCategories->findById($id) : null;

        if ($workflowCategory === null) {
            throw WorkflowCategoryNotFoundException::withId($id);
        }

        return $workflowCategory;
    }
}
