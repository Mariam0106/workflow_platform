<?php

namespace Database\Factories;

use App\Models\BusinessFunction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BusinessFunction>
 *
 * NOTE (Etape 0) : factory minimale - voir EntityFactory.
 */
class BusinessFunctionFactory extends Factory
{
    protected $model = BusinessFunction::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->lexify('FN-???')),
            'name' => fake()->jobTitle(),
            'is_active' => true,
        ];
    }
}
