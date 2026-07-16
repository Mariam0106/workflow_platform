<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * ==========================================================================
 * NotificationChannel Enum
 * ==========================================================================
 *
 * BR-44 : Notifications exist as Email and In-App. Only these two.
 */
enum NotificationChannel: string
{
    case Email = 'Email';
    case InApp = 'In-App';
}
