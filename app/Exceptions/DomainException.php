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
 * le projet, ici. Le collegue qui travaille sur le domaine Organisation
 * doit etendre CETTE classe pour ses propres exceptions
 * (app/Exceptions/Organisation/*), jamais en recreer une autre.
 *
 * --------------------------------------------------------------------------
 * Regle d'or (Etape 5 du roadmap)
 * --------------------------------------------------------------------------
 * Un Service ne retourne JAMAIS `false`/`null` pour signaler un echec
 * metier - il leve une DomainException. Le Controller (Etape 12) attrape
 * DomainException une seule fois, generique, et laisse chaque sous-classe
 * dire elle-meme quel code HTTP et quel code d'erreur stable renvoyer
 * (grace a render() ci-dessous, une convention Laravel : si l'exception
 * definit render(), le Handler global l'appelle automatiquement - aucun
 * cablage supplementaire necessaire dans bootstrap/app.php).
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
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'error' => $this->errorCode(),
            'message' => $this->getMessage(),
        ], $this->httpStatus());
    }
}
