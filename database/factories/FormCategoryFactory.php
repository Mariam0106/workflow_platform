<?php

namespace Database\Factories;

use App\Models\FormCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<FormCategory> */
class FormCategoryFactory extends Factory
{
    protected $model = FormCategory::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->lexify('FMC-???')),
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'icon' => 'document',
            'display_order' => fake()->numberBetween(1, 10),
            'is_active' => true,
        ];
    }
}
