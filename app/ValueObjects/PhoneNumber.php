<?php

declare(strict_types=1);

namespace App\ValueObjects;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;
use Stringable;

/**
 * ==========================================================================
 * PhoneNumber Value Object
 * ==========================================================================
 *
 * Normalizes and validates a phone number: strips spaces, dots and dashes,
 * keeps a leading "+" for international numbers, and requires between 8
 * and 15 remaining digits (a deliberately loose bound - this platform does
 * not need strict E.164/country-code validation, just protection against
 * obviously malformed input).
 * ==========================================================================
 */
final readonly class PhoneNumber implements Castable, Stringable
{
    private function __construct(
        public string $value,
    ) {
    }

    /**
     * @throws InvalidArgumentException if the number cannot be normalized
     *                                  into a plausible phone number.
     */
    public static function fromString(string $value): self
    {
        $trimmed = trim($value);

        $normalized = preg_replace('/[\s.\-()]/', '', $trimmed);

        if ($normalized === null || $normalized === '') {
            throw new InvalidArgumentException('Phone number cannot be empty.');
        }

        if (! preg_match('/^\+?\d{8,15}$/', $normalized)) {
            throw new InvalidArgumentException("Invalid phone number: \"{$value}\".");
        }

        return new self($normalized);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function castUsing(array $arguments): CastsAttributes
    {
        return new class implements CastsAttributes
        {
            public function get($model, string $key, $value, array $attributes): ?PhoneNumber
            {
                return $value === null ? null : PhoneNumber::fromString($value);
            }

            public function set($model, string $key, $value, array $attributes): ?string
            {
                if ($value === null) {
                    return null;
                }

                return $value instanceof PhoneNumber ? $value->value : PhoneNumber::fromString((string) $value)->value;
            }
        };
    }
}
