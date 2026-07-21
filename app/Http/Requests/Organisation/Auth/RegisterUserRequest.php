<?php

declare(strict_types=1);

namespace App\Http\Requests\Organisation\Auth;

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
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $allowedDomains = array_map('strtolower', config('workflow.company_email_domains', []));

                    if ($allowedDomains === []) {
                        return;
                    }

                    $domain = strtolower(substr((string) strrchr((string) $value, '@'), 1));

                    if (! in_array($domain, $allowedDomains, true)) {
                        $fail("L'inscription est réservée aux adresses professionnelles (".implode(', ', $allowedDomains).').');
                    }
                },
            ],

            'phone' => ['nullable', 'string', 'max:30'],

            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],

            'entity_id' => ['required', 'integer', Rule::exists(Entity::class, 'id')->where('is_active', true)],
            'department_id' => ['required', 'integer', Rule::exists(Department::class, 'id')->where('is_active', true)],
            'business_function_id' => ['required', 'integer', Rule::exists(BusinessFunction::class, 'id')->where('is_active', true)],
            'application_role_id' => ['required', 'integer', Rule::exists(ApplicationRole::class, 'id')->where('is_active', true)],

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
        ];
    }
}
