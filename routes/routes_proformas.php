<?php

use App\Http\Controllers\ProformaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes Web
|--------------------------------------------------------------------------
*/

// Routes pour les Types d'Appels d'Offres
Route::middleware(['auth'])->prefix('proformas')->name('proformas.')->group(function () {
    Route::get('/', [ProformaController::class, 'index'])->name('index');
    Route::get('/create', [ProformaController::class, 'create'])->name('create');
    Route::get('/edit/{id}', [ProformaController::class, 'edit'])->name('edit');
    Route::post('/', [ProformaController::class, 'store'])->name('store');
    Route::get('/{id}', [ProformaController::class, 'show'])->name('show');
    Route::put('/{id}', [ProformaController::class, 'update'])->name('update');
    Route::delete('/{id}', [ProformaController::class, 'destroy'])->name('destroy');

    // Actions spécifiques

    Route::post('{id}/toggle-status', [ProformaController::class, 'toggleStatus'])->name('toggle-status');
Route::post('{id}/creer-version', [ProformaController::class, 'creerVersion'])->name('creer-version');
Route::post('{id}/duplicate', [ProformaController::class, 'duplicate'])->name('duplicate');
Route::get('{id}/historique', [ProformaController::class, 'historique'])->name('historique');

});


/*
|--------------------------------------------------------------------------
| Routes API
|--------------------------------------------------------------------------
*/

Route::prefix('api')->name('api.')->middleware(['auth:sanctum'])->group(function () {

    // API Types d'Appels d'Offres
    Route::prefix('proformas')->name('proformas.')->group(function () {
        Route::get('/', [ProformaController::class, 'index'])->name('index');
        Route::post('/', [ProformaController::class, 'store'])->name('store');
        Route::get('/{id}', [ProformaController::class, 'show'])->name('show');
        Route::put('/{id}', [ProformaController::class, 'update'])->name('update');
        Route::delete('/{id}', [ProformaController::class, 'destroy'])->name('destroy');

        // Actions spécifiques
        Route::post('/{id}/toggle-status', [ProformaController::class, 'toggleStatus'])->name('toggle-status');
    });
});
