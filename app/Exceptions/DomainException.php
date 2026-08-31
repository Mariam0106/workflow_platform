<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

/**
 * ==========================================================================
 * DomainException (base commune - PARTAGEE entre Organisation et Workflow)
 * ==========================================================================
 *
 * IMPORTANT : ce fichier est PARTAGE entre les deux domaines du projet
 * (Organisation et Workflow). Il ne doit exister qu'UNE seule fois dans
 * le projet, ici. Le collègue qui travaille sur le domaine Organisation
 * doit étendre CETTE classe pour ses propres exceptions
 * (app/Exceptions/Organisation/*), jamais en recréer une autre.
 *
 * --------------------------------------------------------------------------
 * Règle d'or
 * --------------------------------------------------------------------------
 * Un Service ne retourne JAMAIS `false`/`null` pour signaler un échec
 * metier - il leve une DomainException. Le Controller attrape
 * DomainException une seule fois, générique, et laisse chaque sous-classe
 * dire elle-même quel code HTTP et quel code d'erreur stable renvoyer
 * (grace a render() ci-dessous, une convention Laravel : si l'exception
 * definit render(), le Handler global l'appelle automatiquement - aucun
 * cablage supplementaire nécessaire dans bootstrap/app.php).
 * ==========================================================================
 */
abstract class DomainException extends RuntimeException
{
    /**
     * @param  string  $message    Message humain, sûr à afficher tel quel.
     * @param  string  $errorCode  Code stable, machine-readable (ex:
     *                             "workflow.no_eligible_transition"),
     *                             utilisé par le frontend/l'API pour
     *                             réagir sans parser le message.
     * @param  array<string, mixed>  $context  Données utiles au
     *                             débogage/log (jamais affichées telles
     *                             quelles à l'utilisateur final).
     * @param  int|null  $httpStatus  Surcharge ponctuelle du status HTTP
     *                             par défaut de la classe (voir
     *                             defaultHttpStatus()) - permet à deux
     *                             constructeurs nommés de la même classe
     *                             de renvoyer des status différents
     *                             (ex: "form non publié" = 422, mais
     *                             "validateur non autorisé" = 403).
     */
    public function __construct(
        string $message,
        protected readonly string $errorCode,
        protected readonly array $context = [],
        protected readonly ?int $httpStatus = null,
    ) {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return $this->errorCode;
    }

    public function context(): array
    {
        return $this->context;
    }

    /**
     * HTTP status to use when this exception reaches an API boundary.
     * Uses the per-instance override if provided, otherwise falls back
     * to the class-level default.
     */
    public function httpStatus(): int
    {
        return $this->httpStatus ?? $this->defaultHttpStatus();
    }

    /**
     * Each concrete exception class defines its own sensible default.
     */
    abstract protected function defaultHttpStatus(): int;

    /**
     * Laravel convention: if an exception defines render(), the global
     * Handler calls it automatically instead of the generic 500 page.
     *
     * CORRECTION (multi-role, BR-06) : ne renvoie du JSON brut que pour
     * les requetes qui en attendent reellement (API/XHR/`Accept:
     * application/json`) - jusqu'ici render() renvoyait TOUJOURS du
     * JSON, y compris pour un simple POST de formulaire HTML (ex :
     * ActiveRoleController, UserController::deactivate() sur soi-même).
     * Un navigateur recevant du JSON brut a la place d'une redirection
     * avec message d'erreur est un bug réel pour toute UI Back Office
     * - corrige ici, au point unique de rendu, plutôt que
     * dans chaque Controller.
     */
    public function render(Request $request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        if (! $request->expectsJson()) {
            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->withErrors(['error' => $this->getMessage()]);
        }

        return response()->json([
            'error' => $this->errorCode(),
            'message' => $this->getMessage(),
        ], $this->httpStatus());
    }
}
