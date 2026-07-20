<?php

declare(strict_types=1);

namespace App\Http\Requests\Organisation\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * ==========================================================================
 * LoginRequest
 * ==========================================================================
 *
 * Validates the login form and performs the authentication attempt itself.
 *
 * Business Rules covered
 * --------------------------------------------------------------------------
 * BR-07  Only active Users may access the platform (see User::canLogin()
 *        / User query scope 'active', enforced by Auth::attempt() below
 *        via the `is_active` condition).
 *
 * Includes basic brute-force throttling (5 attempts / minute / email+ip) -
 * "gestion sécurisée des accès utilisateurs" from the cahier des charges.
 * ==========================================================================
 */
class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // BR-07 : inactive accounts never authenticate, even with the right password.
        if (! Auth::attempt(['email' => $this->string('email'), 'password' => $this->string('password'), 'is_active' => true], $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Identifiants incorrects ou compte désactivé.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            event(new Lockout($this));

            $seconds = RateLimiter::availableIn($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => "Trop de tentatives. Réessayez dans {$seconds} secondes.",
            ]);
        }
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
