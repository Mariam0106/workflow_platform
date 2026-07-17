<?php

namespace Database\Factories;

use App\Enums\ValidationDecision;
use App\Models\Request;
use App\Models\User;
use App\Models\Validation;
use App\Models\WorkflowStep;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Validation> */
class ValidationFactory extends Factory
{
    protected $model = Validation::class;

    public function definition(): array
    {
        return [
            'request_id' => Request::factory(),
            'workflow_step_id' => WorkflowStep::factory(),
            'validator_id' => User::factory(),
            'decision' => ValidationDecision::Approved,
            'comment' => null,
            'validated_at' => now(),
        ];
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'decision' => ValidationDecision::Rejected,
            'comment' => fake()->sentence(),
        ]);
    }
}
