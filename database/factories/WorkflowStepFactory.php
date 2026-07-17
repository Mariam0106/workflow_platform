<?php

namespace Database\Factories;

use App\Enums\ValidatorType;
use App\Models\Workflow;
use App\Models\WorkflowStep;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<WorkflowStep> */
class WorkflowStepFactory extends Factory
{
    protected $model = WorkflowStep::class;

    public function definition(): array
    {
        return [
            'workflow_id' => Workflow::factory(),
            'code' => strtoupper(fake()->unique()->lexify('STEP-???')),
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'step_order' => 1,
            'is_start' => false,
            'is_end' => false,
            'validator_type' => ValidatorType::Role,
            'validator_reference' => null,
            'is_active' => true,
        ];
    }

    public function start(): static
    {
        return $this->state(fn () => ['is_start' => true]);
    }

    public function end(): static
    {
        return $this->state(fn () => ['is_end' => true]);
    }
}
