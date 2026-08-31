<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organisation;

use App\Contracts\Repositories\Workflow\RequestRepositoryInterface;
use App\Enums\ApplicationRoleCode;
use App\Enums\RequestStatus;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Entity;
use App\Models\Notification;
use App\Models\Request as RequestModel;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * ==========================================================================
 * DashboardController
 * ==========================================================================
 *
 * Cahier des charges, "Interface utilisateur (Tableau de bord)" : KPI +
 * activité récente. Adapté au Rôle ACTIF de la session (BR-06) : un
 * Utilisateur voit ses propres KPI de demandeur, un Validateur voit sa
 * file d'attente, un Administrateur voit une vue d'ensemble de la
 * plateforme - jamais les trois à la fois (redondant pour un profil
 * mono-rôle, hors-sujet pour un profil multi-rôle qui n'agit pas
 * actuellement sous cette casquette).
 *
 * NOTE sur la frontière Organisation/Workflow : ce Controller lit
 * Request/Validation/Notification (Models du domaine Workflow) en
 * lecture seule, par de simples requêtes Eloquent - il n'appelle aucun
 * Service Workflow. Exception délibérée et étroite :
 * RequestRepositoryInterface (un Contract, pas une classe concrète)
 * est injecté uniquement pour le KPI "À valider" du Validateur, qui a
 * besoin de la même logique de résolution que
 * MyValidationController::index() (BR-36 : Rôle/Utilisateur/N+1) -
 * dupliquer cette logique ici aurait risqué de diverger silencieusement
 * de la vraie règle métier.
 */
class DashboardController extends Controller
{
    public function __construct(
        private readonly RequestRepositoryInterface $requests,
    ) {}

    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $activeRole = $user->activeRoleCode();

        return view('dashboard', [
            'user' => $user,
            'activeRole' => $activeRole,
            'cards' => match ($activeRole) {
                ApplicationRoleCode::Administrator => $this->adminCards(),
                ApplicationRoleCode::Validator => $this->validatorCards($user),
                default => $this->userCards($user),
            },
            'recentNotifications' => Notification::query()
                ->where('recipient_id', $user->id)
                ->with('request')
                ->latest()
                ->limit(5)
                ->get(),
            'topForms' => $activeRole === ApplicationRoleCode::Administrator ? $this->topForms() : null,
        ]);
    }

    /**
     * Les 5 Formulaires ayant reçu le plus de Demandes au total - donne
     * à l'Administrateur une vue de ce qui est réellement utilisé.
     *
     * @return \Illuminate\Support\Collection<int, object{name: string, total: int}>
     */
    private function topForms(): \Illuminate\Support\Collection
    {
        return RequestModel::query()
            ->join('forms', 'forms.id', '=', 'requests.form_id')
            ->selectRaw('forms.name as name, COUNT(*) as total')
            ->groupBy('forms.id', 'forms.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
    }

    /**
     * Vue d'ensemble plateforme - Administrateur (BR-74/75).
     *
     * @return array<int, array<string, mixed>>
     */
    private function adminCards(): array
    {
        return [
            ['label' => 'Utilisateurs actifs', 'value' => User::query()->active()->count(), 'icon' => 'users', 'accent' => 'blue', 'href' => route('organisation.users.index')],
            ['label' => 'Inscriptions en attente', 'value' => User::query()->where('registration_status', \App\Enums\RegistrationStatus::Pending)->count(), 'icon' => 'clock', 'accent' => 'warning', 'href' => route('organisation.registrations.index')],
            ['label' => 'Demandes en cours', 'value' => RequestModel::query()->where('status', RequestStatus::Submitted)->count(), 'icon' => 'inbox', 'accent' => 'warning', 'href' => route('workflow.admin.requests.index', ['status' => RequestStatus::Submitted->value])],
            ['label' => 'Demandes validées', 'value' => RequestModel::query()->where('status', RequestStatus::Completed)->count(), 'icon' => 'check', 'accent' => 'success', 'href' => route('workflow.admin.requests.index', ['status' => RequestStatus::Completed->value])],
            ['label' => 'Demandes refusées', 'value' => RequestModel::query()->where('status', RequestStatus::Rejected)->count(), 'icon' => 'close', 'accent' => 'danger', 'href' => route('workflow.admin.requests.index', ['status' => RequestStatus::Rejected->value])],
            ['label' => 'Départements', 'value' => Department::query()->active()->count(), 'icon' => 'building', 'accent' => 'slate', 'href' => route('organisation.departments.index')],
            ['label' => 'Entités', 'value' => Entity::query()->active()->count(), 'icon' => 'layers', 'accent' => 'slate', 'href' => route('organisation.entities.index')],
        ];
    }

    /**
     * File d'attente personnelle - Validateur (BR-54).
     *
     * @return array<int, array<string, mixed>>
     */
    private function validatorCards(User $user): array
    {
        return [
            ['label' => 'À valider', 'value' => $this->requests->findPendingForValidator($user)->count(), 'icon' => 'check', 'accent' => 'warning', 'href' => route('workflow.my-validations.index')],
            ['label' => 'Validées', 'value' => Validation::query()->where('validator_id', $user->id)->where('decision', \App\Enums\ValidationDecision::Approved)->count(), 'icon' => 'check', 'accent' => 'success', 'href' => route('workflow.my-validations.history', ['decision' => 'Approved'])],
            ['label' => 'Rejetées', 'value' => Validation::query()->where('validator_id', $user->id)->where('decision', \App\Enums\ValidationDecision::Rejected)->count(), 'icon' => 'close', 'accent' => 'danger', 'href' => route('workflow.my-validations.history', ['decision' => 'Rejected'])],
        ];
    }

    /**
     * Historique personnel de demandes - Utilisateur standard (défaut).
     *
     * @return array<int, array<string, mixed>>
     */
    private function userCards(User $user): array
    {
        return [
            ['label' => 'Formulaires soumis', 'value' => RequestModel::query()->where('requester_id', $user->id)->count(), 'icon' => 'inbox', 'accent' => 'blue', 'href' => route('workflow.my-requests.index')],
            ['label' => 'En attente', 'value' => RequestModel::query()->where('requester_id', $user->id)->where('status', RequestStatus::Submitted)->count(), 'icon' => 'clock', 'accent' => 'warning', 'href' => route('workflow.my-requests.index', ['status' => RequestStatus::Submitted->value])],
            ['label' => 'Validées', 'value' => RequestModel::query()->where('requester_id', $user->id)->where('status', RequestStatus::Completed)->count(), 'icon' => 'check', 'accent' => 'success', 'href' => route('workflow.my-requests.index', ['status' => RequestStatus::Completed->value])],
            ['label' => 'Refusées', 'value' => RequestModel::query()->where('requester_id', $user->id)->where('status', RequestStatus::Rejected)->count(), 'icon' => 'close', 'accent' => 'danger', 'href' => route('workflow.my-requests.index', ['status' => RequestStatus::Rejected->value])],
        ];
    }
}
