<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workflow;

use App\Contracts\Services\Workflow\WorkflowEngineInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workflow\RecordValidationRequest;
use App\Http\Resources\Workflow\ValidationResource;
use App\Models\Request;
use App\Models\Validation;

/**
 * ==========================================================================
 * ValidationController
 * ==========================================================================
 */
class ValidationController extends Controller
{
    public function __construct(
        private readonly WorkflowEngineInterface $engine,
    ) {
    }

    /**
     * BR-36/38/39/41 : enregistre une decision de validation.
     */
    public function store(RecordValidationRequest $httpRequest, Request $request): ValidationResource
    {
        $this->authorize('create', [Validation::class, $request]);

        $validation = $this->engine->recordValidation($httpRequest->toDto($request->id));

        return new ValidationResource($validation->load('request.currentStep'));
    }
}
