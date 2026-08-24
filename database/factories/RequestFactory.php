<?php

namespace Database\Factories;

use App\Enums\RequestStatus;
use App\Models\Form;
use App\Models\Request;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowStep;
use App\ValueObjects\RequestReference;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Request> */
class RequestFactory extends Factory
{
    protected $model = Request::class;

    public function definition(): array
    {
        return [
            'form_id' => Form::factory(),
            'workflow_id' => Workflow::factory(),
            'requester_id' => User::factory(),
            'current_step_id' => WorkflowStep::factory(),
            'reference_number' => RequestReference::generate(fake()->unique()->numberBetween(1, 999999)),
            'workflow_version' => 1,
            'status' => RequestStatus::Draft,
            'submitted_at' => null,
            'completed_at' => null,
        ];
    }

    public function submitted(): static
    {
        return $this->state(fn () => [
            'status' => RequestStatus::Submitted,
            'submitted_at' => now(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => RequestStatus::Completed,
            'submitted_at' => now()->subDay(),
            'completed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status' => RequestStatus::Rejected,
            'submitted_at' => now()->subDay(),
            'completed_at' => now(),
        ]);
    }
}
