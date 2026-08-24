<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});

// --- Un fichier de routes par domaine (voir règles de répartition) --------

require __DIR__.'/organisation.php';
require __DIR__.'/workflow.php';



