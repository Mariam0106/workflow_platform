<?php

declare(strict_types=1);

namespace App\Contracts\Services\Workflow;

use App\DataTransferObjects\Workflow\RecordValidationData;
use App\DataTransferObjects\Workflow\SubmitRequestData;
use App\Exceptions\Workflow\RequestException;
use App\Exceptions\Workflow\TransitionException;
use App\Exceptions\Workflow\ValidationException;
use App\Models\Request;
use App\Models\Validation;

/**
 * ==========================================================================
 * WorkflowEngineInterface
 * ==========================================================================
 *
 * Le contrat unique que les Controllers (Etape 12) appelleront - jamais
 * directement une classe concrete. C'est ce qui permettra demain de
 * remplacer l'implementation (par ex. brancher un moteur de regles
 * externe) sans toucher un seul Controller.
 * ==========================================================================
 */
interface WorkflowEngineInterface
{
    /**
     * BR-28 : cree une Request a partir d'un Form publie, l'initialise
     * sur la premiere Step (is_start) du Workflow resolu en derniere
     * version publiee (BR-25).
     *
     * @throws RequestException si le Form n'est pas publie (BR-28).
     */
    public function submit(SubmitRequestData $data): Request;

    /**
     * BR-36/38/39/41 : enregistre la decision d'un Validateur.
     * Si Approved, selectionne et execute la Transition eligible
     * (BR-21/22/23) et avance la Request vers le Step suivant.
     * Si Rejected, cloture immediatement le Workflow (BR-39).
     *
     * @throws ValidationException si l'utilisateur n'est pas le
     *                              Validateur attendu pour ce Step (BR-36).
     * @throws TransitionException si aucune Transition eligible n'est
     *                              trouvee alors que la Request doit avancer.
     */
    public function recordValidation(RecordValidationData $data): Validation;
}
