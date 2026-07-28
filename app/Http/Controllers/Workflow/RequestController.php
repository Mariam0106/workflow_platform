<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workflow;

use App\Contracts\Services\Workflow\WorkflowEngineInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workflow\SubmitRequestRequest;
use App\Http\Resources\Workflow\RequestResource;
use App\Models\Request;
use Illuminate\Http\JsonResponse;

/**
 * ==========================================================================
 * RequestController
 * ==========================================================================
 *
 * Volontairement fin : Form Request -> DTO -> Service (via son
 * Contract) -> Resource. Aucune regle de gestion ici - tout vit dans
 * WorkflowEngineService (Etape 8) et ses exceptions (Etape 5), qui se
 * rendent seules en reponse HTTP via DomainException::render(), sans
 * bloc try/catch necessaire dans ce Controller.
 * ==========================================================================
 */
class RequestController extends Controller
{
    public function __construct(
        private readonly WorkflowEngineInterface $engine,
    ) {
    }

    /**
     * BR-28/29 : soumet une nouvelle Request.
     */
    public function store(SubmitRequestRequest $httpRequest): JsonResponse
    {
        $request = $this->engine->submit($httpRequest->toDto());

        return (new RequestResource($request->load(['currentStep', 'requester'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request): RequestResource
    {
        $this->authorize('view', $request);

        return new RequestResource($request->load(['currentStep', 'requester']));
    }
}
