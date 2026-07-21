<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// --- Un fichier de routes par domaine (voir règles de répartition) --------

require __DIR__.'/organisation.php';

// require __DIR__.'/workflow.php'; // À ajouter par Lali quand le fichier existera
