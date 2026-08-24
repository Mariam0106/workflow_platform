<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workflow;

use App\Enums\NotificationStatus;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * BR-63/64 : les Notifications In-App générées automatiquement par le
 * moteur (RequestSubmitted/RequestApproved/RequestRejected via les
 * Listeners de l'Étape 9) - cet écran ne fait qu'en afficher la liste
 * et permettre de les marquer comme lues, aucune règle métier ici.
 */
class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = Notification::query()
            ->where('recipient_id', $request->user()->id)
            ->with('request')
            ->latest()
            ->paginate(20);

        return view('workflow.notifications.index', ['notifications' => $notifications]);
    }

    public function markAsRead(Request $request, Notification $notification): RedirectResponse
    {
        abort_unless($notification->recipient_id === $request->user()->id, 403);

        if (! $notification->isRead()) {
            $notification->update(['status' => NotificationStatus::Read, 'read_at' => now()]);
        }

        return back();
    }
}
