<?php

declare(strict_types=1);

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
});
