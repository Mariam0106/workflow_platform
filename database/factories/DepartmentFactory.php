<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Entity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Department>
 *
 * NOTE (Etape 0) : factory minimale - voir EntityFactory.
 */
class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'entity_id' => Entity::factory(),
            'code' => strtoupper(fake()->unique()->lexify('DEP-???')),
            'name' => fake()->word(),
            'is_active' => true,
        ];
    }
}
