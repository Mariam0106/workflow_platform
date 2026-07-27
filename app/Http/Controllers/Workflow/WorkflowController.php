<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workflow;

use App\Contracts\Repositories\Workflow\WorkflowRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\Workflow\WorkflowResource;
use App\Models\Workflow;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * ==========================================================================
 * WorkflowController
 * ==========================================================================
 *
 * Lecture seule pour l'instant : la creation/edition de Workflows est
 * une action de configuration reservee au BackOffice (Etape 13/14 du
 * roadmap partage - "Dynamic Form Builder"/"BackOffice"), pas une
 * simple route CRUD ici.
 * ==========================================================================
 */
class WorkflowController extends Controller
{
    public function __construct(
        private readonly WorkflowRepositoryInterface $workflowRepository,
    ) {
    }

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Workflow::class);

        return WorkflowResource::collection(Workflow::query()->latest()->paginate(20));
    }

    public function show(Workflow $workflow): WorkflowResource
    {
        $this->authorize('view', $workflow);

        return new WorkflowResource($this->workflowRepository->findWithStepsAndTransitions($workflow->id));
    }
}
