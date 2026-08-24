<?php

declare(strict_types=1);

namespace App\Http\Requests\Workflow\Admin;

use App\Models\Form;
use Illuminate\Foundation\Http\FormRequest;

class StoreFieldOptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Form $form */
        $form = $this->route('form');

        return $this->user()->can('update', $form);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'value' => ['required', 'string', 'max:255'],
            'label' => ['required', 'string', 'max:255'],
            'is_default' => ['boolean'],
        ];
    }
}
