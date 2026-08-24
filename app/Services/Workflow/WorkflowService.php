<?php

declare(strict_types=1);

namespace App\Services\Workflow;

use App\Contracts\Repositories\Workflow\WorkflowRepositoryInterface;
use App\DataTransferObjects\Workflow\CreateWorkflowData;
use App\DataTransferObjects\Workflow\UpdateWorkflowData;
use App\Enums\WorkflowStatus;
use App\Exceptions\Workflow\WorkflowNotFoundException;
use App\Exceptions\Workflow\WorkflowNotModifiableException;
use App\Exceptions\Workflow\WorkflowPublicationException;
use App\Models\User;
use App\Models\Workflow;
use App\Services\AuditLogger;
use Illuminate\Support\Arr;

/**
 * ==========================================================================
 * WorkflowService
 * ==========================================================================
 *
 * Write path for the Workflow Designer (BR-18 à BR-27, BR-32 à BR-34,
 * BR-53, BR-57/58/60). Authorization enforced at the Controller layer
 * via WorkflowPolicy - mirrors FormService exactly (même raisonnement,
 * même périmètre : pas encore de création de nouvelle VERSION d'un
 * Workflow déjà publié, seule la création initiale v1 est couverte).
 * ==========================================================================
 */
class WorkflowService
{
    public function __construct(
        private readonly WorkflowRepositoryInterface $workflows,
        private readonly AuditLogger $auditLogger,
    ) {}

    public function createDraft(CreateWorkflowData $dto, User $actor): Workflow
    {
        $workflow = new Workflow([
            'workflow_category_id' => $dto->workflowCategoryId,
            'code' => $dto->code,
            'name' => $dto->name,
            'description' => $dto->description,
            'version' => 1,
            'status' => WorkflowStatus::Draft,
            'is_default' => false,
            'created_by' => $actor->id,
        ]);

        $workflow = $this->workflows->save($workflow);

        $this->auditLogger->log($actor->id, 'workflow_created', 'Workflow', $workflow->id, newValues: [
            'code' => $workflow->code, 'name' => $workflow->name,
        ]);

        return $workflow;
    }

    /**
     * @throws WorkflowNotFoundException
     * @throws WorkflowNotModifiableException
     */
    public function updateDraft(UpdateWorkflowData $dto, User $actor): Workflow
    {
        $workflow = $this->findOrFail($dto->id);
        $this->assertDraft($workflow);

        $oldValues = Arr::only($workflow->getAttributes(), ['name', 'code', 'workflow_category_id', 'description']);

        $workflow->fill([
            ...$dto->toArray(),
            'updated_by' => $actor->id,
        ]);
        $workflow = $this->workflows->save($workflow);

        $this->auditLogger->log($actor->id, 'workflow_updated', 'Workflow', $workflow->id, $oldValues, $dto->toArray());

        return $workflow;
    }

    /**
     * BR-18 : au moins une Étape. BR-33 : exactement une Étape de
     * début (déjà garanti en permanence par WorkflowStepService::
     * setAsStart(), donc vérifié ici comme filet de sécurité plutôt que
     * comme flux nominal). BR-34 : au moins une Étape de fin.
     *
     * @throws WorkflowPublicationException
     */
    public function publish(int $workflowId, User $actor): Workflow
    {
        $workflow = $this->findOrFail($workflowId);
        $this->assertDraft($workflow);

        $steps = $workflow->workflowSteps()->active()->get();

        if ($steps->isEmpty()) {
            throw WorkflowPublicationException::noSteps($workflow);
        }

        if ($steps->where('is_start', true)->isEmpty()) {
            throw WorkflowPublicationException::noStartStep($workflow);
        }

        if ($steps->where('is_end', true)->isEmpty()) {
            throw WorkflowPublicationException::noEndStep($workflow);
        }

        $workflow->status = WorkflowStatus::Published;
        $workflow->published_at = now();
        $workflow->published_by = $actor->id;
        $workflow = $this->workflows->save($workflow);

        $this->auditLogger->log($actor->id, 'workflow_published', 'Workflow', $workflow->id, newValues: ['status' => WorkflowStatus::Published->value]);

        return $workflow;
    }

    /**
     * Suppression définitive, réservée à un Workflow encore en
     * brouillon ET sans aucun Formulaire (brouillon ou publié) qui le
     * référence déjà, en filet de sécurité supplémentaire - pour un
     * brouillon créé par erreur, pas pour un Workflow qui a déjà servi.
     * Contrairement à archive(), ceci efface réellement le Workflow et
     * ses Étapes/Transitions.
     *
     * @throws WorkflowNotFoundException si le Workflow n'existe pas
     * @throws WorkflowNotModifiableException si le Workflow est publié/
     *                                         archivé ou déjà référencé
     *                                         par un Formulaire
     */
    public function delete(int $workflowId, User $actor): void
    {
        $workflow = $this->findOrFail($workflowId);

        if (! $workflow->isDraft()) {
            throw WorkflowNotModifiableException::becausePublished($workflow);
        }

        if ($workflow->forms()->exists()) {
            throw WorkflowNotModifiableException::becauseInUse($workflow);
        }

        $this->auditLogger->log($actor->id, 'workflow_deleted', 'Workflow', $workflow->id, ['code' => $workflow->code, 'name' => $workflow->name]);

        $workflow->delete();
    }

