<?php

declare(strict_types=1);

namespace App\Http\Resources\Workflow;

use Illuminate\Http\Request as HttpRequest;
use Illuminate\Http\Resources\Json\JsonResource;

class FormResource extends JsonResource
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
            'fields' => $this->whenLoaded('formFields', fn () => $this->formFields->map(fn ($field) => [
                'id' => $field->id,
                'label' => $field->label,
                'technical_name' => $field->technical_name,
                'field_type' => $field->field_type,
                'is_required' => $field->is_required,
            ])),
        ];
    }
}
