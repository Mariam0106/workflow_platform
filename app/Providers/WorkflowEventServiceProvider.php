<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\Workflow\RequestApproved;
use App\Events\Workflow\RequestRejected;
use App\Events\Workflow\RequestSubmitted;
use App\Events\Workflow\WorkflowFinished;
use App\Listeners\Workflow\CreateAuditLog;
use App\Listeners\Workflow\QueueEmails;
use App\Listeners\Workflow\SendNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * ==========================================================================
 * WorkflowEventServiceProvider
 * ==========================================================================
 *
 * Enregistrement EXPLICITE des Events/Listeners du domaine Workflow -
 * pas d'auto-decouverte magique, pour que n'importe qui puisse voir en
 * un coup d'oeil "quel Listener reagit a quel Event" dans un seul
 * fichier. Meme principe que WorkflowServiceProvider:
 * chaque domaine a son propre Provider dedie, zero risque de conflit
 * Git avec le futur equivalent Organisation du collègue.
 * ==========================================================================
 */
class WorkflowEventServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, array<int, array{0: class-string, 1: string}>>
     */
    protected $listen = [
        RequestSubmitted::class => [
            [SendNotification::class, 'onRequestSubmitted'],
            [CreateAuditLog::class, 'onRequestSubmitted'],
            [QueueEmails::class, 'onRequestSubmitted'],
        ],
        RequestApproved::class => [
            [SendNotification::class, 'onRequestApproved'],
            [CreateAuditLog::class, 'onRequestApproved'],
            [QueueEmails::class, 'onRequestApproved'],
        ],
        RequestRejected::class => [
            [SendNotification::class, 'onRequestRejected'],
            [CreateAuditLog::class, 'onRequestRejected'],
            [QueueEmails::class, 'onRequestRejected'],
        ],
        WorkflowFinished::class => [
            [SendNotification::class, 'onWorkflowFinished'],
            [CreateAuditLog::class, 'onWorkflowFinished'],
            [QueueEmails::class, 'onWorkflowFinished'],
        ],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
