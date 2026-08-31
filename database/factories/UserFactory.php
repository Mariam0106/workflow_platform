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
     * NOTE: reecrite entierement - l'ancienne version
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
            'default_application_role_id' => ApplicationRole::factory(),
            'manager_id' => null,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
           // BR-08 : générer une adresse dans un domaine autorisé configuré.
            'email' => Str::slug(fake()->unique()->userName(), '.') . '@' . (config('workflow.company_email_domains.0') ?? 'workflow-platform.test'),
            'phone' => '+' . fake()->numerify('##########'),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'employee_number' => fake()->unique()->numerify('EMP-#####'),
            'job_title' => fake()->jobTitle(),
            'is_active' => true,
        ];
    }

    /**
     * BR-06 (multi-role) : chaque User cree par la factory obtient
     * automatiquement une ligne pivot pour son Role par defaut, même si
     * l'appelant n'a jamais entendu parler de user_application_roles -
     * garantit que TOUS les appels existants (Seeder, tests, autres
     * factories) restent valides sans modification : un User fabrique
     * via `User::factory()->create()` a toujours au moins un Role
     * autorise (BR-06), exactement comme avant l'ajout du multi-role.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (User $user): void {
            if ($user->default_application_role_id !== null) {
                $user->applicationRoles()->syncWithoutDetaching([$user->default_application_role_id]);
            }
        });
    }

    /**
     * Attaches one or more ADDITIONAL authorized Application Roles on
     * top of the default one (BR-06) - e.g.
     * `User::factory()->withAdditionalRoles($validatorRole)->create()`.
     */
    public function withAdditionalRoles(ApplicationRole ...$roles): static
    {
        return $this->afterCreating(function (User $user) use ($roles): void {
            $user->applicationRoles()->syncWithoutDetaching(
                array_map(static fn (ApplicationRole $role): int => $role->id, $roles),
            );
        });
    }
}
