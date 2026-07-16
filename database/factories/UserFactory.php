<?php

namespace Database\Factories;

use App\Models\ApplicationRole;
use App\Models\BusinessFunction;
use App\Models\Department;
use App\Models\Entity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * NOTE (Etape 0) : reecrite entierement - l'ancienne version
     * generait "name"/"email_verified_at", des colonnes qui n'existent
     * pas sur notre table users, et ne fournissait aucune des 4
     * relations obligatoires (BR-03/04/05/06).
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'entity_id' => Entity::factory(),
            'department_id' => Department::factory(),
            'business_function_id' => BusinessFunction::factory(),
            'application_role_id' => ApplicationRole::factory(),
            'manager_id' => null,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '+' . fake()->numerify('##########'),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'employee_number' => fake()->unique()->numerify('EMP-#####'),
            'job_title' => fake()->jobTitle(),
            'is_active' => true,
        ];
    }
}
