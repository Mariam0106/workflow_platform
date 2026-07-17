<?php

namespace Database\Factories;

use App\Enums\FormStatus;
use App\Models\Form;
use App\Models\FormCategory;
use App\Models\Workflow;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Form> */
class FormFactory extends Factory
{
    protected $model = Form::class;

    public function definition(): array
    {
        return [
            'form_category_id' => FormCategory::factory(),
            'workflow_id' => Workflow::factory(),
            'code' => strtoupper(fake()->unique()->lexify('FORM-????')),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'version' => 1,
            'status' => FormStatus::Draft,
            'published_at' => null,
            'is_active' => true,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => FormStatus::Published,
            'published_at' => now(),
        ]);
    }
}
