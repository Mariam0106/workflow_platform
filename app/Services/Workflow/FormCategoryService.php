<?php

declare(strict_types=1);

namespace App\Services\Workflow;

use App\Contracts\Repositories\Workflow\FormCategoryRepositoryInterface;
use App\DataTransferObjects\Workflow\FormCategoryData;
use App\Exceptions\Workflow\FormCategoryNotFoundException;
use App\Models\FormCategory;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Support\Arr;

/**
 * ==========================================================================
 * FormCategoryService
 * ==========================================================================
 *
 * Write path for Form Categories. Authorization is enforced once, at the
 * Controller layer via FormCategoryPolicy (Gate::authorize) - same
 * convention already used by the rest of the Workflow domain
 * (FormPolicy/WorkflowPolicy gate directly, no PermissionService
 * double-check like the Organisation domain does), so this Service
 * stays a thin persistence layer.
 * ==========================================================================
 */
class FormCategoryService
{
    public function __construct(
        private readonly FormCategoryRepositoryInterface $formCategories,
        private readonly AuditLogger $auditLogger,
    ) {}

    public function create(FormCategoryData $dto, User $actor): FormCategory
    {
        $formCategory = new FormCategory([
            ...$dto->toArray(),
            'created_by' => $actor->id,
        ]);

        $formCategory = $this->formCategories->save($formCategory);

        $this->auditLogger->log($actor->id, 'form_category_created', 'FormCategory', $formCategory->id, newValues: $dto->toArray());

        return $formCategory;
    }

    /**
     * @throws FormCategoryNotFoundException if the category does not exist
     */
    public function update(FormCategoryData $dto, User $actor): FormCategory
    {
        $formCategory = $this->findOrFail($dto->id);
        $oldValues = Arr::only($formCategory->getAttributes(), ['name', 'code', 'description', 'is_active']);

        $formCategory->fill([
            ...$dto->toArray(),
            'updated_by' => $actor->id,
        ]);
        $formCategory = $this->formCategories->save($formCategory);

        $this->auditLogger->log($actor->id, 'form_category_updated', 'FormCategory', $formCategory->id, $oldValues, $dto->toArray());

        return $formCategory;
    }

    /**
     * @throws FormCategoryNotFoundException
     */
    public function archive(int $formCategoryId, User $actor): FormCategory
    {
        $formCategory = $this->findOrFail($formCategoryId);
        $formCategory->is_active = false;
        $formCategory->updated_by = $actor->id;
        $formCategory = $this->formCategories->save($formCategory);

        $this->auditLogger->log($actor->id, 'form_category_archived', 'FormCategory', $formCategory->id);

        return $formCategory;
    }

    /**
     * @throws FormCategoryNotFoundException
     */
    public function restore(int $formCategoryId, User $actor): FormCategory
    {
        $formCategory = $this->findOrFail($formCategoryId);
        $formCategory->is_active = true;
        $formCategory->updated_by = $actor->id;
        $formCategory = $this->formCategories->save($formCategory);

        $this->auditLogger->log($actor->id, 'form_category_restored', 'FormCategory', $formCategory->id);

        return $formCategory;
    }

    /**
     * @throws FormCategoryNotFoundException
     */
    private function findOrFail(?int $id): FormCategory
    {
        $formCategory = $id !== null ? $this->formCategories->findById($id) : null;

        if ($formCategory === null) {
            throw FormCategoryNotFoundException::withId($id);
        }

        return $formCategory;
    }
}
