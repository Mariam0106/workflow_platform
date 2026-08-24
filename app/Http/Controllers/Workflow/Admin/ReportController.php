<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workflow\Admin;

use App\Enums\ApplicationRoleCode;
use App\Enums\RequestStatus;
use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\Request as RequestModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Cahier des charges, "Consultation globale des demandes et
 * indicateurs" (profil USER ADMIN). Volontairement de simples
 * décomptes/tableaux - pas de librairie de graphiques ajoutée pour ce
 * projet (cohérent avec le reste de l'interface : simple, clair).
 */
class ReportController extends Controller
{
    public function index(Request $request): View
    {
        // NOTE : RequestPolicy::viewAny() renvoie systematiquement true
        // (tout Utilisateur authentifie peut voir SES PROPRES Requests
        // via "Mes demandes") - impropre pour gater un ecran de
        // reporting global, reserve aux Administrateurs. On verifie
        // donc directement le Role actif plutot que de reutiliser cette
        // habilite hors de son contexte.
        abort_unless($request->user()->hasRole(ApplicationRoleCode::Administrator), 403);

        return view('workflow.reports.index', [
            'byStatus' => RequestModel::query()
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
            'byForm' => RequestModel::query()
                ->join('forms', 'forms.id', '=', 'requests.form_id')
                ->selectRaw('forms.name as form_name, count(*) as total')
                ->groupBy('forms.name')
                ->orderByDesc('total')
                ->get(),
            // NOTE : ->having() sur un withCount() casse sous SQLite
            // ("HAVING clause on a non-aggregate query" - contrairement
            // à MySQL, qui l'accepte) car la requête de base n'a pas de
            // GROUP BY réel, seulement une sous-requête corrélée. Filtré
            // en PHP à la place - portable entre SQLite (dev) et MySQL
            // (cible de production), et une poignée de Validateurs n'a
            // aucun enjeu de performance.
            'topValidators' => User::query()
                ->withCount(['validations as validations_count' => fn ($q) => $q->whereNotNull('validated_at')])
                ->get()
                ->filter(fn ($u) => $u->validations_count > 0)
                ->sortByDesc('validations_count')
                ->take(5)
                ->values(),
            'averageResolutionDays' => $this->averageResolutionDays(),
            'totalForms' => Form::query()->published()->count(),
        ]);
    }

    /**
     * Calculé en PHP plutôt qu'en SQL (ex. julianday() de SQLite) pour
     * rester portable entre SQLite (dev) et MySQL (cible de production
     * selon le cahier des charges) - un simple AVG() sur une poignée de
     * Requests terminées n'a aucun enjeu de performance.
     */
    private function averageResolutionDays(): ?float
    {
        $durations = RequestModel::query()
            ->where('status', RequestStatus::Completed)
            ->whereNotNull('completed_at')
            ->get(['submitted_at', 'completed_at'])
            ->map(fn ($r) => $r->submitted_at->diffInHours($r->completed_at) / 24);

        return $durations->isEmpty() ? null : round($durations->average(), 1);
    }
}
