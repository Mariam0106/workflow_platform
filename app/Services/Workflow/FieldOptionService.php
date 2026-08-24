<?php

declare(strict_types=1);

namespace App\Services\Workflow;

use App\DataTransferObjects\Workflow\FieldOptionData;
use App\Exceptions\Workflow\FormNotModifiableException;
use App\Models\FieldOption;
use App\Models\Form;
use App\Models\FormField;
use App\Models\User;
use App\Services\AuditLogger;

/**
 * Options d'un FormField de type liste (select/radio/checkbox) -
 * toujours manipulées à travers le Form parent, mêmes garanties que
 * FormFieldService (Form en brouillon uniquement).
 */
class FieldOptionService
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
    ) {}

    public function addOption(Form $form, FormField $field, FieldOptionData $dto, User $actor): FieldOption
    {
        $this->assertDraft($form);

        $nextOrder = (int) ($field->fieldOptions()->max('display_order') ?? 0) + 1;

        $option = $field->fieldOptions()->create([
            'value' => $dto->value,
            'label' => $dto->label,
            'display_order' => $nextOrder,
            'is_default' => false,
            'created_by' => $actor->id,
        ]);

        if ($dto->isDefault) {
            $option->markAsDefault();
        }

        $this->auditLogger->log($actor->id, 'field_option_created', 'FieldOption', $option->id, newValues: [
            'form_field_id' => $field->id, 'label' => $option->label, 'value' => $option->value,
        ]);

        return $option;
    }

    public function removeOption(Form $form, FieldOption $option): void
    {
        $this->assertDraft($form);

        $optionId = $option->id;
        $label = $option->label;

        $option->delete();

        $this->auditLogger->log(auth()->id(), 'field_option_deleted', 'FieldOption', $optionId, ['label' => $label]);
    }

    public function setDefault(Form $form, FieldOption $option): void
    {
        $this->assertDraft($form);

        $option->markAsDefault();

        $this->auditLogger->log(auth()->id(), 'field_option_set_default', 'FieldOption', $option->id, newValues: ['label' => $option->label]);
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
}
