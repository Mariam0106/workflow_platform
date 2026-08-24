<?php

declare(strict_types=1);

namespace App\Services\Workflow;

use App\DataTransferObjects\Workflow\WorkflowStepData;
use App\Exceptions\Workflow\WorkflowNotModifiableException;
use App\Exceptions\Workflow\WorkflowStepNotDeletableException;
use App\Models\Workflow;
use App\Models\WorkflowStep;
use App\Services\AuditLogger;

/**
 * ==========================================================================
 * WorkflowStepService
 * ==========================================================================
 *
 * BR-19/32/33 : gère les Étapes d'un Workflow - toujours à travers son
 * Workflow parent (agrégat), toujours en brouillon uniquement (BR-26).
 *
 * BR-33 ("exactement une Étape de début") est garanti en PERMANENCE,
 * pas seulement vérifié à la publication : `setAsStart()` est le seul
 * moyen de marquer une Étape comme étape de début, et désigne
 * atomiquement celle-ci tout en retirant le marqueur de toute autre
 * Étape du même Workflow - jamais deux Étapes de début possibles, même
 * transitoirement (même schéma que FieldOption::markAsDefault()).
 * ==========================================================================
 */
class WorkflowStepService
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
    ) {}

    public function addStep(Workflow $workflow, WorkflowStepData $dto): WorkflowStep
    {
        $this->assertDraft($workflow);

        $nextOrder = (int) ($workflow->workflowSteps()->max('step_order') ?? 0) + 1;

        $step = $workflow->workflowSteps()->create([
            ...$dto->toArray(),
            'step_order' => $nextOrder,
        ]);

        $this->auditLogger->log(auth()->id(), 'workflow_step_created', 'WorkflowStep', $step->id, newValues: [
            'workflow_id' => $workflow->id, 'name' => $step->name, 'validator_type' => $step->validator_type->value,
        ]);

        return $step;
    }

    public function updateStep(Workflow $workflow, WorkflowStep $step, WorkflowStepData $dto): WorkflowStep
    {
        $this->assertDraft($workflow);

        $oldValues = ['name' => $step->name, 'validator_type' => $step->validator_type->value, 'is_end' => $step->is_end];

        $step->fill($dto->toArray());
        $step->save();

        $this->auditLogger->log(auth()->id(), 'workflow_step_updated', 'WorkflowStep', $step->id, $oldValues);

        return $step;
    }

    /**
     * @throws WorkflowStepNotDeletableException si l'Étape est encore
     *         reliée à une Transition (entrante ou sortante).
     */
    public function removeStep(Workflow $workflow, WorkflowStep $step): void
    {
        $this->assertDraft($workflow);

        if ($step->outgoingTransitions()->exists() || $step->incomingTransitions()->exists()) {
            throw WorkflowStepNotDeletableException::hasTransitions($step);
        }

        $stepId = $step->id;
        $name = $step->name;

        // forceDelete() plutôt que delete() (SoftDeletes) : "step_order"
        // et "code" sont uniques par Workflow AU NIVEAU BASE (contrainte
        // SQL, indifférente à deleted_at) - une simple suppression douce
        // laisserait une ligne "fantôme" qui bloquerait la réutilisation
        // du même numéro d'ordre ou du même code pour toute nouvelle
        // Étape (bug reproduit : UNIQUE constraint failed dès la
        // première Étape ajoutée après une suppression). Sûr ici
        // uniquement parce qu'une Étape n'est supprimable que tant que
        // son Workflow est en brouillon (assertDraft ci-dessus) - aucun
        // WorkflowStepHistory (restrictOnDelete) n'a jamais pu être créé
        // contre une Étape d'un Workflow qui n'a jamais été publié.
        $step->forceDelete();

        $this->auditLogger->log(auth()->id(), 'workflow_step_deleted', 'WorkflowStep', $stepId, ['name' => $name, 'workflow_id' => $workflow->id]);
    }

    /**
     * BR-33 : désigne cette Étape comme l'unique Étape de début du
     * Workflow - retire atomiquement le marqueur de toute autre Étape.
     */
    public function setAsStart(Workflow $workflow, WorkflowStep $step): void
    {
        $this->assertDraft($workflow);

        $workflow->workflowSteps()
            ->where('id', '!=', $step->id)
            ->update(['is_start' => false]);

        $step->update(['is_start' => true]);

        $this->auditLogger->log(auth()->id(), 'workflow_start_step_changed', 'WorkflowStep', $step->id, newValues: ['name' => $step->name]);
    }

    public function moveUp(Workflow $workflow, WorkflowStep $step): void
    {
        $this->assertDraft($workflow);
        $this->swapWithNeighbour($workflow, $step, direction: -1);
    }

    public function moveDown(Workflow $workflow, WorkflowStep $step): void
    {
        $this->assertDraft($workflow);
        $this->swapWithNeighbour($workflow, $step, direction: 1);
    }

    /**
     * Glisser-déposer (Workflow Designer) : réordonne TOUTES les Étapes
     * du Workflow d'un coup, dans l'ordre où leurs id apparaissent dans
     * $orderedStepIds - plus direct qu'une succession de moveUp()/
     * moveDown() pour un déplacement arbitraire (ex. la dernière Étape
     * déplacée tout en haut).
     *
     * @param list<int> $orderedStepIds tous les id d'Étapes de ce
     *        Workflow, dans le nouvel ordre d'affichage souhaité -
     *        validé en amont par ReorderWorkflowStepsRequest (ensemble
     *        exact, aucun id étranger, aucun manquant).
     */
    public function reorder(Workflow $workflow, array $orderedStepIds): void
    {
        $this->assertDraft($workflow);

        // BR-32 : "step_order" est unique au sein d'un Workflow - passer
        // par une plage de valeurs temporaires (au-delà de tout ordre
        // réel possible) évite tout conflit UNIQUE transitoire pendant
        // que les UPDATE s'exécutent les uns après les autres.
        foreach ($orderedStepIds as $index => $stepId) {
            $workflow->workflowSteps()->whereKey($stepId)->update(['step_order' => 1000 + $index]);
        }

        foreach ($orderedStepIds as $index => $stepId) {
            $workflow->workflowSteps()->whereKey($stepId)->update(['step_order' => $index + 1]);
        }
    }

    private function swapWithNeighbour(Workflow $workflow, WorkflowStep $step, int $direction): void
    {
        $neighbour = $workflow->workflowSteps()
            ->where('step_order', $direction < 0 ? '<' : '>', $step->step_order)
            ->orderBy('step_order', $direction < 0 ? 'desc' : 'asc')
            ->first();

        if ($neighbour === null) {
            return;
        }

        [$stepOrder, $neighbourOrder] = [$step->step_order, $neighbour->step_order];

        // BR-32 : "step_order" est unique au sein d'un Workflow -
        // passer par une valeur temporaire évite un conflit UNIQUE
        // transitoire quand les deux UPDATE s'exécutent l'un après
        // l'autre (l'un des deux verrait, l'espace d'un instant, la
        // même valeur que l'autre Étape).
        $step->update(['step_order' => -1]);
        $neighbour->update(['step_order' => $stepOrder]);
        $step->update(['step_order' => $neighbourOrder]);
    }

    /**
     * @throws WorkflowNotModifiableException
     */
    private function assertDraft(Workflow $workflow): void
    {
        if (! $workflow->isDraft()) {
            throw WorkflowNotModifiableException::becausePublished($workflow);
        }
    }
}
