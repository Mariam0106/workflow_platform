<?php

declare(strict_types=1);

namespace App\Services\Workflow;

use App\DataTransferObjects\Workflow\FormFieldData;
use App\Exceptions\Workflow\FormNotModifiableException;
use App\Models\Form;
use App\Models\FormField;
use App\Services\AuditLogger;
use Illuminate\Support\Str;

/**
 * ==========================================================================
 * FormFieldService
 * ==========================================================================
 *
 * BR-13/14 : gère les Champs dynamiques d'un Form - toujours à travers
 * son Form parent (agrégat), jamais un FormField isolé. N'agit que sur
 * un Form en brouillon (BR-18 : un Form publié est immuable) - un
 * FormField d'un Form en brouillon n'a par construction jamais pu être
 * référencé par une Request (BR-15 : seuls les Forms publiés génèrent
 * des Requests), donc aucune règle BR-54 ("configuration référencée par
 * des données historiques ne peut être supprimée") ne s'applique ici :
 * la suppression d'un Champ de brouillon est toujours sûre.
 * ==========================================================================
 */
class FormFieldService
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
    ) {}

    public function addField(Form $form, FormFieldData $dto): FormField
    {
        $this->assertDraft($form);

        $nextOrder = (int) ($form->formFields()->max('display_order') ?? 0) + 1;

        $field = $form->formFields()->create([
            ...$dto->toArray(),
            // BR-14 : identifiant technique interne, jamais saisi par
            // l'utilisateur (voir FormFieldData::toArray()) - dérivé du
            // libellé et rendu unique au sein de ce Form en ajoutant un
            // suffixe numérique en cas de collision (ex. deux champs
            // tous les deux appelés "Nom").
            'technical_name' => $this->uniqueTechnicalName($form, $dto->label),
            'display_order' => $nextOrder,
        ]);

        $this->auditLogger->log(auth()->id(), 'form_field_created', 'FormField', $field->id, newValues: [
            'form_id' => $form->id, 'label' => $field->label, 'field_type' => $field->field_type,
        ]);

        return $field;
    }

    /**
     * Ne régénère JAMAIS le nom technique d'un Champ existant, même si
     * son libellé change (BR-14 : c'est un identifiant interne stable,
     * pas une traduction du libellé - le régénérer casserait toute
     * référence existante sans aucun bénéfice pour l'utilisateur, qui
     * ne le voit jamais).
     */
    public function updateField(Form $form, FormField $field, FormFieldData $dto): FormField
    {
        $this->assertDraft($form);

        $oldValues = ['label' => $field->label, 'field_type' => $field->field_type, 'is_required' => $field->is_required];

        $field->fill($dto->toArray());
        $field->save();

        $this->auditLogger->log(auth()->id(), 'form_field_updated', 'FormField', $field->id, $oldValues, $dto->toArray());

        return $field;
    }

    public function removeField(Form $form, FormField $field): void
    {
        $this->assertDraft($form);

        $fieldId = $field->id;
        $label = $field->label;

        // forceDelete() plutôt que delete() (SoftDeletes) : même bug que
        // WorkflowStep (voir WorkflowStepService::removeStep()) -
        // "technical_name" est unique par Formulaire au niveau base,
        // indifférent à deleted_at. Sûr uniquement parce qu'un Champ
        // n'est supprimable que tant que son Formulaire est en brouillon
        // - aucune RequestValue (restrictOnDelete) n'a jamais pu être
        // créée contre un Champ d'un Formulaire jamais publié.
        $field->forceDelete();

        $this->auditLogger->log(auth()->id(), 'form_field_deleted', 'FormField', $fieldId, ['label' => $label, 'form_id' => $form->id]);
    }

    /**
     * Échange l'ordre d'affichage de ce Champ avec celui qui le précède
     * immédiatement - pas de réordonnancement par glisser-déposer
     * (aucune dépendance JS ajoutée), deux boutons "monter"/"descendre"
     * suffisent pour un nombre de Champs par Form réaliste.
     */
    public function moveUp(Form $form, FormField $field): void
    {
        $this->assertDraft($form);
        $this->swapWithNeighbour($form, $field, direction: -1);
    }

    public function moveDown(Form $form, FormField $field): void
    {
        $this->assertDraft($form);
        $this->swapWithNeighbour($form, $field, direction: 1);
    }

    private function swapWithNeighbour(Form $form, FormField $field, int $direction): void
    {
        $neighbour = $form->formFields()
            ->where('display_order', $direction < 0 ? '<' : '>', $field->display_order)
            ->orderBy('display_order', $direction < 0 ? 'desc' : 'asc')
            ->first();

        if ($neighbour === null) {
            return;
        }

        [$fieldOrder, $neighbourOrder] = [$field->display_order, $neighbour->display_order];

        $field->update(['display_order' => $neighbourOrder]);
        $neighbour->update(['display_order' => $fieldOrder]);
    }

    /**
     * @throws FormNotModifiableException
     */
    private function assertDraft(Form $form): void
    {
        if (! $form->isDraft()) {
            throw FormNotModifiableException::becausePublished($form);
        }
    }

    /**
     * BR-14 : dérive un identifiant technique lisible à partir du
     * libellé (ex. "Montant demandé" -> "montant_demande"), rendu
     * unique au sein de ce Form en ajoutant "_2", "_3"... en cas de
     * collision. L'utilisateur ne voit ni ne saisit jamais cette
     * valeur.
     */
    private function uniqueTechnicalName(Form $form, string $label): string
    {
        $base = Str::slug($label, '_') ?: 'champ';
        $candidate = $base;
        $suffix = 2;

        while ($form->formFields()->where('technical_name', $candidate)->exists()) {
            $candidate = "{$base}_{$suffix}";
            $suffix++;
        }

        return $candidate;
    }
}
