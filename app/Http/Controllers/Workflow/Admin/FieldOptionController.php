<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workflow\Admin;

use App\DataTransferObjects\Workflow\FieldOptionData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workflow\Admin\StoreFieldOptionRequest;
use App\Models\FieldOption;
use App\Models\Form;
use App\Models\FormField;
use App\Services\Workflow\FieldOptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Toujours nested sous {form}/fields/{field}/options/... - une Option
 * n'a de sens que rattachée à son FormField (lui-même rattaché à son
 * Form), voir FieldOptionService.
 */
class FieldOptionController extends Controller
{
    public function __construct(
        private readonly FieldOptionService $fieldOptionService,
    ) {}

    public function store(StoreFieldOptionRequest $request, Form $form, FormField $field): RedirectResponse
    {
        $dto = FieldOptionData::fromArray($request->validated());

        $this->fieldOptionService->addOption($form, $field, $dto, $request->user());

        return redirect()
            ->route('workflow.admin.forms.fields.edit', [$form, $field])
            ->with('status', 'Option ajoutée.');
    }

    public function destroy(Request $request, Form $form, FormField $field, FieldOption $option): RedirectResponse
    {
        Gate::authorize('update', $form);

        $this->fieldOptionService->removeOption($form, $option);

        return redirect()
            ->route('workflow.admin.forms.fields.edit', [$form, $field])
            ->with('status', 'Option supprimée.');
    }

    public function setDefault(Request $request, Form $form, FormField $field, FieldOption $option): RedirectResponse
    {
        Gate::authorize('update', $form);

        $this->fieldOptionService->setDefault($form, $option);

        return redirect()
            ->route('workflow.admin.forms.fields.edit', [$form, $field])
            ->with('status', "Option « {$option->label} » définie par défaut.");
    }
}
