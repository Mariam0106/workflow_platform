<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Workflow;

use App\Contracts\Repositories\Workflow\FormRepositoryInterface;
use App\Enums\FormStatus;
use App\Models\Form;

class FormRepository implements FormRepositoryInterface
{
    public function findById(int $id): ?Form
    {
        return Form::find($id);
    }

    public function findWithFields(int $id): ?Form
    {
        return Form::query()
            ->with(['formFields' => fn ($q) => $q->orderBy('display_order'), 'formFields.fieldOptions'])
            ->find($id);
    }

    public function findLatestPublishedVersion(string $code): ?Form
    {
        return Form::query()
            ->where('code', $code)
            ->where('status', FormStatus::Published)
            ->orderByDesc('version')
            ->first();
    }
}
