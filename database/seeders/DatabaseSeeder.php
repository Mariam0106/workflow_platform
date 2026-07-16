<?php

namespace Database\Seeders;

use App\Models\ApplicationRole;
use App\Models\BusinessFunction;
use App\Models\Department;
use App\Models\Entity;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * NOTE (Etape 0) : seeder minimal, juste suffisant pour valider que
     * la chaine Entity -> Department -> BusinessFunction ->
     * ApplicationRole -> User fonctionne de bout en bout apres
     * stabilisation. Un WorkflowPlatformSeeder complet (avec un
     * Workflow entier, ses Steps, Transitions et Conditions) sera
     * ecrit a l'Etape 4 du roadmap.
     */
    public function run(): void
    {
        $entity = Entity::factory()->create([
            'code' => 'HQ',
            'name' => 'Headquarters',
        ]);

        $department = Department::factory()->create([
            'entity_id' => $entity->id,
            'code' => 'IT',
            'name' => 'Information Technology',
        ]);

        $businessFunction = BusinessFunction::factory()->create([
            'code' => 'DG',
            'name' => 'Direction Generale',
        ]);

        $adminRole = ApplicationRole::factory()->create([
            'code' => 'ADMIN',
            'name' => 'Administrator',
        ]);

        User::factory()->create([
            'entity_id' => $entity->id,
            'department_id' => $department->id,
            'business_function_id' => $businessFunction->id,
            'application_role_id' => $adminRole->id,
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
        ]);
    }
}
