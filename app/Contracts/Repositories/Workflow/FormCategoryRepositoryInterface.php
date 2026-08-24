<?php

declare(strict_types=1);

namespace App\Contracts\Repositories\Workflow;

use App\Models\FormCategory;
use Illuminate\Database\Eloquent\Collection;

interface FormCategoryRepositoryInterface
{
    public function findById(int $id): ?FormCategory;

    /**
     * @return Collection<int, FormCategory>
     */
    public function findActive(): Collection;

    /**
     * @return Collection<int, FormCategory>
     */
    public function findAll(): Collection;

    public function save(FormCategory $formCategory): FormCategory;
}
