<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organisation;

use App\Enums\NotificationStatus;
use App\Enums\RequestStatus;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Request as RequestModel;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * ==========================================================================
 * DashboardController (Étape 14 - BackOffice)
 * ==========================================================================
 *
 * Cahier des charges, "Interface utilisateur (Tableau de bord)" : 5 KPI
 * (formulaires soumis / en attente / à valider / validées / refusées) +
 * activité récente.
 *
 * NOTE sur la frontière Organisation/Workflow : ce Controller lit
 * Request/Validation/Notification (Models du domaine Workflow) en
 * lecture seule, par de simples requêtes Eloquent - il n'importe ni
 * n'appelle aucun Repository/Service Workflow (interdit par la règle 0
 * du doc de répartition). Un tableau de bord agrège forcément les deux
 * domaines ; le jour où Mariam construit un vrai reporting Service côté
 * Workflow, ces requêtes pourront être déplacées derrière lui sans
 * changer la vue.
 * ==========================================================================
 */
class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        return view('dashboard', [
            'user' => $user,
            'kpis' => $this->kpisFor($user),
            'recentNotifications' => Notification::query()
                ->where('recipient_id', $user->id)
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }

    /**
     * @return array<string, int>
     */
    private function kpisFor(User $user): array
    {
        return [
            'submitted' => RequestModel::query()->where('requester_id', $user->id)->count(),
            'pending' => RequestModel::query()->where('requester_id', $user->id)->where('status', RequestStatus::Submitted)->count(),
            'to_validate' => Validation::query()->where('validator_id', $user->id)->whereNull('validated_at')->count(),
            'approved' => RequestModel::query()->where('requester_id', $user->id)->where('status', RequestStatus::Completed)->count(),
            'rejected' => RequestModel::query()->where('requester_id', $user->id)->where('status', RequestStatus::Rejected)->count(),
        ];
    }
}
