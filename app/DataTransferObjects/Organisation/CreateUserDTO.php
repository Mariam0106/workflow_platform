<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Organisation;

/**
 * ==========================================================================
 * CreateUserDTO
 * ==========================================================================
 *
 * Carries the data needed to create a User, from the Controller/Request
 * layer down to UserService (Étape 8) - the Service never touches
 * `$request->validated()` directly, it receives a typed, immutable object.
 *
 * Two producers today:
 *  - RegisterUserRequest (Jalon J1, self-registration - is_active forced
 *    to true, password already in clear text, hashed later by the Service)
 *  - The future Admin "create user" screen (Étape 13)
 *
 * Business Rules covered
 * --------------------------------------------------------------------------
 * BR-03  Every User belongs to exactly one Entity.
 * BR-04  Every User belongs to exactly one Department.
 * BR-05  Every User has exactly one Business Function.
 * BR-06  Every User has exactly one Application Role.
 * BR-08  Company email is mandatory (enforced by CompanyEmail VO, not
 *        re-validated here - this DTO trusts its caller already validated).
 * ==========================================================================
 */
final readonly class CreateUserDTO
{
    public function __construct(
        public int $entityId,
        public int $departmentId,
        public int $businessFunctionId,
        public int $applicationRoleId,
        public ?int $managerId,
        public string $firstName,
        public string $lastName,
        public string $email,
        public ?string $phone,
        public string $password,
        public bool $isActive = true,
        public ?string $employeeNumber = null,
        public ?string $jobTitle = null,
    ) {}

    /**
     * @param array<string, mixed> $data typically $request->validated()
     */
    public static function fromArray(array $data): self
    {
        return new self(
            entityId: (int) $data['entity_id'],
            departmentId: (int) $data['department_id'],
            businessFunctionId: (int) $data['business_function_id'],
            applicationRoleId: (int) $data['application_role_id'],
            managerId: isset($data['manager_id']) ? (int) $data['manager_id'] : null,
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
     * Ready to hand to User::query()->create(...) - password left in clear
     * text on purpose, UserService is responsible for Hash::make() so the
     * hashing policy lives in exactly one place.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'entity_id' => $this->entityId,
            'department_id' => $this->departmentId,
            'business_function_id' => $this->businessFunctionId,
            'application_role_id' => $this->applicationRoleId,
            'manager_id' => $this->managerId,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => $this->password,
            'is_active' => $this->isActive,
            'employee_number' => $this->employeeNumber,
            'job_title' => $this->jobTitle,
        ];
    }
}
