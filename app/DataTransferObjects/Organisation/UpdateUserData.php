<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Organisation;

/**
 * ==========================================================================
 * UpdateUserData
 * ==========================================================================
 *
 * Partial update: only the fields actually present in the source array are
 * applied by toArray(). This matters because several User fields are
 * legitimately nullable (manager_id, phone, job_title, employee_number) -
 * a naive "skip null values" approach would make it impossible to ever
 * clear them (e.g. removing someone's manager). Instead, this DTO tracks
 * which keys were *provided*, independently of whether their value is null.
 *
 * Typical caller: the Admin "edit user" screen (Étape 13) sends only the
 * fields the form actually submitted.
 * ==========================================================================
 */
final readonly class UpdateUserData
{
    /**
     * @param array<string, mixed> $provided raw payload as provided by the
     *        caller - used only to know WHICH keys were sent, values are
     *        read from the typed properties below.
     */
    public function __construct(
        public ?int $entityId = null,
        public ?int $departmentId = null,
        public ?int $businessFunctionId = null,
        public ?int $applicationRoleId = null,
        public ?int $managerId = null,
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?bool $isActive = null,
        public ?string $employeeNumber = null,
        public ?string $jobTitle = null,
        private array $provided = [],
    ) {}

    /**
     * @param array<string, mixed> $data typically $request->validated()
     *        from an Admin "update user" Form Request using `sometimes`
     *        rules, so only submitted fields are present.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            entityId: array_key_exists('entity_id', $data) ? (int) $data['entity_id'] : null,
            departmentId: array_key_exists('department_id', $data) ? (int) $data['department_id'] : null,
            businessFunctionId: array_key_exists('business_function_id', $data) ? (int) $data['business_function_id'] : null,
            applicationRoleId: array_key_exists('application_role_id', $data) ? (int) $data['application_role_id'] : null,
            managerId: array_key_exists('manager_id', $data) ? $data['manager_id'] : null,
            firstName: $data['first_name'] ?? null,
            lastName: $data['last_name'] ?? null,
            email: $data['email'] ?? null,
            phone: array_key_exists('phone', $data) ? $data['phone'] : null,
            isActive: array_key_exists('is_active', $data) ? (bool) $data['is_active'] : null,
            employeeNumber: array_key_exists('employee_number', $data) ? $data['employee_number'] : null,
            jobTitle: array_key_exists('job_title', $data) ? $data['job_title'] : null,
            provided: $data,
        );
    }

    /**
     * Only the keys actually present in the original payload - safe to
     * hand directly to $user->update(...).
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $map = [
            'entity_id' => $this->entityId,
            'department_id' => $this->departmentId,
            'business_function_id' => $this->businessFunctionId,
            'application_role_id' => $this->applicationRoleId,
            'manager_id' => $this->managerId,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'phone' => $this->phone,
            'is_active' => $this->isActive,
            'employee_number' => $this->employeeNumber,
            'job_title' => $this->jobTitle,
        ];

        $sourceKeys = [
            'entity_id', 'department_id', 'business_function_id', 'application_role_id',
            'manager_id', 'first_name', 'last_name', 'email', 'phone', 'is_active',
            'employee_number', 'job_title',
        ];

        return array_intersect_key($map, array_flip(array_intersect($sourceKeys, array_keys($this->provided))));
    }

    public function isEmpty(): bool
    {
        return $this->toArray() === [];
    }
}
