<?php

namespace Database\Factories;

use App\Enums\TransitionLogicalOperator;
use App\Enums\TransitionOperator;
use App\Models\FormField;
use App\Models\TransitionCondition;
use App\Models\WorkflowTransition;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TransitionCondition> */
class TransitionConditionFactory extends Factory
{
    protected $model = TransitionCondition::class;

    public function definition(): array
    {
        return [
            'workflow_transition_id' => WorkflowTransition::factory(),
            'form_field_id' => FormField::factory(),
            'operator' => TransitionOperator::GreaterThanOrEqual,
            'expected_value' => (string) fake()->numberBetween(1000, 100000),
            'logical_operator' => TransitionLogicalOperator::And,
            'execution_order' => 1,
            'is_active' => true,
        ];
    }
}
