<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\ApplicationRoleCode;
use App\Models\ApplicationRole;
use App\Models\BusinessFunction;
use App\Models\Department;
use App\Models\Entity;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * ==========================================================================
 * CreateAdminCommand
 * ==========================================================================
 *
 * `php artisan app:create-admin` - à lancer UNE FOIS lors du déploiement
 * réel, pour créer le premier compte Administrateur de production. Ne
 * touche à aucune donnée de démonstration (DatabaseSeeder) : cette
 * commande est volontairement indépendante, réutilisable sur une base
 * vide comme sur une base qui contient déjà de l'Organisation réelle.
 *
 * Réutilise l'Entité/le Département/la Fonction Métier existants si
 * l'Administrateur en indique un code déjà présent, sinon les crée -
 * BR-03/04/05 (chaque Utilisateur en a exactement un).
 * ==========================================================================
 */
class CreateAdminCommand extends Command
{
    protected $signature = 'app:create-admin';

    protected $description = 'Crée le premier compte Administrateur réel (déploiement production)';

    public function handle(): int
    {
        $this->info('Création du compte Administrateur de production.');
        $this->newLine();

        // --- Organisation (BR-01/02) -----------------------------------
        $entity = $this->resolveEntity();
        $department = $this->resolveDepartment($entity);
        $businessFunction = $this->resolveBusinessFunction();

        // --- Identité de l'Administrateur -------------------------------
        $firstName = $this->ask('Prénom');
        $lastName = $this->ask('Nom');
        $email = $this->ask('Adresse e-mail professionnelle (identifiant de connexion)');

        $validator = Validator::make(
            ['first_name' => $firstName, 'last_name' => $lastName, 'email' => $email],
            [
                'first_name' => ['required', 'string', 'max:100'],
                'last_name' => ['required', 'string', 'max:100'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            ],
        );

        if ($validator->fails()) {
            $this->error('Informations invalides :');
            foreach ($validator->errors()->all() as $message) {
                $this->line("  - {$message}");
            }

            return self::FAILURE;
        }

        $password = $this->secret('Mot de passe (min. 8 caractères)');
        $passwordConfirmation = $this->secret('Confirmer le mot de passe');

        if ($password !== $passwordConfirmation) {
            $this->error('Les deux mots de passe ne correspondent pas.');

            return self::FAILURE;
        }

        if (mb_strlen((string) $password) < 8) {
            $this->error('Le mot de passe doit contenir au moins 8 caractères.');

            return self::FAILURE;
        }

        // BR-06 : les 3 Rôles Applicatifs (Administrateur/Utilisateur/
        // Validateur) sont fixes et doivent toujours exister, quel que
        // soit l'Utilisateur créé - pas seulement Administrateur.
        foreach (ApplicationRoleCode::cases() as $roleCode) {
            ApplicationRole::query()->firstOrCreate(
                ['code' => $roleCode->value],
                ['name' => $roleCode->label(), 'is_active' => true],
            );
        }

        $adminRole = ApplicationRole::query()->where('code', ApplicationRoleCode::Administrator->value)->first();

        $admin = DB::transaction(function () use ($entity, $department, $businessFunction, $adminRole, $firstName, $lastName, $email, $password) {
            $admin = User::create([
                'entity_id' => $entity->id,
                'department_id' => $department->id,
                'business_function_id' => $businessFunction->id,
                'default_application_role_id' => $adminRole->id,
                'manager_id' => null,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'password' => Hash::make($password),
                'is_active' => true,
            ]);

            $admin->applicationRoles()->syncWithoutDetaching([$adminRole->id]);

            return $admin;
        });

        $this->newLine();
        $this->info("Administrateur « {$admin->first_name} {$admin->last_name} » créé ({$admin->email}).");
        $this->line('Il peut se connecter dès maintenant avec le mot de passe choisi.');

        return self::SUCCESS;
    }

    private function resolveEntity(): Entity
    {
        $existing = Entity::query()->orderBy('name')->get();

        if ($existing->isNotEmpty()) {
            $this->line('Entités existantes : ' . $existing->pluck('name')->implode(', '));

            if ($this->confirm('Rattacher l\'Administrateur à une Entité existante ?', true)) {
                $code = $this->ask('Code de l\'Entité (ex. HQ)');
                $entity = $existing->firstWhere('code', $code);

                if ($entity !== null) {
                    return $entity;
                }

                $this->warn("Aucune Entité avec le code « {$code} » - création d'une nouvelle Entité.");
            }
        }

        return Entity::create([
            'code' => $this->ask('Code de la nouvelle Entité (ex. HQ)'),
            'name' => $this->ask('Nom de la nouvelle Entité (ex. Siège)'),
            'is_active' => true,
        ]);
    }

    private function resolveDepartment(Entity $entity): Department
    {
        $existing = Department::query()->where('entity_id', $entity->id)->orderBy('name')->get();

        if ($existing->isNotEmpty()) {
            $this->line('Départements existants pour cette Entité : ' . $existing->pluck('name')->implode(', '));

            if ($this->confirm('Rattacher l\'Administrateur à un Département existant ?', true)) {
                $code = $this->ask('Code du Département (ex. IT)');
                $department = $existing->firstWhere('code', $code);

                if ($department !== null) {
                    return $department;
                }

                $this->warn("Aucun Département avec le code « {$code} » - création d'un nouveau Département.");
            }
        }

        return Department::create([
            'entity_id' => $entity->id,
            'code' => $this->ask('Code du nouveau Département (ex. IT)'),
            'name' => $this->ask('Nom du nouveau Département (ex. Direction des Systèmes d\'Information)'),
            'is_active' => true,
        ]);
    }

    private function resolveBusinessFunction(): BusinessFunction
    {
        $existing = BusinessFunction::query()->orderBy('name')->get();

        if ($existing->isNotEmpty()) {
            $this->line('Fonctions Métier existantes : ' . $existing->pluck('name')->implode(', '));

            if ($this->confirm('Utiliser une Fonction Métier existante ?', true)) {
                $code = $this->ask('Code de la Fonction Métier (ex. DG)');
                $businessFunction = $existing->firstWhere('code', $code);

                if ($businessFunction !== null) {
                    return $businessFunction;
                }

                $this->warn("Aucune Fonction Métier avec le code « {$code} » - création d'une nouvelle.");
            }
        }

        return BusinessFunction::create([
            'code' => $this->ask('Code de la nouvelle Fonction Métier (ex. DG)'),
            'name' => $this->ask('Nom de la nouvelle Fonction Métier (ex. Direction Générale)'),
            'is_active' => true,
        ]);
    }
}