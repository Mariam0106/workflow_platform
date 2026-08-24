<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workflow\Admin;

use App\Enums\RequestStatus;
use App\Http\Controllers\Controller;
use App\Models\Request as RequestModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * ==========================================================================
 * RequestController (BackOffice)
 * ==========================================================================
 *
 * BR-74/75 : vue d'ensemble Administrateur sur TOUTES les Demandes de la
 * plateforme, quel que soit le Demandeur - contrairement à
 * MyRequestController (uniquement les siennes) et MyValidationController
 * (uniquement celles qu'on doit soi-même valider). Lecture seule : gérer
 * le cycle de vie d'une Demande (approuver/rejeter) reste réservé au
 * Validateur désigné par l'Étape courante, jamais à l'Administrateur
 * lui-même (voir RequestPolicy::before(), qui exclut délibérément
 * update/delete du contournement Administrateur).
 * ==========================================================================
 */
class RequestController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', RequestModel::class);

        $status = $request->query('status');
        $search = $request->query('q');

        $requests = RequestModel::query()
            ->when(
                $status && in_array($status, array_column(RequestStatus::cases(), 'value'), true),
                fn ($q) => $q->where('status', $status),
            )
            ->when($search, fn ($q) => $q->where(fn ($inner) => $inner
                ->where('reference_number', 'like', "%{$search}%")
                ->orWhereHas('form', fn ($f) => $f->where('name', 'like', "%{$search}%"))
                ->orWhereHas('requester', fn ($r) => $r->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%"))))
            ->with(['form', 'requester', 'currentStep'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('workflow.admin.requests.index', [
            'requests' => $requests,
            'activeStatus' => $status,
            'search' => $search,
        ]);
    }
}
