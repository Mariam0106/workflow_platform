<?php

namespace Database\Factories;

use App\Enums\WorkflowStatus;
use App\Models\Workflow;
use App\Models\WorkflowCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Workflow> */
class WorkflowFactory extends Factory
{
    protected $model = Workflow::class;

    public function definition(): array
    {
        return [
            'workflow_category_id' => WorkflowCategory::factory(),
            'code' => strtoupper(fake()->unique()->lexify('WF-????')),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'version' => 1,
            'status' => WorkflowStatus::Draft,
            'published_at' => null,
            'is_default' => false,
            'is_active' => true,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => WorkflowStatus::Published,
            'published_at' => now(),
        ]);
    }
}
