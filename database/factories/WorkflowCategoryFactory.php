<?php

namespace Database\Factories;

use App\Models\WorkflowCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<WorkflowCategory> */
class WorkflowCategoryFactory extends Factory
{
    protected $model = WorkflowCategory::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->lexify('WFC-???')),
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
