<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workflow;

use App\Contracts\Services\Workflow\WorkflowEngineInterface;
use App\DataTransferObjects\Workflow\SaveDraftData;
use App\Enums\RequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workflow\SaveRequestDraftRequest;
use App\Http\Requests\Workflow\SubmitRequestRequest;
use App\Models\Form;
use App\Models\FormCategory;
use App\Models\Request as RequestModel;
use App\Support\AttachmentUploader;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * ==========================================================================
 * MyRequestController
 * ==========================================================================
 *
 * Interface web du rôle User (cahier des charges, "L'utilisateur
 * standard pourra : Initier un formulaire, Soumettre une demande à
 * validation, Consulter ses formulaires, Suivre l'avancement de ses
 * demandes"). Enveloppe fine autour de WorkflowEngineInterface (déjà
 * utilisé par l'API JSON existante, RequestController) - même moteur,
 * simplement une redirection HTML au lieu d'une réponse JSON.
 * ==========================================================================
 */
class MyRequestController extends Controller
{
    public function __construct(
        private readonly WorkflowEngineInterface $engine,
    ) {}

    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = $request->query('q');
        $categoryId = $request->query('category');

        $requests = RequestModel::query()
            ->where('requester_id', $request->user()->id)
            ->when(
                $status && in_array($status, array_column(RequestStatus::cases(), 'value'), true),
                fn ($q) => $q->where('status', $status),
            )
            ->when($categoryId, fn ($q) => $q->whereHas('form', fn ($f) => $f->where('form_category_id', $categoryId)))
            ->when($search, fn ($q) => $q->where(fn ($inner) => $inner
                ->where('reference_number', 'like', "%{$search}%")
                ->orWhereHas('form', fn ($f) => $f->where('name', 'like', "%{$search}%"))))
            ->with(['form.formCategory', 'currentStep'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('workflow.my-requests.index', [
            'requests' => $requests,
            'activeStatus' => $status,
            'search' => $search,
            'activeCategoryId' => $categoryId,
            'categories' => FormCategory::query()->active()->orderBy('name')->get(),
        ]);
    }

    /**
     * BR-15 : seuls les Formulaires publiés peuvent être utilisés pour
     * créer une nouvelle Demande.
     */
    public function selectForm(Request $request): View
    {
        $categoryId = $request->query('category');

        if ($categoryId === null) {
            $categories = FormCategory::query()
                ->whereHas('forms', fn ($q) => $q->published())
                ->withCount(['forms' => fn ($q) => $q->published()])
                ->orderBy('name')
                ->get();

            return view('workflow.my-requests.select-form', [
                'categories' => $categories,
                'category' => null,
                'forms' => null,
                'search' => null,
            ]);
        }

        $category = FormCategory::query()->findOrFail($categoryId);
        $search = $request->query('q');

        $forms = Form::query()
            ->published()
            ->where('form_category_id', $category->id)
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->get();

        return view('workflow.my-requests.select-form', [
            'categories' => null,
            'category' => $category,
            'forms' => $forms,
            'search' => $search,
        ]);
    }

    public function create(Request $request, Form $form): View
    {
        if (! $form->isPublished()) {
            abort(404);
        }

        $form->load(['formFields' => fn ($q) => $q->orderBy('display_order'), 'formFields.fieldOptions']);

        // Reprend le Brouillon existant de CET Utilisateur pour CE
        // Formulaire, s'il y en a un - au plus un par couple (voir
        // WorkflowEngineService::saveDraft()).
        $draft = RequestModel::query()
            ->where('requester_id', $request->user()->id)
            ->where('form_id', $form->id)
            ->where('status', RequestStatus::Draft)
            ->with(['requestValues', 'attachments.uploader'])
            ->first();

        // Les Pièces Jointes (BR-51) sont rattachées à une Request, donc
        // il en faut une dès l'ouverture de cet écran pour pouvoir en
        // téléverser une immédiatement, sans attendre la première
        // sauvegarde automatique (20s, voir le script de l'écran) - un
        // Brouillon vide (aucune valeur) est créé au besoin, exactement
        // comme le fait déjà l'auto-save.
        if ($draft === null) {
            $draft = $this->engine->saveDraft(new SaveDraftData(
                formId: $form->id,
                requesterId: $request->user()->id,
                values: [],
            ));
            $draft->load(['requestValues', 'attachments.uploader']);
        }

        $draftValues = $draft->requestValues->pluck('value', 'form_field_id');

        return view('workflow.my-requests.create', [
            'form' => $form,
            'draft' => $draft,
            'draftValues' => $draftValues,
        ]);
    }

    /**
     * Appelé périodiquement en arrière-plan (fetch) depuis l'écran de
     * saisie - jamais depuis une navigation complète, d'où la réponse
     * JSON plutôt qu'une redirection.
     */
    public function saveDraft(SaveRequestDraftRequest $httpRequest, Form $form): JsonResponse
    {
        $draft = $this->engine->saveDraft($httpRequest->toDto($form));

        return response()->json([
            'saved' => true,
            'savedAt' => $draft->updated_at->format('H:i'),
        ]);
    }

    public function store(SubmitRequestRequest $httpRequest, Form $form): RedirectResponse
    {
        $createdRequest = $this->engine->submit($httpRequest->toDto());

        // Champs de formulaire dynamique de type "file" (voir
        // SubmitRequestRequest::prepareForValidation()) - stockés comme
        // Pièce Jointe seulement maintenant que $createdRequest existe.
        foreach ($httpRequest->fieldFiles() as $file) {
            AttachmentUploader::store($file, $createdRequest, $httpRequest->user()->id);
        }

        // engine->submit() crée toujours une Request neuve, distincte de
        // la ligne "Draft" (voir WorkflowEngineService::submit()) - les
        // Pièces Jointes (BR-51) déjà téléversées sur ce Brouillon
        // doivent donc être rattachées à cette nouvelle Request AVANT
        // de supprimer le Brouillon, sinon la contrainte
        // cascadeOnDelete() de la table attachments les supprimerait
        // silencieusement avec lui.
        $draft = RequestModel::query()
            ->where('requester_id', $httpRequest->user()->id)
            ->where('form_id', $form->id)
            ->where('status', RequestStatus::Draft)
            ->first();

        if ($draft !== null) {
            $draft->attachments()->update(['request_id' => $createdRequest->id]);
            $draft->delete();
        }

        return redirect()
            ->route('workflow.my-requests.show', $createdRequest)
            ->with('status', "Demande {$createdRequest->reference_number} envoyée.");
    }

    public function show(RequestModel $request, \App\Services\Workflow\RequestValidationPathPreviewService $pathPreview): View
    {
        Gate::authorize('view', $request);

        $request->load([
            'form.formCategory',
            'currentStep',
            'requester',
            'requestValues.formField',
            'attachments.uploader',
            'validations.validator',
            'validations' => fn ($q) => $q->orderBy('validated_at'),
        ]);

        return view('workflow.my-requests.show', [
            'requestModel' => $request,
            'validationPath' => $pathPreview->preview($request),
        ]);
    }

    /**
     * Supprime un Brouillon avant tout envoi - RequestPolicy::delete()
     * garantit déjà que ce n'est possible que pour SON PROPRE Brouillon
     * (BR-53 : seules les Demandes en brouillon peuvent être
     * supprimées par leur Demandeur). Les Pièces Jointes déjà
     * téléversées sont supprimées avec (fichiers physiques inclus,
     * pas seulement la ligne en base).
     */
    public function destroy(RequestModel $request): RedirectResponse
    {
        Gate::authorize('delete', $request);

        foreach ($request->attachments as $attachment) {
            AttachmentUploader::delete($attachment);
        }

        $request->delete();

        return redirect()
            ->route('workflow.my-requests.select-form')
            ->with('status', 'Brouillon supprimé.');
    }
}
