<?php

namespace Database\Factories;

use App\Enums\ApplicationRoleCode;
use App\Models\ApplicationRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApplicationRole>
 *
 * NOTE (Etape 1) : "code" est maintenant caste vers l'Enum
 * ApplicationRoleCode (ADMIN/USER/VALIDATOR uniquement, BR-06) - la
 * factory ne doit donc produire qu'une de ces 3 valeurs, jamais un
 * code invente aleatoirement.
 */
class ApplicationRoleFactory extends Factory
{
    protected $model = ApplicationRole::class;

    public function definition(): array
    {
        $case = fake()->randomElement(ApplicationRoleCode::cases());

        return [
            'code' => $case->value,
            'name' => $case->label(),
            'is_active' => true,
        ];
    }

    public function administrator(): static
    {
        return $this->state(fn () => [
            'code' => ApplicationRoleCode::Administrator->value,
            'name' => ApplicationRoleCode::Administrator->label(),
        ]);
    }

    public function validator(): static
    {
        return $this->state(fn () => [
            'code' => ApplicationRoleCode::Validator->value,
            'name' => ApplicationRoleCode::Validator->label(),
        ]);
    }
}
