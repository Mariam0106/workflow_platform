<?php

declare(strict_types=1);

namespace App\Contracts\Repositories\Organisation;

use App\Exceptions\Organisation\BusinessFunctionNotFoundException;
use App\Models\BusinessFunction;
use Illuminate\Database\Eloquent\Collection;

/**
 * ==========================================================================
 * BusinessFunctionRepositoryInterface
 * ==========================================================================
 *
 * Mirrors EntityRepositoryInterface - a Business Function has no
 * hierarchy of its own (BR-05 : it only tags what a User does, no
 * manager-resolution method needed here, unlike Entity/Department).
 * ==========================================================================
 */
interface BusinessFunctionRepositoryInterface
{
    /**
     * @throws BusinessFunctionNotFoundException
     */
    public function findById(int $id): BusinessFunction;

    public function findByCode(string $code): ?BusinessFunction;

    /**
     * @return Collection<int, BusinessFunction>
     */
    public function findActive(): Collection;

    /**
     * @return Collection<int, BusinessFunction>
     */
    public function findAll(): Collection;

    public function save(BusinessFunction $businessFunction): BusinessFunction;

    public function delete(BusinessFunction $businessFunction): bool;
}
