<?php

namespace Database\Factories;

use App\Models\FormField;
use App\Models\Request;
use App\Models\RequestValue;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<RequestValue> */
class RequestValueFactory extends Factory
{
    protected $model = RequestValue::class;

    public function definition(): array
    {
        return [
            'request_id' => Request::factory(),
            'form_field_id' => FormField::factory(),
            'value' => fake()->word(),
        ];
    }
}
