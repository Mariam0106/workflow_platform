<?php

declare(strict_types=1);

namespace App\Http\Resources\Workflow;

use Illuminate\Http\Request as HttpRequest;
use Illuminate\Http\Resources\Json\JsonResource;

class ValidationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(HttpRequest $httpRequest): array
    {
        return [
            'id' => $this->id,
            'decision' => $this->decision->value,
            'comment' => $this->comment,
            'validated_at' => $this->validated_at?->toIso8601String(),
            'request' => new RequestResource($this->whenLoaded('request')),
        ];
    }
}
