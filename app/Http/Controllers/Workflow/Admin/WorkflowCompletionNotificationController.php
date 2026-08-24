<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workflow\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessFunction;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowCompletionNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * ==========================================================================
 * WorkflowCompletionNotificationController
 * ==========================================================================
 *
 * Qui prévenir, en plus du Demandeur, quand une Demande de ce Workflow
 * est entièrement approuvée - voir la migration
 * create_workflow_completion_notifications_table pour le contexte
 * métier complet (ex. Crédit Client doit savoir qu'un compte est prêt
 * à ouvrir, même s'il n'est Validateur d'aucune Étape).
 * ==========================================================================
 */
class WorkflowCompletionNotificationController extends Controller
{
    public function store(Request $httpRequest, Workflow $workflow): RedirectResponse
    {
        Gate::authorize('update', $workflow);

        $data = $httpRequest->validate([
            'notify_type' => ['required', Rule::in(['BUSINESS_FUNCTION', 'USER'])],
            'notify_reference' => ['required', 'integer'],
        ]);

        if ($data['notify_type'] === 'BUSINESS_FUNCTION') {
            $httpRequest->validate(['notify_reference' => ['exists:business_functions,id']]);
        } else {
            $httpRequest->validate(['notify_reference' => ['exists:users,id']]);
        }

        $workflow->completionNotifications()->create([
            'notify_type' => $data['notify_type'],
            'notify_reference' => $data['notify_reference'],
            'created_by' => $httpRequest->user()->id,
        ]);

        return back()->with('status', 'Destinataire ajouté.');
    }

    public function destroy(Workflow $workflow, WorkflowCompletionNotification $completionNotification): RedirectResponse
    {
        Gate::authorize('update', $workflow);

        abort_unless($completionNotification->workflow_id === $workflow->id, 404);

        $completionNotification->delete();

        return back()->with('status', 'Destinataire retiré.');
    }
}
