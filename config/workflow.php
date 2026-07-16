<?php

use App\Enums\WorkflowPriority;

return [

    /*
    |--------------------------------------------------------------------------
    | Request Reference
    |--------------------------------------------------------------------------
    |
    | BR-29 : Each Request receives a unique Reference Number
    | (e.g. REQ-2026-000152). Centralized here so the format can evolve
    | (prefix, separator, padding) without touching a single line of PHP
    | code elsewhere - the RequestReference Value Object (Etape 3) will
    | read these three values.
    |
    */

    'reference_prefix' => env('WORKFLOW_REFERENCE_PREFIX', 'REQ'),

    'reference_separator' => '-',

    'reference_sequence_padding' => 6,

    /*
    |--------------------------------------------------------------------------
    | Notifications & Escalation
    |--------------------------------------------------------------------------
    |
    | BR-45 : Reminder notifications are automatic.
    | BR-46 : Escalation occurs after reminder timeout
    |         (example given in the cahier des charges: 3 days).
    | BR-47 : Notification failures are logged - notification_retry_attempts
    |         bounds how many times the platform retries a failed send
    |         before giving up and only logging the failure.
    |
    */

    'reminder_after_days' => env('WORKFLOW_REMINDER_AFTER_DAYS', 3),

    'escalation_after_days' => env('WORKFLOW_ESCALATION_AFTER_DAYS', 5),

    'notification_retry_attempts' => env('WORKFLOW_NOTIFICATION_RETRY_ATTEMPTS', 3),

    /*
    |--------------------------------------------------------------------------
    | Workflow Transitions
    |--------------------------------------------------------------------------
    |
    | BR-23 : Transition priority resolves conflicts if multiple conditions
    | are true. This is the priority assigned to a new Transition when the
    | BackOffice administrator does not explicitly set one (still an
    | ordinary integer column - see App\Enums\WorkflowPriority for the
    | rationale on why priority itself stays a plain, freely adjustable
    | integer rather than a strict enum-cast column).
    |
    */

    'default_transition_priority' => WorkflowPriority::Medium->value,

    /*
    |--------------------------------------------------------------------------
    | Company Email Domains
    |--------------------------------------------------------------------------
    |
    | BR-08 : Company email is mandatory. Registration/user creation must be
    | restricted to these domains (case-insensitive). Comma-separated list,
    | e.g. "acme.com,acme-group.com". Left empty by default so the platform
    | does not block anyone before an administrator configures the real
    | company domain(s) - App\ValueObjects\CompanyEmail reads this list.
    |
    */

    'company_email_domains' => array_filter(array_map(
        'trim',
        explode(',', (string) env('WORKFLOW_COMPANY_EMAIL_DOMAINS', ''))
    )),

];
