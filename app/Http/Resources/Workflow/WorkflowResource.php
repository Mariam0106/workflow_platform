<?php

declare(strict_types=1);

namespace App\Http\Resources\Workflow;

use Illuminate\Http\Request as HttpRequest;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkflowResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(HttpRequest $httpRequest): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'version' => $this->version,
            'status' => $this->status->value,
            'steps' => $this->whenLoaded('workflowSteps', fn () => $this->workflowSteps->map(fn ($step) => [
                'id' => $step->id,
                'code' => $step->code,
                'name' => $step->name,
                'step_order' => $step->step_order,
            ])),
        ];
    }
}
