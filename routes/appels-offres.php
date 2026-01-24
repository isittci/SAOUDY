<?php

use App\Http\Controllers\AppelOffreController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes Web
|--------------------------------------------------------------------------
*/

// Routes pour les Appels d'Offres
Route::middleware(['auth'])->prefix('appels-offres')->name('appels-offres.')->group(function () {
    Route::get('/', [AppelOffreController::class, 'index'])->name('index');
    Route::get('/create', [AppelOffreController::class, 'create'])->name('create');
    Route::post('/', [AppelOffreController::class, 'store'])->name('store');
    Route::get('/{id}', [AppelOffreController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [AppelOffreController::class, 'edit'])->name('edit');
    Route::put('/{id}', [AppelOffreController::class, 'update'])->name('update');
    Route::delete('/{id}', [AppelOffreController::class, 'destroy'])->name('destroy');


    Route::post('/{id}/terminer', [AppelOffreController::class, 'terminer'])->name('terminer');
    Route::post('/{id}/rouvrir', [AppelOffreController::class, 'rouvrir'])->name('rouvrir');

    // Actions spécifiques
    Route::post('/{id}/toggle-status', [AppelOffreController::class, 'toggleStatus'])->name('toggle-status');
    Route::post('/{id}/publier', [AppelOffreController::class, 'publier'])->name('publier');
    Route::post('/{id}/cloturer', [AppelOffreController::class, 'cloturer'])->name('cloturer');
    Route::get('/{id}/statistiques', [AppelOffreController::class, 'statistiques'])->name('statistiques');
    Route::post('/{id}/duplicate', [AppelOffreController::class, 'duplicate'])->name('duplicate');
});


