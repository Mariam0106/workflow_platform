<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workflow;

use App\Contracts\Repositories\Workflow\RequestRepositoryInterface;
use App\Contracts\Services\Workflow\WorkflowEngineInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workflow\RecordValidationRequest;
use App\Models\Request as RequestModel;
use App\Models\Validation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * ==========================================================================
 * MyValidationController
 * ==========================================================================
 *
 * Interface web du rôle Validateur (cahier des charges, "USER
 * VALIDATOR : Valider ou refuser les formulaires, Accéder aux demandes
 * nécessitant son approbation, Ajouter des commentaires lors des
 * validations ou refus"). Même moteur que l'API JSON existante
 * (ValidationController) - simple redirection HTML au lieu d'une
 * réponse JSON.
 * ==========================================================================
 */
class MyValidationController extends Controller
{
    public function __construct(
        private readonly WorkflowEngineInterface $engine,
        private readonly RequestRepositoryInterface $requests,
    ) {}

    /**
     * BR-36 : uniquement les Requests actuellement en attente de CE
     * Validateur précis pour son Étape courante - triées par urgence
     * du Formulaire d'origine (Urgent d'abord), pour que le Validateur
     * sache immédiatement laquelle traiter en premier sans avoir à
     * comparer chaque ligne lui-même.
     */
    public function index(Request $request): View
    {
        $search = $request->query('q');

        $pending = $this->requests->findPendingForValidator($request->user())
            ->load(['form', 'currentStep', 'requester'])
            ->when($search, fn ($items) => $items->filter(fn ($requestItem) => str_contains(
                mb_strtolower((string) $requestItem->reference_number . ' ' . $requestItem->form?->name),
                mb_strtolower($search),
            )))
            ->sortByDesc(fn ($requestItem) => $requestItem->priority?->sortWeight() ?? 0)
            ->values();

        return view('workflow.my-validations.index', ['pendingRequests' => $pending, 'search' => $search]);
    }

    /**
     * Historique des décisions déjà prises par ce Validateur (BR-42 :
     * lecture seule, l'historique n'est jamais modifiable) - alimente
     * les KPI "Validées"/"Rejetées" du tableau de bord, filtrable par
     * décision pour que cliquer sur l'un ou l'autre affiche uniquement
     * ce sous-ensemble plutôt que tout l'historique.
     */
    public function history(Request $request): View
    {
        $decision = $request->query('decision');
        $search = $request->query('q');

        $validations = Validation::query()
            ->where('validator_id', $request->user()->id)
            ->whereNotNull('validated_at')
            ->when(
                in_array($decision, ['Approved', 'Rejected'], true),
                fn ($q) => $q->where('decision', $decision),
            )
            ->when($search, fn ($q) => $q->whereHas('request', fn ($r) => $r
                ->where('reference_number', 'like', "%{$search}%")
                ->orWhereHas('form', fn ($f) => $f->where('name', 'like', "%{$search}%"))))
            ->with(['request.form'])
            ->latest('validated_at')
            ->paginate(20)
            ->withQueryString();

        return view('workflow.my-validations.history', ['validations' => $validations, 'activeDecision' => $decision, 'search' => $search]);
    }

    public function show(RequestModel $request): View
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

        return view('workflow.my-validations.show', [
            'requestModel' => $request,
            'canDecide' => Gate::allows('create', [Validation::class, $request]),
        ]);
    }

    public function decide(RecordValidationRequest $httpRequest, RequestModel $request): RedirectResponse
    {
        Gate::authorize('create', [Validation::class, $request]);

        $this->engine->recordValidation($httpRequest->toDto($request->id));

        return redirect()
            ->route('workflow.my-validations.index')
            ->with('status', "Décision enregistrée pour la demande {$request->reference_number}.");
    }
}
