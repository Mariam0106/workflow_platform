<?php

declare(strict_types=1);

namespace App\Http\Requests\Organisation\Auth;

use App\Http\Requests\Organisation\Concerns\ValidatesCompanyEmailDomain;
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
 * Validates the registration form.
 *
 * BR-06 (sécurité) : le Rôle Applicatif n'est PLUS choisi ici - une
 * auto-inscription publique ne doit jamais pouvoir s'attribuer
 * elle-même un Rôle (ex. Administrator). UserService::register()
 * force systématiquement "User", et le vrai Rôle est attribué par un
 * Administrateur au moment de l'approbation.
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
