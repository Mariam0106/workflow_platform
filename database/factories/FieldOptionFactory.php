<?php

namespace Database\Factories;

use App\Models\FieldOption;
use App\Models\FormField;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<FieldOption> */
class FieldOptionFactory extends Factory
{
    protected $model = FieldOption::class;

    public function definition(): array
    {
        return [
            'form_field_id' => FormField::factory(),
            'value' => strtoupper(fake()->unique()->lexify('???')),
            'label' => fake()->word(),
            'display_order' => 1,
            'is_default' => false,
            'is_active' => true,
            'created_by' => null,
            'updated_by' => null,
        ];
    }

    public function default(): static
    {
        return $this->state(fn () => ['is_default' => true]);
    }
}
