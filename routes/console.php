<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduler (Étape 12 - Infrastructure)
|--------------------------------------------------------------------------
|
| Frontière (voir "Règles de répartition du travail", section 6) :
|   - La COMMANDE Artisan métier (contenant la logique BR-45/46) est du
|     Workflow -> Mariam la code (`workflow:check-reminders`).
|   - L'ENREGISTREMENT dans le planificateur ci-dessous est de
|     l'infrastructure -> Dev1 (moi).
|
| Ce fichier ne contient donc AUCUNE règle métier, seulement le "quand"
| et le "avec quelles options" - jamais le "quoi".
|
*/

// --- Organisation : tâches de maintenance standard Laravel -----------------
// Rien de métier ici, uniquement de l'hygiène d'infrastructure.

// Purge les tokens de réinitialisation de mot de passe expirés.
Schedule::command('auth:clear-resets')->daily();

// Purge les anciens enregistrements de jobs échoués/batches terminés
// (garde l'historique 7 jours par défaut - table failed_jobs/job_batches,
// voir database/migrations/0001_01_01_000002_create_jobs_table.php).
Schedule::command('queue:prune-failed', ['--hours' => 168])->weeklyOn(0, '01:00');
Schedule::command('queue:prune-batches', ['--hours' => 168])->weeklyOn(0, '01:15');

// --- Workflow : en attente de Mariam ---------------------------------------
// Dès que `workflow:check-reminders` existe côté Workflow (BR-45/46 -
// relances de validation en attente), décommenter la ligne suivante.
// Ne PAS créer de commande vide ici pour "faire passer le test" - le
// Scheduler ne doit référencer que des commandes qui existent réellement.
//
// Schedule::command('workflow:check-reminders')->daily();
