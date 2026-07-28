<?php

use App\Http\Controllers\Workflow\FormController;
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
});
