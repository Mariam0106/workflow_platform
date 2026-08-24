<?php

use App\Http\Controllers\Workflow\Admin\AuditLogController;
use App\Http\Controllers\Workflow\Admin\FieldOptionController;
use App\Http\Controllers\Workflow\Admin\FormCategoryController;
use App\Http\Controllers\Workflow\Admin\FormController as AdminFormController;
use App\Http\Controllers\Workflow\Admin\FormFieldController;
use App\Http\Controllers\Workflow\Admin\ReportController;
use App\Http\Controllers\Workflow\Admin\RequestController as AdminRequestController;
use App\Http\Controllers\Workflow\Admin\TransitionConditionController;
use App\Http\Controllers\Workflow\Admin\WorkflowCategoryController;
use App\Http\Controllers\Workflow\Admin\WorkflowCompletionNotificationController;
use App\Http\Controllers\Workflow\Admin\WorkflowController as AdminWorkflowController;
use App\Http\Controllers\Workflow\Admin\WorkflowStepController;
use App\Http\Controllers\Workflow\Admin\WorkflowTransitionController;
use App\Http\Controllers\Workflow\AttachmentController;
use App\Http\Controllers\Workflow\FormController;
use App\Http\Controllers\Workflow\MyRequestController;
use App\Http\Controllers\Workflow\MyValidationController;
use App\Http\Controllers\Workflow\NotificationController;
use App\Http\Controllers\Workflow\RequestController;
use App\Http\Controllers\Workflow\ValidationController;
use App\Http\Controllers\Workflow\WorkflowController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Workflow Domain Routes
|--------------------------------------------------------------------------
|
| Fichier separe du domaine Organisation (voir routes/organisation.php) -
| meme principe que les Service Providers dedies par domaine.
|
*/

