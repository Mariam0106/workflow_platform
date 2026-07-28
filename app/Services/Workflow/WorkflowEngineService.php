<?php

declare(strict_types=1);

namespace App\Services\Workflow;

use App\Contracts\Repositories\Workflow\FormRepositoryInterface;
use App\Contracts\Repositories\Workflow\RequestRepositoryInterface;
use App\Contracts\Repositories\Workflow\WorkflowRepositoryInterface;
use App\Contracts\Services\Workflow\WorkflowEngineInterface;
use App\DataTransferObjects\Workflow\RecordValidationData;
use App\DataTransferObjects\Workflow\SubmitRequestData;
use App\Enums\RequestStatus;
use App\Enums\ValidationDecision;
use App\Events\Workflow\RequestApproved;
use App\Events\Workflow\RequestRejected;
use App\Events\Workflow\RequestSubmitted;
use App\Events\Workflow\WorkflowFinished;
use App\Exceptions\Workflow\FormNotPublishedException;
use App\Exceptions\Workflow\InvalidRequestStatusException;
use App\Exceptions\Workflow\ValidationNotAllowedException;
use App\Exceptions\Workflow\WorkflowNotFoundException;
use App\Exceptions\Workflow\InvalidTransitionException;
use App\Models\Request;
use App\Models\RequestValue;
use App\Models\User;
use App\Models\Validation;
use App\Models\WorkflowStepHistory;
use App\ValueObjects\RequestReference;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

/**
 * ==========================================================================
 * WorkflowEngineService (le coeur de la plateforme)
 * ==========================================================================
 *
 * Seule classe que les Controllers (Etape 12) appelleront pour soumettre
 * une Request ou enregistrer une Validation. Toute la logique metier
 * (BR-21 a BR-42) vit ici et dans les 3 briques qu'elle orchestre
 * (TransitionConditionEvaluator, ValidatorResolverService,
 * WorkflowTransitionSelector) - jamais dans un Controller.
 * ==========================================================================
 */
class WorkflowEngineService implements WorkflowEngineInterface
{
    public function __construct(
        private readonly WorkflowRepositoryInterface $workflowRepository,
        private readonly FormRepositoryInterface $formRepository,
        private readonly RequestRepositoryInterface $requestRepository,
        private readonly ValidatorResolverService $validatorResolver,
        private readonly WorkflowTransitionSelector $transitionSelector,
    ) {
    }

    /**
     * BR-28 : cree une Request a partir d'un Form publie.
     * BR-25 : resout la DERNIERE VERSION PUBLIEE du Workflow associe au
     *         Form (pas forcement celle que le Form pointe directement -
     *         un Form peut avoir ete configure avec une version qui,
     *         depuis, a ete remplacee par une version plus recente).
     * BR-29 : genere une reference unique.
     * BR-34/35 : fige la version de Workflow utilisee sur la Request.
     *
     * @throws FormNotPublishedException si le Form n'existe pas ou n'est pas publie.
     * @throws WorkflowNotFoundException si aucune version publiee du Workflow
     *                           n'existe, ou si le Workflow n'a aucune
     *                           Step de depart configuree.
     */
    public function submit(SubmitRequestData $data): Request
    {
        $request = DB::transaction(function () use ($data) {
            $form = $this->formRepository->findWithFields($data->formId);

            if ($form === null) {
                throw FormNotPublishedException::notFound($data->formId);
            }

            if (! $form->isPublished()) {
                throw FormNotPublishedException::notPublished($form);
            }

            if ($form->workflow === null) {
                throw WorkflowNotFoundException::workflowOfFormUnavailable($form);
            }

            $workflow = $this->workflowRepository->findLatestPublishedVersion($form->workflow->code);

            if ($workflow === null) {
                throw WorkflowNotFoundException::noPublishedVersion($form->workflow->code);
            }

            $startStep = $workflow->workflowSteps()->where('is_start', true)->first();

            if ($startStep === null) {
                throw InvalidTransitionException::workflowHasNoSteps($workflow);
            }

            $year = (int) now()->format('Y');
            $reference = RequestReference::generate(
                $this->requestRepository->nextSequenceNumber($year),
                $year,
            );

            $request = Request::create([
                'form_id' => $form->id,
                'workflow_id' => $workflow->id,
                'requester_id' => $data->requesterId,
                'current_step_id' => $startStep->id,
                'reference_number' => $reference->value,
                'workflow_version' => $workflow->version,
                'status' => RequestStatus::Submitted,
                'submitted_at' => now(),
            ]);

            foreach ($data->values as $formFieldId => $value) {
                RequestValue::create([
                    'request_id' => $request->id,
                    'form_field_id' => $formFieldId,
                    'value' => $value,
                ]);
            }

            WorkflowStepHistory::create([
                'request_id' => $request->id,
                'workflow_step_id' => $startStep->id,
                'workflow_transition_id' => null,
                'triggered_by' => $data->requesterId,
                'entered_at' => now(),
            ]);

            return $request->fresh(['requestValues', 'currentStep']);
        });

        // Evenement leve APRES le commit de la transaction (jamais dedans) :
        // un Listener ne doit jamais reagir a des donnees susceptibles
        // d'etre annulees par un rollback.
        Event::dispatch(new RequestSubmitted($request));

        return $request;
    }

