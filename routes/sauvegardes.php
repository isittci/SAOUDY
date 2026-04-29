<?php
// routes/sauvegardes.php

use App\Http\Controllers\SauvegardeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('g4nz-bx')->name('sauvegardes.')->group(function () {

    // Routes statiques EN PREMIER
    Route::post('r7mx-yt',                      [SauvegardeController::class, 'store'])->name('store');
    Route::post('k2vj-hn',                      [SauvegardeController::class, 'purger'])->name('purger');

    // Liste
    Route::get('/',                              [SauvegardeController::class, 'index'])->name('index');

    // Actions paramétrées
    Route::get('{sauvegarde}/f9bw-qc',           [SauvegardeController::class, 'download'])->name('download');
    Route::get('{sauvegarde}/p5tx-dm',           [SauvegardeController::class, 'verifier'])->name('verifier');
    Route::delete('{sauvegarde}',                [SauvegardeController::class, 'destroy'])->name('destroy');
});
