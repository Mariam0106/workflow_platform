<?php

namespace Database\Factories;

use App\Models\Request;
use App\Models\WorkflowStep;
use App\Models\WorkflowStepHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<WorkflowStepHistory> */
class WorkflowStepHistoryFactory extends Factory
{
    protected $model = WorkflowStepHistory::class;

    public function definition(): array
    {
        return [
            'request_id' => Request::factory(),
            'workflow_step_id' => WorkflowStep::factory(),
            'workflow_transition_id' => null,
            'entered_at' => now(),
            'left_at' => null,
        ];
    }

    public function closed(): static
    {
        return $this->state(fn () => ['left_at' => now()]);
    }
}
