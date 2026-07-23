<?php

declare(strict_types=1);

use App\Http\Controllers\Organisation\Admin\DepartmentController;
use App\Http\Controllers\Organisation\Admin\EntityController;
use App\Http\Controllers\Organisation\Admin\UserController;
use App\Http\Controllers\Organisation\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Organisation\Auth\RegisteredUserController;
use App\Http\Controllers\Organisation\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Organisation Routes
|--------------------------------------------------------------------------
|
| Toutes les routes du module Organisation (Users, Auth, Departments,
| Entities...). Chargé depuis routes/web.php.
|
| Convention (voir "Règles de répartition du travail", section 3) :
| un fichier de routes par domaine - Lali possède routes/workflow.php,
| zéro conflit de merge possible sur ce fichier.
|
*/

// --- Jalon J1 : Auth minimale ---------------------------------------------

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Placeholder - remplacé par le vrai tableau de bord à l'Étape 14.
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    // --- Étape 11 : BackOffice Administration -----------------------------
    // Pas de middleware de rôle ici à dessein : l'autorisation fine est
    // gérée par les Policies (Étape 10) via Gate::authorize() dans chaque
    // Controller/Form Request - un simple 'auth' suffit au niveau route.
    Route::prefix('admin')->name('organisation.')->group(function () {
        Route::resource('users', UserController::class)->except(['destroy']);
        Route::post('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
        Route::post('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');

        Route::resource('departments', DepartmentController::class)->except(['show', 'destroy']);
        Route::post('departments/{department}/archive', [DepartmentController::class, 'archive'])->name('departments.archive');
        Route::post('departments/{department}/restore', [DepartmentController::class, 'restore'])->name('departments.restore');

        Route::resource('entities', EntityController::class)->except(['show', 'destroy']);
        Route::post('entities/{entity}/archive', [EntityController::class, 'archive'])->name('entities.archive');
        Route::post('entities/{entity}/restore', [EntityController::class, 'restore'])->name('entities.restore');
    });
});
