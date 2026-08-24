<?php

declare(strict_types=1);

namespace App\Services\Workflow;

use App\Contracts\Repositories\Workflow\FormRepositoryInterface;
use App\Contracts\Repositories\Workflow\WorkflowRepositoryInterface;
use App\DataTransferObjects\Workflow\CreateFormData;
use App\DataTransferObjects\Workflow\UpdateFormData;
use App\Enums\FormPriority;
use App\Enums\FormStatus;
use App\Exceptions\Workflow\FormNotModifiableException;
use App\Exceptions\Workflow\FormNotPublishedException;
use App\Exceptions\Workflow\FormPublicationException;
use App\Models\Form;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Support\Arr;

/**
 * ==========================================================================
 * FormService
 * ==========================================================================
 *
 * Write path for the Dynamic Form Builder (BR-10 à BR-18, BR-53, BR-56/57).
 * Authorization enforced at the Controller layer via FormPolicy - see
 * FormCategoryService for why this Service stays a thin persistence +
 * lifecycle layer rather than duplicating role checks.
 *
 * NOTE (périmètre de cette phase) : ne gère pas encore la création
 * d'une NOUVELLE VERSION d'un Form déjà publié (BR-18 second volet) -
 * seule la création initiale (toujours v1) est couverte. Un Form publié
 * reste immuable (FormNotModifiableException) ; le versionnement
 * complet est laissé pour une itération suivante du Form Builder.
 * ==========================================================================
 */
class FormService
{
    public function __construct(
        private readonly FormRepositoryInterface $forms,
        private readonly WorkflowRepositoryInterface $workflows,
        private readonly AuditLogger $auditLogger,
    ) {}

    public function createDraft(CreateFormData $dto, User $actor): Form
    {
        $form = new Form([
            'form_category_id' => $dto->formCategoryId,
            'workflow_id' => $dto->workflowId,
            'code' => $dto->code,
            'name' => $dto->name,
            'description' => $dto->description,
            'priority' => $dto->priority->value,
            'version' => 1,
            'status' => FormStatus::Draft,
            'created_by' => $actor->id,
        ]);

        $form = $this->forms->save($form);

        $this->auditLogger->log($actor->id, 'form_created', 'Form', $form->id, newValues: [
            'code' => $form->code, 'name' => $form->name, 'workflow_id' => $form->workflow_id,
        ]);

        return $form;
    }

    /**
     * @throws FormNotPublishedException si le Form n'existe pas
     * @throws FormNotModifiableException si le Form est déjà publié (BR-18)
     */
    public function updateDraft(UpdateFormData $dto, User $actor): Form
    {
        $form = $this->findOrFail($dto->id);
        $this->assertModifiable($form);

        $oldValues = Arr::only($form->getAttributes(), ['name', 'code', 'form_category_id', 'workflow_id', 'description']);

        $form->fill([
            ...$dto->toArray(),
            'updated_by' => $actor->id,
        ]);
        $form = $this->forms->save($form);

        $this->auditLogger->log($actor->id, 'form_updated', 'Form', $form->id, $oldValues, $dto->toArray());

        return $form;
    }

    /**
     * BR-15/BR-30/BR-31 : un Form ne peut être publié que s'il a au
     * moins un Champ actif (BR-13) et que son Workflow est lui-même
     * publié (BR-30).
     *
     * @throws FormPublicationException
     */
    public function publish(int $formId, User $actor): Form
    {
        $form = $this->findOrFail($formId);
        $this->assertModifiable($form);

        if ($form->formFields()->active()->doesntExist()) {
            throw FormPublicationException::noFields($form);
        }

        $workflow = $this->workflows->findById($form->workflow_id);

        if ($workflow === null || ! $workflow->isPublished()) {
            throw FormPublicationException::workflowNotPublished($form);
        }

        $form->status = FormStatus::Published;
        $form->published_at = now();
        $form->published_by = $actor->id;
        $form = $this->forms->save($form);

        $this->auditLogger->log($actor->id, 'form_published', 'Form', $form->id, newValues: ['status' => FormStatus::Published->value]);

        return $form;
    }

    /**
     * BR-53/76 (par analogie) : suppression définitive, réservée à un
     * Formulaire encore en brouillon (jamais publié, donc jamais
     * utilisable par une vraie Demande - BR-15) ET sans aucune Demande
     * qui le référence déjà, en filet de sécurité supplémentaire.
     * Contrairement à archive(), ceci efface réellement le Formulaire
     * et ses Champs - pour un brouillon créé par erreur (mauvaise
     * duplication, faute de frappe dans le nom...), pas pour un
     * Formulaire qui a déjà servi.
     *
     * @throws FormNotPublishedException si le Form n'existe pas
     * @throws FormNotModifiableException si le Form est publié/archivé
     *                                     ou déjà référencé par une Demande
     */
    public function delete(int $formId, User $actor): void
    {
        $form = $this->findOrFail($formId);

        if (! $form->isDraft()) {
            throw FormNotModifiableException::becausePublished($form);
        }

        if ($form->requests()->exists()) {
            throw FormNotModifiableException::becauseInUse($form);
        }

        $this->auditLogger->log($actor->id, 'form_deleted', 'Form', $form->id, ['code' => $form->code, 'name' => $form->name]);

        $form->delete();
    }

