<?php

declare(strict_types=1);

namespace App\Http\Requests\Organisation\Admin;

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
 * StoreUserRequest (BackOffice - Étape 13)
 * ==========================================================================
 *
 * Admin-initiated User creation - UserController::store() ->
 * UserService::createByAdmin(). Same core rules as the public
 * RegisterUserRequest (Jalon J1), plus is_active (an Admin may create a
 * pre-deactivated account, e.g. onboarding ahead of someone's start date).
 * ==========================================================================
 */
class StoreUserRequest extends FormRequest
{
    use ValidatesCompanyEmailDomain;

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
                'required', 'string', 'email', 'max:255',
                Rule::unique(User::class, 'email'),
                $this->companyEmailDomainRule(),
            ],

            'phone' => ['nullable', 'string', 'max:30'],
            'employee_number' => ['nullable', 'string', 'max:30'],
            'job_title' => ['nullable', 'string', 'max:150'],

            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],

            'entity_id' => ['required', 'integer', Rule::exists(Entity::class, 'id')->where('is_active', true)],
            'department_id' => ['required', 'integer', Rule::exists(Department::class, 'id')->where('is_active', true)],
            'business_function_id' => ['required', 'integer', Rule::exists(BusinessFunction::class, 'id')->where('is_active', true)],

            // BR-06 : au moins un Role Applicatif autorise, plusieurs
            // possibles ; default_application_role_id doit en faire
            // partie (voir withValidator() ci-dessous).
            'application_role_ids' => ['required', 'array', 'min:1'],
            'application_role_ids.*' => ['integer', 'distinct', Rule::exists(ApplicationRole::class, 'id')->where('is_active', true)],
            'default_application_role_id' => ['required', 'integer', Rule::exists(ApplicationRole::class, 'id')->where('is_active', true)],

            'manager_id' => ['nullable', 'integer', Rule::exists(User::class, 'id')],

            'is_active' => ['boolean'],
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
                    'Le rôle par défaut doit faire partie des rôles applicatifs attribués.',
                );
            }
        });
    }
}