Route::middleware('auth')->prefix('workflow')->name('workflow.')->group(function () {
    Route::get('workflows', [WorkflowController::class, 'index'])->name('workflows.index');
    Route::get('workflows/{workflow}', [WorkflowController::class, 'show'])->name('workflows.show');

    Route::get('forms', [FormController::class, 'index'])->name('forms.index');
    Route::get('forms/{form}', [FormController::class, 'show'])->name('forms.show');

    Route::post('requests', [RequestController::class, 'store'])->name('requests.store');
    Route::get('requests/{request}', [RequestController::class, 'show'])->name('requests.show');

    Route::post('requests/{request}/validations', [ValidationController::class, 'store'])->name('requests.validations.store');

    // --- Espace "Mes demandes" (rôle User) --------------------------
    Route::get('my-requests', [MyRequestController::class, 'index'])->name('my-requests.index');
    Route::get('my-requests/new', [MyRequestController::class, 'selectForm'])->name('my-requests.select-form');
    Route::get('my-requests/new/{form}', [MyRequestController::class, 'create'])->name('my-requests.create');
    Route::post('my-requests/new/{form}', [MyRequestController::class, 'store'])->name('my-requests.store');
    Route::post('my-requests/new/{form}/draft', [MyRequestController::class, 'saveDraft'])->name('my-requests.save-draft');
    Route::get('my-requests/{request}', [MyRequestController::class, 'show'])->name('my-requests.show');
    Route::delete('my-requests/{request}', [MyRequestController::class, 'destroy'])->name('my-requests.destroy');

    // --- Pièces jointes (BR-51) - accessibles par Demandeur ET Validateur --
    Route::get('attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download');
    Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');

    // --- Espace "Mes validations" (rôle Validateur) ------------------
    Route::get('my-validations', [MyValidationController::class, 'index'])->name('my-validations.index');
    Route::get('my-validations/history', [MyValidationController::class, 'history'])->name('my-validations.history');
    Route::get('my-validations/{request}', [MyValidationController::class, 'show'])->name('my-validations.show');
    Route::post('my-validations/{request}', [MyValidationController::class, 'decide'])->name('my-validations.decide');

    // --- Notifications (tous rôles) ----------------------------------
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    // --- Étape 13 (Phase 2) : BackOffice "Dynamic Form Builder" ---------
    // Préfixé "admin/" et nommé "admin." pour ne jamais entrer en
    // collision avec les routes de lecture publique ci-dessus
    // (workflow.forms.index existe déjà pour tout Utilisateur
    // authentifié - workflow.admin.forms.index est un espace distinct,
    // Administrateur uniquement, voir FormPolicy/FormCategoryPolicy).
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('form-categories', FormCategoryController::class)
            ->except(['show', 'destroy'])
            ->parameters(['form-categories' => 'form_category']);
        Route::post('form-categories/{form_category}/archive', [FormCategoryController::class, 'archive'])->name('form-categories.archive');
        Route::post('form-categories/{form_category}/restore', [FormCategoryController::class, 'restore'])->name('form-categories.restore');

        Route::resource('workflow-categories', WorkflowCategoryController::class)
            ->except(['show', 'destroy'])
            ->parameters(['workflow-categories' => 'workflow_category']);
        Route::post('workflow-categories/{workflow_category}/archive', [WorkflowCategoryController::class, 'archive'])->name('workflow-categories.archive');
        Route::post('workflow-categories/{workflow_category}/restore', [WorkflowCategoryController::class, 'restore'])->name('workflow-categories.restore');

        Route::post('workflows/{workflow}/forms-from-existing', [AdminFormController::class, 'storeFromExisting'])->name('workflows.forms-from-existing');

        // --- Formulaires dynamiques (Form Builder, BR-10 à BR-18) ------
        Route::resource('forms', AdminFormController::class)->except(['show']);
        Route::post('forms/{form}/publish', [AdminFormController::class, 'publish'])->name('forms.publish');
        Route::post('forms/{form}/archive', [AdminFormController::class, 'archive'])->name('forms.archive');
        Route::post('forms/{form}/duplicate', [AdminFormController::class, 'duplicate'])->name('forms.duplicate');

        Route::prefix('forms/{form}/fields')->name('forms.fields.')->group(function () {
            Route::get('create', [FormFieldController::class, 'create'])->name('create');
            Route::post('/', [FormFieldController::class, 'store'])->name('store');
            Route::get('{field}/edit', [FormFieldController::class, 'edit'])->name('edit');
            Route::put('{field}', [FormFieldController::class, 'update'])->name('update');
            Route::delete('{field}', [FormFieldController::class, 'destroy'])->name('destroy');
            Route::post('{field}/move-up', [FormFieldController::class, 'moveUp'])->name('move-up');
            Route::post('{field}/move-down', [FormFieldController::class, 'moveDown'])->name('move-down');

            Route::post('{field}/options', [FieldOptionController::class, 'store'])->name('options.store');
            Route::delete('{field}/options/{option}', [FieldOptionController::class, 'destroy'])->name('options.destroy');
            Route::post('{field}/options/{option}/default', [FieldOptionController::class, 'setDefault'])->name('options.default');
        });

        // --- Workflows (Workflow Designer, BR-18 à BR-27, BR-32 à BR-34) ---
        Route::resource('workflows', AdminWorkflowController::class)->except(['show']);
        Route::post('workflows/{workflow}/publish', [AdminWorkflowController::class, 'publish'])->name('workflows.publish');
        Route::post('workflows/{workflow}/archive', [AdminWorkflowController::class, 'archive'])->name('workflows.archive');
        Route::post('workflows/{workflow}/duplicate', [AdminWorkflowController::class, 'duplicate'])->name('workflows.duplicate');

        Route::prefix('workflows/{workflow}/steps')->name('workflows.steps.')->group(function () {
            Route::get('create', [WorkflowStepController::class, 'create'])->name('create');
            Route::post('/', [WorkflowStepController::class, 'store'])->name('store');
            Route::get('{step}/edit', [WorkflowStepController::class, 'edit'])->name('edit');
            Route::put('{step}', [WorkflowStepController::class, 'update'])->name('update');
            Route::delete('{step}', [WorkflowStepController::class, 'destroy'])->name('destroy');
            Route::post('{step}/set-start', [WorkflowStepController::class, 'setAsStart'])->name('set-start');
            Route::post('{step}/move-up', [WorkflowStepController::class, 'moveUp'])->name('move-up');
            Route::post('{step}/move-down', [WorkflowStepController::class, 'moveDown'])->name('move-down');
            Route::post('reorder', [WorkflowStepController::class, 'reorder'])->name('reorder');
        });

        Route::prefix('workflows/{workflow}/transitions')->name('workflows.transitions.')->group(function () {
            Route::get('create', [WorkflowTransitionController::class, 'create'])->name('create');
            Route::post('/', [WorkflowTransitionController::class, 'store'])->name('store');
            Route::get('{transition}/edit', [WorkflowTransitionController::class, 'edit'])->name('edit');
            Route::put('{transition}', [WorkflowTransitionController::class, 'update'])->name('update');
            Route::delete('{transition}', [WorkflowTransitionController::class, 'destroy'])->name('destroy');

            Route::post('{transition}/conditions', [TransitionConditionController::class, 'store'])->name('conditions.store');
            Route::delete('{transition}/conditions/{condition}', [TransitionConditionController::class, 'destroy'])->name('conditions.destroy');
        });

        Route::prefix('workflows/{workflow}/completion-notifications')->name('workflows.completion-notifications.')->group(function () {
            Route::post('/', [WorkflowCompletionNotificationController::class, 'store'])->name('store');
            Route::delete('{completionNotification}', [WorkflowCompletionNotificationController::class, 'destroy'])->name('destroy');
        });

        Route::get('requests', [AdminRequestController::class, 'index'])->name('requests.index');

        // --- Historique / Audit (BR-69 à BR-73) --------------------------
        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

        // --- Rapports ------------------------------------------------
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    });
});
