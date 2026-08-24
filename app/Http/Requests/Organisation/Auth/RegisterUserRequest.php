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
use Illuminate\Validation\Validator;

/**
 * ==========================================================================
 * RegisterUserRequest
 * ==========================================================================
 *
 * Validates the registration form (Jalon J1 - auth minimale).
 *
 * Business Rules covered
 * --------------------------------------------------------------------------
 * BR-03  Every User belongs to exactly one Entity.
 * BR-04  Every User belongs to exactly one Department.
 * BR-05  Every User has exactly one Business Function.
 * BR-06  Every User has exactly one Application Role.
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

    public function authorize(): bool
    {
        // Registration is public - anyone with a company email may sign up.
        return true;
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

            // BR-06 : au moins un Role Applicatif autorise des
            // l'inscription. Un inscrit ne se donne generalement qu'un
            // seul Role a ce stade (ADMIN reste ensuite attribuable par
            // un Administrateur via le BackOffice), mais le formulaire
            // public reste compatible multi-selection au cas ou.
            'application_role_ids' => ['required', 'array', 'min:1'],
            'application_role_ids.*' => ['integer', 'distinct', Rule::exists(ApplicationRole::class, 'id')->where('is_active', true)],
            'default_application_role_id' => ['required', 'integer', Rule::exists(ApplicationRole::class, 'id')->where('is_active', true)],

            // Nullable: only the top of the hierarchy has no manager (BR unspecified for root, deliberately allowed).
            'manager_id' => ['nullable', 'integer', Rule::exists(User::class, 'id')],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $roleIds = array_map('intval', (array) $this->input('application_role_ids', []));
            $defaultId = (int) $this->input('default_application_role_id');

            if ($defaultId !== 0 && $roleIds !== [] && ! in_array($defaultId, $roleIds, true)) {
                $validator->errors()->add(
                    'default_application_role_id',
                    'Le rôle par défaut doit faire partie des rôles applicatifs sélectionnés.',
                );
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'Un compte existe déjà avec cette adresse e-mail.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ];
    }
}
