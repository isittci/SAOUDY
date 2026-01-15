<?php

/**
 * =========================================================================
 * ROUTES PROFIL - À ajouter dans routes/web.php
 * =========================================================================
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

Route::middleware(['auth'])->prefix('profile')->name('profile.')->group(function () {

    // Afficher le profil
    Route::get('/', [ProfileController::class, 'index'])->name('index');

    // Mettre à jour les informations du profil
    Route::put('/update', [ProfileController::class, 'update'])->name('update');

    // Mettre à jour le mot de passe
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');

});
