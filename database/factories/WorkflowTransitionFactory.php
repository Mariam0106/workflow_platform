<?php

namespace Database\Factories;

use App\Enums\WorkflowPriority;
use App\Models\Workflow;
use App\Models\WorkflowStep;
use App\Models\WorkflowTransition;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<WorkflowTransition> */
class WorkflowTransitionFactory extends Factory
{
    protected $model = WorkflowTransition::class;

    public function definition(): array
    {
        return [
            'workflow_id' => Workflow::factory(),
            'from_step_id' => WorkflowStep::factory(),
            'to_step_id' => WorkflowStep::factory(),
            'action_name' => fake()->word(),
            'description' => fake()->sentence(),
            'priority' => WorkflowPriority::Medium->value,
            'is_default' => false,
            'is_active' => true,
        ];
    }
}
