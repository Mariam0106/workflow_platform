<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workflow;

use App\Contracts\Repositories\Workflow\FormRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\Workflow\FormResource;
use App\Models\Form;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * ==========================================================================
 * FormController
 * ==========================================================================
 *
 * Meme principe que WorkflowController : lecture seule, la
 * configuration reelle vit au BackOffice (Etape 13/14).
 * ==========================================================================
 */
class FormController extends Controller
{
    public function __construct(
        private readonly FormRepositoryInterface $formRepository,
    ) {
    }

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Form::class);

        return FormResource::collection(Form::query()->latest()->paginate(20));
    }

    public function show(Form $form): FormResource
    {
        $this->authorize('view', $form);

        return new FormResource($this->formRepository->findWithFields($form->id));
    }
}
