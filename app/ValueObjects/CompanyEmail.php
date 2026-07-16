<?php

declare(strict_types=1);

namespace App\ValueObjects;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;
use Stringable;

/**
 * ==========================================================================
 * CompanyEmail Value Object
 * ==========================================================================
 *
 * BR-08 : Company email is mandatory.
 *
 * Validates that a string is a well-formed email address AND, when
 * config('workflow.company_email_domains') is non-empty, that its domain
 * belongs to the configured whitelist. This is what the registration
 * screen (Etape 12) will use to reject personal email addresses.
 *
 * If no domain is configured yet (fresh install), only the email FORMAT
 * is validated - the platform never blocks everyone before an
 * administrator has had a chance to configure the real company domain(s).
 * ==========================================================================
 */
final readonly class CompanyEmail implements Castable, Stringable
{
    private function __construct(
        public string $value,
    ) {
    }

    /**
     * @throws InvalidArgumentException if the format is invalid or the
     *                                  domain is not allowed.
     */
    public static function fromString(string $value): self
    {
        $value = trim($value);

        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email address: \"{$value}\".");
        }

        $allowedDomains = array_map('strtolower', config('workflow.company_email_domains', []));

        if ($allowedDomains !== []) {
            $domain = strtolower(substr(strrchr($value, '@'), 1));

            if (! in_array($domain, $allowedDomains, true)) {
                throw new InvalidArgumentException(
                    "Email domain \"{$domain}\" is not an allowed company domain."
                );
            }
        }

        return new self($value);
    }

    public function domain(): string
    {
        return strtolower(substr(strrchr($this->value, '@'), 1));
    }

    public function equals(self $other): bool
    {
        return strtolower($this->value) === strtolower($other->value);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function castUsing(array $arguments): CastsAttributes
    {
        return new class implements CastsAttributes
        {
            public function get($model, string $key, $value, array $attributes): ?CompanyEmail
            {
                return $value === null ? null : CompanyEmail::fromString($value);
            }

            public function set($model, string $key, $value, array $attributes): ?string
            {
                if ($value === null) {
                    return null;
                }

                return $value instanceof CompanyEmail ? $value->value : CompanyEmail::fromString((string) $value)->value;
            }
        };
    }
}
