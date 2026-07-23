<?php

declare(strict_types=1);

namespace App\Http\Requests\Organisation\Concerns;

/**
 * BR-08 : company email domain restriction, shared between every Form
 * Request that creates/edits a User's email (public self-registration -
 * RegisterUserRequest - and the Admin BackOffice - StoreUserRequest/
 * UpdateUserRequest). Extracted here so the rule is defined once; the
 * VO CompanyEmail (app/ValueObjects) remains the real, non-bypassable
 * enforcement at the Model layer - this is only the Form Request's UX
 * layer, giving a proper field-level error instead of a 500.
 */
trait ValidatesCompanyEmailDomain
{
    protected function companyEmailDomainRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            $allowedDomains = array_map('strtolower', config('workflow.company_email_domains', []));

            if ($allowedDomains === []) {
                return;
            }

            $domain = strtolower(substr((string) strrchr((string) $value, '@'), 1));

            if (! in_array($domain, $allowedDomains, true)) {
                $fail('Seules les adresses professionnelles ('.implode(', ', $allowedDomains).') sont autorisées.');
            }
        };
    }
}
