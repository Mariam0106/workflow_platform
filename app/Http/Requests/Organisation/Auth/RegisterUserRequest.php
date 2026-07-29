<?php

declare(strict_types=1);

namespace App\Http\Requests\Organisation\Auth;

use App\Http\Requests\Organisation\Concerns\ValidatesCompanyEmailDomain;
use App\Models\ApplicationRole;
use App\Models\BusinessFunction;
use App\Models\Department;
use App\Models\Entity;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * ==========================================================================
 * RegisterUserRequest
 * ==========================================================================
 *
 * Validates the account-creation form.
 *
 * AJOUT (round 2 - demande client) : cette route n'est plus publique -
 * elle est désormais réservée aux Administrateurs (voir authorize() et
 * routes/organisation.php, gate `can:create`). Un Admin choisit
 * directement un ou plusieurs rôles autorisés (`role_ids[]`) pour le
 * nouveau compte ; il n'y a plus de champ "rôle actif" unique séparé -
 * le rôle actif initial est déterminé automatiquement (voir
 * RegisteredUserController::store()), et le nouvel utilisateur pourra
 * lui-même basculer entre ses rôles autorisés dès sa première connexion
 * via l'écran de sélection de rôle (RoleSelectionController).
 *
 * Business Rules covered
 * --------------------------------------------------------------------------
 * BR-03  Every User belongs to exactly one Entity.
 * BR-04  Every User belongs to exactly one Department.
 * BR-05  Every User has exactly one Business Function.
 * BR-06  Every User has exactly one Application Role (rôle actif) -
 *        toujours vrai, mais désormais dérivé de role_ids plutôt que
 *        soumis directement par le formulaire (round 2).
 * BR-08  Company email is mandatory (restricted to the configured
 *        domain(s), e.g. @saint-gobain.com - see config/workflow.php).
 *
 * NOTE : the domain restriction is validated twice on purpose:
 *  - here, with a clean Form Request rule -> good UX, a proper "email"
 *    field error message instead of a generic 500.
 *  - again in App\ValueObjects\CompanyEmail when the Model persists ->
 *    the real, non-bypassable guarantee (BR-08), in case this Request is
 *    ever skipped (Tinker, Seeder, future API...).
 * ==========================================================================
 */
class RegisterUserRequest extends FormRequest
{
    use ValidatesCompanyEmailDomain;

    /**
     * AJOUT (round 2 - demande client) : réservé aux Administrateurs -
     * même règle que StoreUserRequest (Admin/Étape 13), via
     * UserPolicy::create() (Étape 10, inchangée).
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', User::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class, 'email'),
                $this->companyEmailDomainRule(),
            ],

            'phone' => ['nullable', 'string', 'max:30'],

            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],

            'entity_id' => ['required', 'integer', Rule::exists(Entity::class, 'id')->where('is_active', true)],
            'department_id' => ['required', 'integer', Rule::exists(Department::class, 'id')->where('is_active', true)],
            'business_function_id' => ['required', 'integer', Rule::exists(BusinessFunction::class, 'id')->where('is_active', true)],

            // MODIFIÉ (round 2 - demande client) : remplace l'ancien champ
            // unique `application_role_id` (round 1 : optionnel en plus de
            // lui) - un Admin choisit désormais un OU PLUSIEURS rôles
            // autorisés directement, obligatoire (min:1).
            'role_ids' => ['required', 'array', 'min:1'],
            'role_ids.*' => ['integer', Rule::exists(ApplicationRole::class, 'id')->where('is_active', true)],

            // Nullable: only the top of the hierarchy has no manager (BR unspecified for root, deliberately allowed).
            'manager_id' => ['nullable', 'integer', Rule::exists(User::class, 'id')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'Un compte existe déjà avec cette adresse e-mail.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'role_ids.required' => 'Sélectionnez au moins un rôle.',
        ];
    }
}
