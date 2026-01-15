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
    Route::get('/', [ProformaController::class, 'index'])->name('index')->middleware('can:proformas.read');
    Route::get('/create', [ProformaController::class, 'create'])->name('create')->middleware('can:proformas.create');
    Route::get('/edit/{id}', [ProformaController::class, 'edit'])->name('edit')->middleware('can:proformas.update');
    Route::post('/', [ProformaController::class, 'store'])->name('store')->middleware('can:proformas.create');
    Route::get('/{id}', [ProformaController::class, 'show'])->name('show')->middleware('can:proformas.view-details');
    Route::put('/{id}', [ProformaController::class, 'update'])->name('update')->middleware('can:proformas.update');
    Route::delete('/{id}', [ProformaController::class, 'destroy'])->name('destroy')->middleware('can:proformas.delete');

    // Actions spécifiques

    Route::post('{id}/toggle-status', [ProformaController::class, 'toggleStatus'])->name('toggle-status')->middleware('can:proformas.update');
    Route::post('{id}/creer-version', [ProformaController::class, 'creerVersion'])->name('creer-version')->middleware('can:proformas.create-version');
    // Route::post('{id}/duplicate', [ProformaController::class, 'duplicate'])->name('duplicate')->middleware('can:proformas.duplicate');
    Route::get('{id}/historique', [ProformaController::class, 'historique'])->name('historique')->middleware('can:proformas.view-history');
});


/*
|--------------------------------------------------------------------------
| Routes API
|--------------------------------------------------------------------------
*/

Route::prefix('api')->name('api.')->middleware(['auth:sanctum'])->group(function () {

    // API Types d'Appels d'Offres
    Route::prefix('proformas')->name('proformas.')->group(function () {
        Route::get('/', [ProformaController::class, 'index'])->name('index')->middleware('can:proformas.read');
        Route::post('/', [ProformaController::class, 'store'])->name('store')->middleware('can:proformas.create');
        Route::get('/{id}', [ProformaController::class, 'show'])->name('show')->middleware('can:proformas.view-details');
        Route::put('/{id}', [ProformaController::class, 'update'])->name('update')->middleware('can:proformas.update');
        Route::delete('/{id}', [ProformaController::class, 'destroy'])->name('destroy')->middleware('can:proformas.delete');

        // Actions spécifiques
        Route::post('/{id}/toggle-status', [ProformaController::class, 'toggleStatus'])->name('toggle-status')->middleware('can:proformas.update');
    });
});
