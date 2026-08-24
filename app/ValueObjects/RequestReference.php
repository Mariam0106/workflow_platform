<?php

declare(strict_types=1);

namespace App\ValueObjects;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;
use Stringable;

/**
 * ==========================================================================
 * RequestReference Value Object
 * ==========================================================================
 *
 * BR-29 : Each Request receives a unique Reference Number, e.g.
 * "REQ-2026-000152".
 *
 * This class is the ONLY place in the codebase allowed to know the exact
 * format of a reference number. Nowhere else should a string be built by
 * hand with "REQ-" . date(...) - that logic lives here once, and reads its
 * prefix/separator/padding from config('workflow.*') (Etape 2), so an
 * administrator can change the format without touching PHP code.
 *
 * Immutable : once constructed, a RequestReference cannot be changed.
 * ==========================================================================
 */
final readonly class RequestReference implements Castable, Stringable
{
    private function __construct(
        public string $value,
    ) {
    }

    /**
     * Build a RequestReference from an already-formatted string
     * (e.g. read back from the database). Validates the format.
     *
     * @throws InvalidArgumentException if the string does not match the
     *                                  expected format.
     */
    public static function fromString(string $value): self
    {
        $pattern = self::pattern();

        if (! preg_match($pattern, $value)) {
            throw new InvalidArgumentException(
                "Invalid request reference format: \"{$value}\". Expected pattern: {$pattern}"
            );
        }

        return new self($value);
    }

    /**
     * Generate a brand new RequestReference for a given year and sequence
     * number. The sequence is expected to come from a dedicated, atomic
     * counter (e.g. a database sequence or a locked "last used number"
     * row) - this Value Object only formats it, it never decides what
     * the next number is.
     */
    public static function generate(int $sequenceNumber, ?int $year = null): self
    {
        $year ??= (int) date('Y');

        $prefix = (string) config('workflow.reference_prefix', 'REQ');
        $separator = (string) config('workflow.reference_separator', '-');
        $padding = (int) config('workflow.reference_sequence_padding', 6);

        $formatted = sprintf(
            '%s%s%d%s%s',
            $prefix,
            $separator,
            $year,
            $separator,
            str_pad((string) $sequenceNumber, $padding, '0', STR_PAD_LEFT)
        );

        return new self($formatted);
    }

    /**
     * Build the validation regex from the current configuration, so a
     * changed prefix/separator/padding in config/workflow.php is honoured
     * automatically.
     */
    private static function pattern(): string
    {
        $prefix = preg_quote((string) config('workflow.reference_prefix', 'REQ'), '/');
        $separator = preg_quote((string) config('workflow.reference_separator', '-'), '/');
        $padding = (int) config('workflow.reference_sequence_padding', 6);

        return "/^{$prefix}{$separator}\\d{4}{$separator}\\d{{$padding}}$/";
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Allow this Value Object to be used directly as an Eloquent cast:
     *
     *   protected function casts(): array
     *   {
     *       return ['reference_number' => RequestReference::class];
     *   }
     */
    public static function castUsing(array $arguments): CastsAttributes
    {
        return new class implements CastsAttributes
        {
            public function get($model, string $key, $value, array $attributes): ?RequestReference
            {
                return $value === null ? null : RequestReference::fromString($value);
            }

            public function set($model, string $key, $value, array $attributes): ?string
            {
                if ($value === null) {
                    return null;
                }

                return $value instanceof RequestReference ? $value->value : RequestReference::fromString((string) $value)->value;
            }
        };
    }
}
