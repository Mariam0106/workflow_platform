<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Organisation;

use App\Contracts\Repositories\Organisation\BusinessFunctionRepositoryInterface;
use App\Exceptions\Organisation\BusinessFunctionNotFoundException;
use App\Models\BusinessFunction;
use Illuminate\Database\Eloquent\Collection;

class BusinessFunctionRepository implements BusinessFunctionRepositoryInterface
{
    public function __construct(
        private readonly BusinessFunction $model,
    ) {}

    public function findById(int $id): BusinessFunction
    {
        $businessFunction = $this->model->newQuery()->find($id);

        if ($businessFunction === null) {
            throw BusinessFunctionNotFoundException::withId($id);
        }

        return $businessFunction;
    }

    public function findByCode(string $code): ?BusinessFunction
    {
        return $this->model->newQuery()->where('code', $code)->first();
    }

    public function findActive(): Collection
    {
        return $this->model->newQuery()->active()->orderBy('name')->get();
    }

    public function findAll(): Collection
    {
        return $this->model->newQuery()->orderBy('name')->get();
    }

    public function save(BusinessFunction $businessFunction): BusinessFunction
    {
        $businessFunction->save();

        return $businessFunction;
    }

    public function delete(BusinessFunction $businessFunction): bool
    {
        return $businessFunction->delete();
    }
}
