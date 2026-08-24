<?php

namespace Database\Factories;

use App\Enums\NotificationChannel;
use App\Enums\NotificationStatus;
use App\Models\Notification;
use App\Models\Request;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Notification> */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'request_id' => Request::factory(),
            'recipient_id' => User::factory(),
            'title' => fake()->sentence(4),
            'message' => fake()->paragraph(),
            'channel' => NotificationChannel::Email,
            'status' => NotificationStatus::Pending,
            'sent_at' => null,
            'read_at' => null,
            'failure_reason' => null,
        ];
    }

    public function sent(): static
    {
        return $this->state(fn () => [
            'status' => NotificationStatus::Sent,
            'sent_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'status' => NotificationStatus::Failed,
            'failure_reason' => 'SMTP connection timeout',
        ]);
    }
}
