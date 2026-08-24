<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Workflow;

use App\Contracts\Repositories\Workflow\FormCategoryRepositoryInterface;
use App\Models\FormCategory;
use Illuminate\Database\Eloquent\Collection;

class FormCategoryRepository implements FormCategoryRepositoryInterface
{
    public function __construct(
        private readonly FormCategory $model,
    ) {}

    public function findById(int $id): ?FormCategory
    {
        return $this->model->newQuery()->find($id);
    }

    public function findActive(): Collection
    {
        return $this->model->newQuery()->active()->orderBy('display_order')->orderBy('name')->get();
    }

    public function findAll(): Collection
    {
        return $this->model->newQuery()->withCount('forms')->orderBy('display_order')->orderBy('name')->get();
    }

    public function save(FormCategory $formCategory): FormCategory
    {
        $formCategory->save();

        return $formCategory;
    }
}
