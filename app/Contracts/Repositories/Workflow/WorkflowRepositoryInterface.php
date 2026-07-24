<?php

declare(strict_types=1);

namespace App\Contracts\Repositories\Workflow;

use App\Models\Workflow;
use Illuminate\Database\Eloquent\Collection;

/**
 * ==========================================================================
 * WorkflowRepositoryInterface
 * ==========================================================================
 *
 * Centralise les requetes metier autour de Workflow - notamment BR-25
 * ("New Requests use the latest published Version"), qui ne doit jamais
 * etre reecrite en dur (where('code', ...)->where('status', ...)->
 * latest()...) a plusieurs endroits du code.
 * ==========================================================================
 */
interface WorkflowRepositoryInterface
{
    public function findById(int $id): ?Workflow;

    /**
     * BR-25 : resout la derniere version PUBLIEE d'un Workflow, a
     * utiliser au moment de soumettre une nouvelle Request. Retourne
     * null si aucune version de ce code n'a jamais ete publiee.
     */
    public function findLatestPublishedVersion(string $code): ?Workflow;

    /**
     * Charge le Workflow avec ses Steps et Transitions - evite le
     * probleme N+1 quand le moteur (Etape 9) doit parcourir tout le
     * graphe d'un coup.
     */
    public function findWithStepsAndTransitions(int $id): ?Workflow;

    /**
     * Toutes les versions (Draft/Published/Archived confondues)
     * partageant le meme code - utile pour l'ecran d'administration
     * "historique des versions" (Etape 15).
     */
    public function findAllVersions(string $code): Collection;
}