    /**
     * BR-16 : un Form archivé reste disponible pour les Requests
     * historiques, mais ne peut plus en générer de nouvelles.
     *
     * @throws FormNotPublishedException si le Form n'existe pas
     */
    public function archive(int $formId, User $actor): Form
    {
        $form = $this->findOrFail($formId);

        $form->status = FormStatus::Archived;
        $form->updated_by = $actor->id;
        $form = $this->forms->save($form);

        $this->auditLogger->log($actor->id, 'form_archived', 'Form', $form->id);

        return $form;
    }

    /**
     * BR-17 : "La duplication d'un Formulaire crée un nouveau
     * Formulaire indépendant." Copie la Catégorie/le Workflow/le nom/la
     * description, ainsi que tous les Champs actifs (et leurs Options),
     * dans un nouveau Form Draft v1 - jamais un lien vers l'original.
     *
     * @throws FormNotPublishedException si le Form source n'existe pas
     */
    public function duplicate(int $formId, User $actor): Form
    {
        $source = $this->findOrFail($formId);

        $copy = $this->copyFormWithFields($source, [
            'workflow_id' => $source->workflow_id,
            'code' => $this->nextAvailableCopyCode($source->code),
            'name' => $source->name . ' (copie)',
        ], $actor);

        $this->auditLogger->log($actor->id, 'form_duplicated', 'Form', $copy->id, newValues: ['duplicated_from' => $source->id, 'code' => $copy->code]);

        return $copy;
    }

    /**
     * Réutilise la structure (Champs + Options) d'un Formulaire existant
     * pour un AUTRE Workflow, sous un nouveau nom/code - jamais en
     * réassignant le Formulaire source lui-même. BR-11 ("chaque
     * Formulaire utilise un seul Workflow") reste donc toujours vrai :
     * ceci crée un second Formulaire, indépendant, le premier continue
     * de servir son propre Workflow sans interruption. C'est la façon
     * sûre de répondre au besoin "je veux repartir d'un Formulaire déjà
     * configuré pour ce nouveau Workflow" sans jamais pouvoir créer de
     * conflit sur "quel Workflow suit ce Formulaire" (BR-12 : c'est
     * l'inverse qui est permis - un Workflow peut être réutilisé par
     * plusieurs Formulaires - jamais un Formulaire par plusieurs
     * Workflows).
     *
     * @throws FormNotPublishedException si le Formulaire source n'existe pas
     */
    public function duplicateForWorkflow(int $sourceFormId, int $targetWorkflowId, string $name, string $code, User $actor): Form
    {
        $source = $this->findOrFail($sourceFormId);

        $copy = $this->copyFormWithFields($source, [
            'workflow_id' => $targetWorkflowId,
            'code' => $code,
            'name' => $name,
        ], $actor);

        $this->auditLogger->log(
            $actor->id, 'form_duplicated', 'Form', $copy->id,
            newValues: ['duplicated_from' => $source->id, 'code' => $copy->code, 'workflow_id' => $targetWorkflowId],
        );

        return $copy;
    }

    /**
     * @param array{workflow_id: int, code: string, name: string} $overrides
     */
    private function copyFormWithFields(Form $source, array $overrides, User $actor): Form
    {
        $copy = $this->forms->save(new Form([
            'form_category_id' => $source->form_category_id,
            'workflow_id' => $overrides['workflow_id'],
            'code' => $overrides['code'],
            'name' => $overrides['name'],
            'description' => $source->description,
            'priority' => $source->priority->value,
            'version' => 1,
            'status' => FormStatus::Draft,
            'created_by' => $actor->id,
        ]));

        foreach ($source->formFields()->with('fieldOptions')->get() as $field) {
            $newField = $copy->formFields()->create([
                'label' => $field->label,
                'section_title' => $field->section_title,
                'technical_name' => $field->technical_name,
                'field_type' => $field->field_type,
                'placeholder' => $field->placeholder,
                'default_value' => $field->default_value,
                'validation_rules' => $field->validation_rules,
                'display_order' => $field->display_order,
                'is_required' => $field->is_required,
                'is_active' => $field->is_active,
            ]);

            foreach ($field->fieldOptions as $option) {
                $newField->fieldOptions()->create([
                    'value' => $option->value,
                    'label' => $option->label,
                    'display_order' => $option->display_order,
                    'is_default' => $option->is_default,
                    'is_active' => $option->is_active,
                    'created_by' => $actor->id,
                ]);
            }
        }

        return $copy;
    }

    /**
     * @throws FormNotPublishedException
     */
    private function findOrFail(int $id): Form
    {
        $form = $this->forms->findById($id);

        if ($form === null) {
            throw FormNotPublishedException::notFound($id);
        }

        return $form;
    }

    /**
     * @throws FormNotModifiableException
     */
    private function assertModifiable(Form $form): void
    {
        if (! $form->isDraft()) {
            throw FormNotModifiableException::becausePublished($form);
        }
    }

    private function nextAvailableCopyCode(string $originalCode): string
    {
        $attempt = 1;
        do {
            $candidate = "{$originalCode}-COPY" . ($attempt > 1 ? "-{$attempt}" : '');
            $attempt++;
        } while ($this->forms->existsByCode($candidate));

        return $candidate;
    }
}