    /**
     * BR-16 (par analogie) : un Workflow archivé reste référencé par
     * les Requests historiques mais ne peut plus être associé à un
     * nouveau Formulaire publié (BR-30).
     *
     * @throws WorkflowNotFoundException
     */
    public function archive(int $workflowId, User $actor): Workflow
    {
        $workflow = $this->findOrFail($workflowId);

        $workflow->status = WorkflowStatus::Archived;
        $workflow->updated_by = $actor->id;
        $workflow = $this->workflows->save($workflow);

        $this->auditLogger->log($actor->id, 'workflow_archived', 'Workflow', $workflow->id);

        return $workflow;
    }

    /**
     * Duplique un Workflow complet - Catégorie, Étapes, Transitions ET
     * Conditions - dans un nouveau Workflow Draft v1 totalement
     * indépendant (même logique que BR-17 côté Form). La partie
     * délicate est de faire correspondre les nouvelles Transitions aux
     * nouvelles Étapes copiées (jamais aux anciennes) : une table de
     * correspondance ancien id -> nouvel id est construite au fur et à
     * mesure de la copie des Étapes.
     *
     * @throws WorkflowNotFoundException
     */
    public function duplicate(int $workflowId, User $actor): Workflow
    {
        $source = $this->findOrFail($workflowId);
        $source->load(['workflowSteps.outgoingTransitions.transitionConditions']);

        $copy = $this->workflows->save(new Workflow([
            'workflow_category_id' => $source->workflow_category_id,
            'code' => $this->nextAvailableCopyCode($source->code),
            'name' => $source->name . ' (copie)',
            'description' => $source->description,
            'version' => 1,
            'status' => WorkflowStatus::Draft,
            'is_default' => false,
            'created_by' => $actor->id,
        ]));

        $stepIdMap = [];

        foreach ($source->workflowSteps as $step) {
            $newStep = $copy->workflowSteps()->create([
                'code' => $step->code,
                'name' => $step->name,
                'description' => $step->description,
                'step_order' => $step->step_order,
                'is_start' => $step->is_start,
                'is_end' => $step->is_end,
                'validator_type' => $step->validator_type,
                'validator_reference' => $step->validator_reference,
                'is_active' => $step->is_active,
            ]);

            $stepIdMap[$step->id] = $newStep->id;
        }

        foreach ($source->workflowSteps as $step) {
            foreach ($step->outgoingTransitions as $transition) {
                $newTransition = $copy->workflowTransitions()->create([
                    'from_step_id' => $stepIdMap[$transition->from_step_id],
                    'to_step_id' => $stepIdMap[$transition->to_step_id],
                    'action_name' => $transition->action_name,
                    'description' => $transition->description,
                    'priority' => $transition->priority,
                    'is_default' => $transition->is_default,
                    'is_active' => $transition->is_active,
                ]);

                foreach ($transition->transitionConditions as $condition) {
                    $newTransition->transitionConditions()->create([
                        // form_field_id n'est PAS remappé : une
                        // Condition pointe sur le champ d'un Formulaire
                        // (indépendant du Workflow), donc reste valide
                        // telle quelle sur la copie.
                        'form_field_id' => $condition->form_field_id,
                        'operator' => $condition->operator,
                        'expected_value' => $condition->expected_value,
                        'logical_operator' => $condition->logical_operator,
                        'execution_order' => $condition->execution_order,
                        'is_active' => $condition->is_active,
                    ]);
                }
            }
        }

        $this->auditLogger->log($actor->id, 'workflow_duplicated', 'Workflow', $copy->id, newValues: ['duplicated_from' => $source->id, 'code' => $copy->code]);

        return $copy;
    }

    /**
     * @throws WorkflowNotFoundException
     */
    private function findOrFail(int $id): Workflow
    {
        $workflow = $this->workflows->findById($id);

        if ($workflow === null) {
            throw WorkflowNotFoundException::withId($id);
        }

        return $workflow;
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

    private function nextAvailableCopyCode(string $originalCode): string
    {
        $attempt = 1;
        do {
            $candidate = "{$originalCode}-COPY" . ($attempt > 1 ? "-{$attempt}" : '');
            $attempt++;
        } while ($this->workflows->existsByCode($candidate));

        return $candidate;
    }
}
