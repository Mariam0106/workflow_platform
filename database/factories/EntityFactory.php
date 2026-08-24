<?php

namespace Database\Factories;

use App\Models\Entity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Entity>
 *
 * NOTE (Etape 0) : factory minimale, juste suffisante pour valider la
 * stabilisation (migrate:fresh --seed). Sera enrichie a l'Etape 4
 * (Models, Factories & Seeders) du roadmap.
 */
class EntityFactory extends Factory
{
    protected $model = Entity::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->lexify('ENT-???')),
            'name' => fake()->company(),
            'is_active' => true,
        ];
    }
}
