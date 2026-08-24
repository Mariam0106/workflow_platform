<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workflow\Admin;

use App\DataTransferObjects\Workflow\CreateFormData;
use App\DataTransferObjects\Workflow\UpdateFormData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workflow\Admin\StoreFormFromExistingRequest;
use App\Http\Requests\Workflow\Admin\StoreFormRequest;
use App\Http\Requests\Workflow\Admin\UpdateFormRequest;
use App\Models\Form;
use App\Models\FormCategory;
use App\Models\Workflow;
use App\Services\Workflow\FormService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class FormController extends Controller
{
    public function __construct(
        private readonly FormService $formService,
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Form::class);

        $search = $request->query('q');
        $workflowId = $request->query('workflow_id');

        $forms = Form::query()
            ->with(['formCategory', 'workflow'])
            ->withCount('formFields')
            ->when($search, fn ($q) => $q->where(fn ($inner) => $inner
                ->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")))
            ->when($workflowId, fn ($q) => $q->where('workflow_id', $workflowId))
            ->orderByDesc('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('workflow.forms.index', [
            'forms' => $forms,
            'search' => $search,
            'filteredWorkflow' => $workflowId ? Workflow::find($workflowId) : null,
        ]);
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', Form::class);

        $options = $this->formOptions();

        // Raccourci "Créer un formulaire pour ce workflow" depuis
        // l'écran du Workflow Designer : le Workflow visé est presque
        // toujours encore en BROUILLON à ce stade précis (c'est
        // justement pour ça qu'aucun Formulaire ne l'utilisait encore)
        // - or $this->formOptions() ne liste QUE les Workflows publiés
        // (BR-30). Sans ce correctif, le <select> n'aurait jamais
        // contenu le Workflow pré-sélectionné : l'Administrateur
        // choisissait alors, sans s'en rendre compte, un AUTRE Workflow
        // (déjà publié) resté sélectionné par défaut - le Formulaire se
        // retrouvait rattaché au mauvais Workflow, et "Configurer les
        // conditions" pointait ensuite vers ce Workflow publié, dont
        // l'édition est refusée (BR-26) → 403 déroutant sans rapport
        // apparent avec la cause réelle.
        $preselectedWorkflowId = $request->query('workflow');

        if ($preselectedWorkflowId && ! $options['workflows']->contains('id', (int) $preselectedWorkflowId)) {
            $preselectedWorkflow = Workflow::query()->active()->find($preselectedWorkflowId);

            if ($preselectedWorkflow !== null) {
                $options['workflows']->push($preselectedWorkflow);
            }
        }

        return view('workflow.forms.create', [
            ...$options,
            'preselectedWorkflowId' => $preselectedWorkflowId,
        ]);
    }

    public function store(StoreFormRequest $request): RedirectResponse
    {
        $dto = CreateFormData::fromArray($request->validated());

        $form = $this->formService->createDraft($dto, $request->user());

        return redirect()
            ->route('workflow.admin.forms.edit', $form)
            ->with('status', "Formulaire « {$form->name} » créé en brouillon - ajoutez-lui des champs avant de le publier.");
    }

    public function edit(Form $form): View
    {
        Gate::authorize('view', $form);

        $form->load(['formFields' => fn ($q) => $q->orderBy('display_order'), 'formFields.fieldOptions', 'workflow.workflowSteps']);

        return view('workflow.forms.edit', [...$this->formOptions($form), 'form' => $form]);
    }

    public function update(UpdateFormRequest $request, Form $form): RedirectResponse
    {
        $dto = UpdateFormData::fromArray([...$request->validated(), 'id' => $form->id]);

        $this->formService->updateDraft($dto, $request->user());

        return redirect()
            ->route('workflow.admin.forms.edit', $form)
            ->with('status', "Formulaire « {$form->name} » mis à jour.");
    }

    public function publish(Request $request, Form $form): RedirectResponse
    {
        Gate::authorize('publish', $form);

        $this->formService->publish($form->id, $request->user());

        return redirect()
            ->route('workflow.admin.forms.index')
            ->with('status', "Formulaire « {$form->name} » publié - il peut désormais être utilisé pour créer des demandes.");
    }

    public function archive(Request $request, Form $form): RedirectResponse
    {
        Gate::authorize('archive', $form);

        $this->formService->archive($form->id, $request->user());

        return back()->with('status', "Formulaire « {$form->name} » archivé.");
    }

    public function duplicate(Request $request, Form $form): RedirectResponse
    {
        Gate::authorize('create', Form::class);

        $copy = $this->formService->duplicate($form->id, $request->user());

        return redirect()
            ->route('workflow.admin.forms.edit', $copy)
            ->with('status', "« {$form->name} » dupliqué en un nouveau formulaire indépendant.");
    }

    public function destroy(Request $request, Form $form): RedirectResponse
    {
        Gate::authorize('delete', $form);

        $name = $form->name;

        $this->formService->delete($form->id, $request->user());

        return redirect()
            ->route('workflow.admin.forms.index')
            ->with('status', "Formulaire « {$name} » supprimé.");
    }

    /**
     * BR-11/12 : reprend la structure (Champs + Options) d'un Formulaire
     * existant pour CE Workflow-ci, sous un nouveau nom/code - voir
     * FormService::duplicateForWorkflow() pour le détail de pourquoi
     * ceci crée toujours un second Formulaire indépendant plutôt que de
     * réassigner le Formulaire source.
     */
    public function storeFromExisting(StoreFormFromExistingRequest $request, Workflow $workflow): RedirectResponse
    {
        $copy = $this->formService->duplicateForWorkflow(
            sourceFormId: (int) $request->validated('source_form_id'),
            targetWorkflowId: $workflow->id,
            name: $request->validated('name'),
            code: $request->validated('code'),
            actor: $request->user(),
        );

        return redirect()
            ->route('workflow.admin.forms.edit', $copy)
            ->with('status', "Formulaire « {$copy->name} » créé à partir d'un formulaire existant, pour ce workflow.");
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(?Form $current = null): array
    {
        $formCategories = FormCategory::query()->active()->orderBy('name')->get();
        // BR-30/31 : "Seuls les Workflows publiés peuvent être associés
        // à des Formulaires publiés." - restreint dès la création, pas
        // seulement vérifié à la publication du Form (qui aurait laissé
        // l'Administrateur construire tout un Formulaire autour d'un
        // Workflow en brouillon avant de découvrir le problème
        // seulement au moment de publier - trop tard, contre-intuitif).
        $workflows = Workflow::query()->published()->active()->orderBy('name')->get();

        // Filet de sécurité : si la Catégorie/le Workflow actuellement
        // rattaché au Form a été archivé depuis, on l'ajoute quand même
        // à la liste (sinon le <select> de l'écran d'édition perdrait
        // silencieusement la sélection en cours).
        if ($current !== null) {
            if ($current->formCategory !== null && ! $formCategories->contains('id', $current->formCategory->id)) {
                $formCategories->push($current->formCategory);
            }
            if ($current->workflow !== null && ! $workflows->contains('id', $current->workflow->id)) {
                $workflows->push($current->workflow);
            }
        }

        return [
            'formCategories' => $formCategories,
            'workflows' => $workflows,
        ];
    }
}