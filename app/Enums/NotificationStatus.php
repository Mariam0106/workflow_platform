<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * ==========================================================================
 * NotificationStatus Enum
 * ==========================================================================
 *
 * Delivery lifecycle of a Notification.
 * BR-47 : notification failures are logged (see Failed + failure_reason).
 */
enum NotificationStatus: string
{
    case Pending = 'Pending';
    case Sent = 'Sent';
    case Failed = 'Failed';
    case Read = 'Read';
}
