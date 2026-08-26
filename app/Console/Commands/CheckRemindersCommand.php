<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\Repositories\Workflow\RequestRepositoryInterface;
use App\Enums\ApplicationRoleCode;
use App\Enums\NotificationChannel;
use App\Enums\NotificationStatus;
use App\Mail\PendingValidationsReminderMail;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * ==========================================================================
 * CheckRemindersCommand (workflow:check-reminders)
 * ==========================================================================
 *
 * BR-45/46 : rappel quotidien à chaque Validateur ayant au moins une
 * Demande en attente de sa décision - évite l'oubli et la relance
 * manuelle systématique par le Demandeur.
 *
 * Réutilise volontairement RequestRepositoryInterface::
 * findPendingForValidator() - la MÊME logique déjà utilisée par l'écran
 * "Mes validations" (BusinessFunction, Utilisateur désigné, N+1 avec
 * escalade...) plutôt que de réécrire une résolution parallèle qui
 * pourrait diverger avec le temps.
 *
 * Planifiée quotidiennement à 9h (voir routes/console.php) - mais peut
 * aussi être lancée manuellement à tout moment pour tester :
 *     php artisan workflow:check-reminders
 * ==========================================================================
 */
class CheckRemindersCommand extends Command
{
    protected $signature = 'workflow:check-reminders';

    protected $description = "Envoie un rappel (e-mail + notification) à chaque Validateur ayant au moins une Demande en attente de sa décision.";

    public function __construct(
        private readonly RequestRepositoryInterface $requests,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $validators = User::query()
            ->where('is_active', true)
            ->whereHas('applicationRoles', fn ($q) => $q->where('code', ApplicationRoleCode::Validator->value))
            ->get();

        $remindersSent = 0;

        foreach ($validators as $validator) {
            $pending = $this->requests->findPendingForValidator($validator);

            if ($pending->isEmpty()) {
                continue;
            }

            Mail::to((string) $validator->email)->queue(new PendingValidationsReminderMail($validator, $pending));

            Notification::create([
                'recipient_id' => $validator->id,
                'title' => 'Rappel : demandes en attente',
                'message' => $pending->count() === 1
                    ? 'Une demande attend toujours votre validation.'
                    : "{$pending->count()} demandes attendent toujours votre validation.",
                'channel' => NotificationChannel::InApp,
                'status' => NotificationStatus::Sent,
            ]);

            $remindersSent++;
        }

        $this->info("Rappels envoyés à {$remindersSent} validateur(s) sur {$validators->count()} vérifié(s).");

        return self::SUCCESS;
    }
}
