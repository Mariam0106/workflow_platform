<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workflow\Admin;

use App\DataTransferObjects\Workflow\FieldOptionData;
use App\DataTransferObjects\Workflow\FormFieldData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workflow\Admin\StoreFormFieldRequest;
use App\Http\Requests\Workflow\Admin\UpdateFormFieldRequest;
use App\Models\FieldOption;
use App\Models\Form;
use App\Models\FormField;
use App\Services\Workflow\FieldOptionService;
use App\Services\Workflow\FormFieldService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Toujours nested sous un Form ({form}/fields/...) - un FormField n'a
 * aucun sens hors de son Form parent (agrégat, voir FormFieldService).
 */
class FormFieldController extends Controller
{
    public function __construct(
        private readonly FormFieldService $formFieldService,
        private readonly FieldOptionService $fieldOptionService,
    ) {}

    public function create(Form $form): View
    {
        Gate::authorize('update', $form);

        return view('workflow.forms.fields.create', [
            'form' => $form,
            'fieldTypes' => StoreFormFieldRequest::FIELD_TYPES,
            'existingSectionTitles' => $this->existingSectionTitles($form),
        ]);
    }

    public function store(StoreFormFieldRequest $request, Form $form): RedirectResponse
    {
        $dto = FormFieldData::fromArray($request->validated());

        $field = $this->formFieldService->addField($form, $dto);

        // Options saisies sur ce même écran (voir
        // StoreFormFieldRequest::rules()) - uniquement pour un Champ de
        // type "select" ; ignoré silencieusement sinon plutôt que
        // rejeté, au cas où le navigateur aurait malgré tout soumis des
        // champs "options" masqués par le JS de l'écran.
        if ($dto->fieldType === 'select') {
            $labels = array_values(array_filter(
                $request->input('options', []),
                fn ($label) => is_string($label) && trim($label) !== '',
            ));

            foreach ($labels as $label) {
                $this->fieldOptionService->addOption($form, $field, FieldOptionData::fromArray([
                    'value' => trim($label),
                    'label' => trim($label),
                    'is_default' => false,
                ]), $request->user());
            }

            // "Autre" par défaut (voir FieldOption::FREE_TEXT_VALUE) :
            // demandé explicitement comme choix par défaut plutôt que
            // la première option "normale" - un Utilisateur qui ne
            // remplit pas ce champ n'est ainsi jamais bloqué par une
            // valeur prédéfinie qui ne lui correspond pas.
            if ($request->boolean('include_other_option', true)) {
                $this->fieldOptionService->addOption($form, $field, FieldOptionData::fromArray([
                    'value' => FieldOption::FREE_TEXT_VALUE,
                    'label' => 'Autre (préciser)',
                    'is_default' => true,
                ]), $request->user());
            } elseif (! empty($labels)) {
                // Pas de "Autre" : la première option saisie devient
                // le choix par défaut, pour qu'un Champ obligatoire
                // n'affiche jamais un menu vide au premier chargement.
                $field->fieldOptions()->oldest('display_order')->first()?->markAsDefault();
            }
        }

        return redirect()
            ->route('workflow.admin.forms.edit', $form)
            ->with('status', 'Champ ajouté.');
    }

    public function edit(Form $form, FormField $field): View
    {
        Gate::authorize('update', $form);

        $field->load('fieldOptions');

        return view('workflow.forms.fields.edit', [
            'form' => $form,
            'field' => $field,
            'fieldTypes' => StoreFormFieldRequest::FIELD_TYPES,
            'existingSectionTitles' => $this->existingSectionTitles($form),
        ]);
    }

    public function update(UpdateFormFieldRequest $request, Form $form, FormField $field): RedirectResponse
    {
        $dto = FormFieldData::fromArray([...$request->validated(), 'id' => $field->id]);

        $this->formFieldService->updateField($form, $field, $dto);

        return redirect()
            ->route('workflow.admin.forms.edit', $form)
            ->with('status', 'Champ mis à jour.');
    }

    public function destroy(Request $request, Form $form, FormField $field): RedirectResponse
    {
        Gate::authorize('update', $form);

        $this->formFieldService->removeField($form, $field);

        return redirect()
            ->route('workflow.admin.forms.edit', $form)
            ->with('status', "Champ « {$field->label} » supprimé.");
    }

    public function moveUp(Request $request, Form $form, FormField $field): RedirectResponse
    {
        Gate::authorize('update', $form);

        $this->formFieldService->moveUp($form, $field);

        return back()->with('status', 'Ordre des champs mis à jour.');
    }

    public function moveDown(Request $request, Form $form, FormField $field): RedirectResponse
    {
        Gate::authorize('update', $form);

        $this->formFieldService->moveDown($form, $field);

        return back()->with('status', 'Ordre des champs mis à jour.');
    }

    /**
     * Titres de section déjà utilisés sur ce Formulaire - proposés en
     * autocomplétion pour que l'Administrateur réutilise exactement le
     * même intitulé plutôt que de créer une section en double par une
     * faute de frappe (ex. "Pièces jointes" vs "Pieces Jointe").
     *
     * @return array<int, string>
     */
    private function existingSectionTitles(Form $form): array
    {
        return $form->formFields()
            ->whereNotNull('section_title')
            ->orderBy('display_order')
            ->pluck('section_title')
            ->unique()
            ->values()
            ->all();
    }
}