    /**
     * BR-36 : seul le Validateur assigne peut valider.
     * BR-37 : chaque Validation est timestampee.
     * BR-38 : Approve ou Reject uniquement (garanti par l'Enum
     *         ValidationDecision, Etape 1).
     * BR-39 : Reject termine immediatement le Workflow.
     * BR-40 : commentaire de rejet obligatoire (deja garanti par le DTO
     *         RecordValidationData, Etape 6 - ValidationNotAllowedException y est
     *         deja levee avant meme d'atteindre cette methode).
     * BR-41 : Approve execute automatiquement la Transition eligible.
     *
     * @throws InvalidRequestStatusException si la Request n'est plus modifiable
     *                              (BR-31/32 - deja terminee).
     * @throws ValidationNotAllowedException si l'utilisateur n'est pas le
     *                              Validateur attendu (BR-36).
     */
    public function recordValidation(RecordValidationData $data): Validation
    {
        $wasRejected = false;
        $justFinished = false;

        $validation = DB::transaction(function () use ($data, &$wasRejected, &$justFinished) {
            $request = $this->requestRepository->findById($data->requestId);

            if ($request === null || $request->status !== RequestStatus::Submitted) {
                throw InvalidRequestStatusException::notEditable($request ?? new Request(['status' => RequestStatus::Draft]));
            }

            $step = $request->currentStep;

            if ($step === null) {
                throw WorkflowNotFoundException::currentStepUnavailable($request);
            }

            $validator = User::findOrFail($data->validatorId);

            if (! $this->validatorResolver->isAuthorized($validator, $step, $request)) {
                throw ValidationNotAllowedException::unauthorizedValidator($validator, $step);
            }

            $validation = Validation::create([
                'request_id' => $request->id,
                'workflow_step_id' => $step->id,
                'validator_id' => $validator->id,
                'decision' => $data->decision,
                'comment' => $data->comment,
                'validated_at' => now(),
            ]);

            $this->closeCurrentStepHistory($request);

            if ($data->decision === ValidationDecision::Rejected) {
                $request->update([
                    'status' => RequestStatus::Rejected,
                    'completed_at' => now(),
                ]);

                $wasRejected = true;

                return $validation;
            }

            $justFinished = $this->advanceToNextStep($request, $step, $validator);

            return $validation;
        });

        // Evenements leves APRES le commit (jamais dans la transaction).
        $request = $validation->request()->firstOrFail();

        if ($wasRejected) {
            Event::dispatch(new RequestRejected($request, $validation));
        } else {
            Event::dispatch(new RequestApproved($request, $validation));

            if ($justFinished) {
                Event::dispatch(new WorkflowFinished($request));
            }
        }

        return $validation;
    }

    /**
     * BR-21/22/23 : selectionne et execute la Transition eligible,
     * avance la Request, et la marque Completed si le nouveau Step est
     * une Step de fin.
     *
     * @return bool true si la Request vient d'atteindre son Step de fin
     *              (le Workflow est acheve).
     */
    private function advanceToNextStep(Request $request, \App\Models\WorkflowStep $step, User $triggeredBy): bool
    {
        $transition = $this->transitionSelector->select($step, $request->requestValues);
        $nextStep = $transition->toStep;

        if ($nextStep === null) {
            throw WorkflowNotFoundException::transitionTargetUnavailable($transition);
        }

        $request->update(['current_step_id' => $nextStep->id]);

        WorkflowStepHistory::create([
            'request_id' => $request->id,
            'workflow_step_id' => $nextStep->id,
            'workflow_transition_id' => $transition->id,
            'triggered_by' => $triggeredBy->id,
            'entered_at' => now(),
        ]);

        if ($nextStep->is_end) {
            $request->update([
                'status' => RequestStatus::Completed,
                'completed_at' => now(),
            ]);

            return true;
        }

        return false;
    }

    private function closeCurrentStepHistory(Request $request): void
    {
        $request->workflowStepHistories()
            ->whereNull('left_at')
            ->latest('entered_at')
            ->first()
            ?->update(['left_at' => now()]);
    }
}
