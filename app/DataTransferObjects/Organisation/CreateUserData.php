<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Organisation;

/**
 * ==========================================================================
 * CreateUserData
 * ==========================================================================
 *
 * Carries the data needed to create a User, from the Controller/Request
 * layer down to UserService - the Service never touches
 * `$request->validated()` directly, it receives a typed, immutable object.
 *
 * Two producers today:
 *  - RegisterUserRequest (Jalon J1, self-registration - is_active forced
 *    to true, password already in clear text, hashed later by the Service)
 * - The future Admin "create user" screen
 *
 * Business Rules covered
 * --------------------------------------------------------------------------
 * BR-03  Every User belongs to exactly one Entity.
 * BR-04  Every User belongs to exactly one Department.
 * BR-05  Every User has exactly one Business Function.
 * BR-06  Every User holds at least one Application Role, and may hold
 *        several. `applicationRoleIds` carries every authorized Role;
 *        `defaultApplicationRoleId` (one of them) is the Role proposed
 *        at first login and used as the non-nullable fallback column.
 * BR-08  Company email is mandatory (enforced by CompanyEmail VO, not
 *        re-validated here - this DTO trusts its caller already validated).
 * ==========================================================================
 */
final readonly class CreateUserData
{
    /**
     * @param list<int> $applicationRoleIds every authorized Application
     *        Role id (BR-06) - always contains at least
     *        $defaultApplicationRoleId.
     */
    public function __construct(
        public int $entityId,
        public int $departmentId,
        public int $businessFunctionId,
        public array $applicationRoleIds,
        public int $defaultApplicationRoleId,
        public ?int $managerId,
        public string $firstName,
        public string $lastName,
        public string $email,
        public ?string $phone,
        public string $password,
        public bool $isActive = true,
        public \App\Enums\RegistrationStatus $registrationStatus = \App\Enums\RegistrationStatus::Approved,
        public ?string $employeeNumber = null,
        public ?string $jobTitle = null,
    ) {}

    /**
     * @param array<string, mixed> $data typically $request->validated()
     */
    public static function fromArray(array $data): self
    {
        $roleIds = array_values(array_unique(array_map(
            static fn ($id): int => (int) $id,
            $data['application_role_ids'] ?? [],
        )));

        $defaultRoleId = (int) ($data['default_application_role_id'] ?? ($roleIds[0] ?? 0));

        // The default Role must always be part of the authorized set,
        // even if the caller forgot to include it explicitly - a
        // FormRequest should already guarantee this (see
        // StoreUserRequest/RegisterUserRequest), this is a defensive
        // fallback for any other caller (Artisan, Tinker, tests).
        if (! in_array($defaultRoleId, $roleIds, true)) {
            $roleIds[] = $defaultRoleId;
        }

        return new self(
            entityId: (int) $data['entity_id'],
            departmentId: (int) $data['department_id'],
            businessFunctionId: (int) $data['business_function_id'],
            applicationRoleIds: $roleIds,
            defaultApplicationRoleId: $defaultRoleId,
            managerId: isset($data['manager_id']) && ! blank($data['manager_id']) ? (int) $data['manager_id'] : null,
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            email: $data['email'],
            phone: $data['phone'] ?? null,
            password: $data['password'],
            isActive: (bool) ($data['is_active'] ?? true),
            employeeNumber: $data['employee_number'] ?? null,
            jobTitle: $data['job_title'] ?? null,
        );
    }

    /**
     * Columns only - ready to hand to User::query()->create(...).
     * Password left in clear text on purpose, UserService is responsible
     * for Hash::make() so the hashing policy lives in exactly one place.
     * `application_role_ids` is deliberately NOT included here: it is
     * not a column, it is synced onto the user_application_roles pivot
     * separately (see UserRepository::createFromData()).
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'entity_id' => $this->entityId,
            'department_id' => $this->departmentId,
            'business_function_id' => $this->businessFunctionId,
            'default_application_role_id' => $this->defaultApplicationRoleId,
            'manager_id' => $this->managerId,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => $this->password,
            'is_active' => $this->isActive,
            'registration_status' => $this->registrationStatus->value,
            'employee_number' => $this->employeeNumber,
            'job_title' => $this->jobTitle,
        ];
    }
}