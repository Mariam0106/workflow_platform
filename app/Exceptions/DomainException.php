<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * ==========================================================================
 * Domain Exception (base)
 * ==========================================================================
 *
 * Common ancestor for every business/domain exception thrown by the
 * platform, regardless of module.
 *
 * Rule (shared, see docs "Règles de répartition du travail", section 3) :
 * this class is built ONCE, together, before either module starts its
 * Étape 5. Nobody edits this file afterwards - each module only adds its
 * own subclasses:
 *
 *   App\Exceptions\Organisation\*   (UserNotFoundException, ...)
 *   App\Exceptions\Workflow\*       (WorkflowNotFoundException, ...)
 *
 * Responsibilities
 * --------------------------------------------------------------------------
 * • Carry a machine-readable error code, independent of the HTTP status.
 * • Carry an optional context array (for logs / audit trail).
 * • Know how to render itself as a JSON API response.
 *
 * ==========================================================================
 */
abstract class DomainException extends \Exception
{
    /**
     * Extra structured data about the failure (ids, attempted values...).
     * Never put sensitive data here (passwords, tokens).
     *
     * @var array<string, mixed>
     */
    protected array $context = [];

    public function __construct(string $message, protected string $errorCode, protected int $status = 400, array $context = [])
    {
        parent::__construct($message);

        $this->context = $context;
    }

    /**
     * Machine-readable error code (e.g. "user_not_found").
     * Stable across releases - front-end / API consumers may switch on it.
     */
    public function errorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * HTTP status code to use when this exception reaches the API layer.
     */
    public function status(): int
    {
        return $this->status;
    }

    /**
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return $this->context;
    }

    /**
     * Renders the exception as a JSON API response.
     *
     * Laravel calls render() automatically if it exists on the exception,
     * so Controllers never need to catch these by hand.
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => $this->errorCode,
                'message' => $this->getMessage(),
                'context' => $this->context,
            ],
        ], $this->status);
    }
}
