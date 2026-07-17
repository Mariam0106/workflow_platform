<?php

namespace Database\Factories;

use App\Models\Form;
use App\Models\FormField;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<FormField> */
class FormFieldFactory extends Factory
{
    protected $model = FormField::class;

    public function definition(): array
    {
        return [
            'form_id' => Form::factory(),
            'label' => fake()->words(2, true),
            'technical_name' => fake()->unique()->lexify('field_????'),
            'field_type' => fake()->randomElement(['text', 'number', 'date', 'select', 'textarea']),
            'is_required' => true,
            'display_order' => 1,
            'placeholder' => null,
            'default_value' => null,
            'validation_rules' => null,
            'options' => null,
            'is_active' => true,
        ];
    }
}
