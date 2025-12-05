<?php

use Illuminate\Support\Facades\Route;


require __DIR__ . '/auth-route.php';
require __DIR__ . '/dashboard.php';
require __DIR__ . '/types-appels-offres.php';
require __DIR__ . '/appels-offres.php';
require __DIR__ . '/caracteristiques-lots-routes.php';
require __DIR__ . '/routes_criteres_evaluations.php';
require __DIR__ . '/routes_proformas.php';
require __DIR__ . '/prestataires.php';

Route::get('/test', function () {
    return view('test');
});
