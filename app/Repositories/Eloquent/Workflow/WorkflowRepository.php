<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Workflow;

use App\Contracts\Repositories\Workflow\WorkflowRepositoryInterface;
use App\Enums\WorkflowStatus;
use App\Models\Workflow;
use Illuminate\Database\Eloquent\Collection;

class WorkflowRepository implements WorkflowRepositoryInterface
{
    public function findById(int $id): ?Workflow
    {
        return Workflow::find($id);
    }

    public function findLatestPublishedVersion(string $code): ?Workflow
    {
        return Workflow::query()
            ->where('code', $code)
            ->where('status', WorkflowStatus::Published)
            ->orderByDesc('version')
            ->first();
    }

    public function findWithStepsAndTransitions(int $id): ?Workflow
    {
        return Workflow::query()
            ->with(['workflowSteps.outgoingTransitions.transitionConditions'])
            ->find($id);
    }

    public function findAllVersions(string $code): Collection
    {
        return Workflow::query()
            ->where('code', $code)
            ->orderByDesc('version')
            ->get();
    }
}
