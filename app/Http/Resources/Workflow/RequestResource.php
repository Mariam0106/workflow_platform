<?php

declare(strict_types=1);

namespace App\Http\Resources\Workflow;

use Illuminate\Http\Request as HttpRequest;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(HttpRequest $httpRequest): array
    {
        return [
            'id' => $this->id,
            'reference_number' => (string) $this->reference_number,
            'status' => $this->status->value,
            'workflow_version' => $this->workflow_version,
            'current_step' => $this->whenLoaded('currentStep', fn () => [
                'id' => $this->currentStep->id,
                'name' => $this->currentStep->name,
            ]),
            'requester' => $this->whenLoaded('requester', fn () => [
                'id' => $this->requester->id,
                'full_name' => $this->requester->full_name,
            ]),
            'submitted_at' => $this->submitted_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
        ];
    }
}
