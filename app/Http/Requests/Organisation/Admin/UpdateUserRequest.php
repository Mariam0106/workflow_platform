<?php

declare(strict_types=1);

namespace App\Http\Requests\Organisation\Admin;

use App\Models\ApplicationRole;
use App\Models\BusinessFunction;
use App\Models\Department;
use App\Models\Entity;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * ==========================================================================
 * UpdateUserRequest (BackOffice - Étape 13)
 * ==========================================================================
 *
 * Partial update - every field is `sometimes`, matching UpdateUserData's
 * "only apply what was actually provided" semantics (Étape 6). Used by
 * both self-service (own profile) and Admin edits - UserController
 * decides which UserService method to call based on who $actor is; the
 * Policy (UserPolicy::update()) already restricted *who* may reach this
 * far, this Request only validates *what* was sent.
 *
 * NOTE : password change is deliberately out of scope here - a distinct
 * "change password" flow (current password confirmation, etc.) belongs
 * outside a generic profile-edit form; not built yet, out of Étape 11's
 * scope.
 * ==========================================================================
 */
class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('user'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var User $target */
        $target = $this->route('user');

        return [
            'first_name' => ['sometimes', 'string', 'max:100'],
            'last_name' => ['sometimes', 'string', 'max:100'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'employee_number' => ['sometimes', 'nullable', 'string', 'max:30'],
            'job_title' => ['sometimes', 'nullable', 'string', 'max:150'],

            // Champs admin-only en pratique (UserPolicy/UserService
            // filtrent déjà côté self-service, mais on valide le format
            // si jamais ils sont soumis).
            'entity_id' => ['sometimes', 'integer', Rule::exists(Entity::class, 'id')->where('is_active', true)],
            'department_id' => ['sometimes', 'integer', Rule::exists(Department::class, 'id')->where('is_active', true)],
            'business_function_id' => ['sometimes', 'integer', Rule::exists(BusinessFunction::class, 'id')->where('is_active', true)],
            'application_role_id' => ['sometimes', 'integer', Rule::exists(ApplicationRole::class, 'id')->where('is_active', true)],
            'manager_id' => ['sometimes', 'nullable', 'integer', Rule::exists(User::class, 'id')->whereNot('id', $target->id)],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
