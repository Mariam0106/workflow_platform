<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workflow\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * BR-69/70/73 : l'Historique/Journal d'Audit est immuable et en
 * lecture seule pour tout le monde, y compris un Administrateur - ce
 * Controller n'expose donc volontairement aucune action d'écriture,
 * uniquement index().
 */
class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', AuditLog::class);

        $search = $request->query('q');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $auditLogs = AuditLog::query()
            ->with('user')
            ->when($search, fn ($q) => $q->whereHas('user', fn ($u) => $u
                ->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")))
            ->when($request->filled('action'), fn ($q) => $q->action($request->input('action')))
            ->when($request->filled('entity_type'), fn ($q) => $q->entity($request->input('entity_type')))
            ->when($dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $actions = AuditLog::query()->distinct()->orderBy('action')->pluck('action');
        $entityTypes = AuditLog::query()->distinct()->orderBy('entity_type')->pluck('entity_type');

        return view('workflow.audit-logs.index', compact('auditLogs', 'actions', 'entityTypes', 'search', 'dateFrom', 'dateTo'));
    }
}
