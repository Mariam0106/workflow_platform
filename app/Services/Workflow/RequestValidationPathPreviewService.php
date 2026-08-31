<?php

declare(strict_types=1);

namespace App\Services\Workflow;

use App\Exceptions\Workflow\InvalidTransitionException;
use App\Models\Request;
use App\Models\WorkflowStep;

/**
 * ==========================================================================
 * RequestValidationPathPreviewService
 * ==========================================================================
 *
 * Construit, pour une Demande donnee, la liste complete de son circuit
 * de validation : les Etapes deja franchies (avec leur decision reelle),
 * l'Etape en cours, et les Etapes a venir - ces dernieres SIMULEES en
 * reutilisant WorkflowTransitionSelector, le meme selecteur que le
 * moteur reel utilise pour faire progresser une Demande. Aucune
 * logique de condition n'est dupliquee ici : si le moteur change un
 * jour, cet apercu reste automatiquement coherent avec lui.
 *
 * Lecture seule stricte - ne modifie jamais l'etat de la Demande.
 * ==========================================================================
 */
class RequestValidationPathPreviewService
{
    public function __construct(
        private readonly WorkflowTransitionSelector $transitionSelector,
        private readonly ValidatorResolverService $validatorResolver,
    ) {
    }

    /**
     * @return array<int, array{step: WorkflowStep, status: string, validator_label: string, decided_by: ?string, decided_at: ?\Illuminate\Support\Carbon}>
     */
    public function preview(Request $request): array
    {
        $path = [];

        // --- Etapes deja franchies : historique REEL, jamais simule ---
        $histories = $request->workflowStepHistories()
            ->with('workflowStep')
            ->orderBy('entered_at')
            ->get();

        foreach ($histories as $history) {
            $step = $history->workflowStep;

            if ($step === null) {
                continue;
            }

            // L'etape courante n'est sautee ici QUE si la Demande est
            // encore activement en cours (elle sera alors affichee par
            // le bloc "en cours" juste apres, sans decision encore
            // prise). Si la Demande est deja Terminee/Rejetee, son
            // Etape finale doit au contraire apparaitre normalement
            // ici, avec sa vraie decision - sinon elle disparaissait
            // purement et simplement du circuit affiche.
            if ($step->id === $request->current_step_id && $request->status === \App\Enums\RequestStatus::Submitted) {
                continue;
            }

            $validation = $request->validations()
                ->where('workflow_step_id', $step->id)
                ->latest('validated_at')
                ->first();

            $path[] = [
                'step' => $step,
                'status' => $validation?->decision === \App\Enums\ValidationDecision::Rejected ? 'rejetee' : 'validee',
                'validator_label' => $validation?->validator?->full_name ?? $this->describeValidators($step, $request),
                'decided_by' => $validation?->validator?->full_name,
                'decided_at' => $validation?->validated_at,
                'comment' => $validation?->comment,
            ];
        }

        // --- Etape en cours ---
        if ($request->currentStep !== null && $request->status === \App\Enums\RequestStatus::Submitted) {
            $path[] = [
                'step' => $request->currentStep,
                'status' => 'en_cours',
                'validator_label' => $this->describeValidators($request->currentStep, $request),
                'decided_by' => null,
                'decided_at' => null,
                'comment' => null,
            ];
        }

        // --- Etapes a venir : SIMULEES avec le vrai selecteur ---
        $current = $request->currentStep;
        $visited = $current !== null ? [$current->id] : [];

        while ($current !== null && ! $current->is_end && $request->status === \App\Enums\RequestStatus::Submitted) {
            try {
                $transition = $this->transitionSelector->select($current, $request->requestValues);
            } catch (InvalidTransitionException) {
                break; // trou de configuration - on arrete l'apercu proprement, jamais de crash pour le demandeur
            }

            $next = $transition->toStep;

            if ($next === null || in_array($next->id, $visited, true)) {
                break; // securite anti-boucle
            }

            $visited[] = $next->id;

            $path[] = [
                'step' => $next,
                'status' => 'a_venir',
                'validator_label' => $this->describeValidators($next, $request),
                'decided_by' => null,
                'decided_at' => null,
                'comment' => null,
            ];

            $current = $next;
        }

        return $path;
    }

    /**
     * Libelle lisible du ou des validateurs potentiels d'une Etape -
     * un nom si une seule personne est concernee, sinon le nombre.
     */
    private function describeValidators(WorkflowStep $step, Request $request): string
    {
        $validators = $this->validatorResolver->resolve($step, $request);

        if ($validators->isEmpty()) {
            return 'Aucun validateur désigné';
        }

        if ($validators->count() === 1) {
            return $validators->first()->full_name;
        }

        return "{$validators->count()} validateurs possibles";
    }
}
