<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\Request;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Attachment> */
class AttachmentFactory extends Factory
{
    protected $model = Attachment::class;

    public function definition(): array
    {
        $name = fake()->word() . '.pdf';

        return [
            'request_id' => Request::factory(),
            'original_name' => $name,
            'stored_name' => fake()->uuid() . '.pdf',
            'storage_path' => 'attachments/' . fake()->uuid() . '.pdf',
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'size' => fake()->numberBetween(1000, 5_000_000),
            'uploaded_by' => User::factory(),
        ];
    }
}
