<?php

use App\Http\Controllers\TypeAppelOffreController;
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

    // Actions spécifiques
    Route::post('/{id}/toggle-status', [AppelOffreController::class, 'toggleStatus'])->name('toggle-status');
    Route::post('/{id}/publier', [AppelOffreController::class, 'publier'])->name('publier');
    Route::post('/{id}/cloturer', [AppelOffreController::class, 'cloturer'])->name('cloturer');
    Route::get('/{id}/statistiques', [AppelOffreController::class, 'statistiques'])->name('statistiques');
    Route::post('/{id}/duplicate', [AppelOffreController::class, 'duplicate'])->name('duplicate');

    //Lots de l'appel d'offre
    Route::get('/{id}/lot', [AppelOffreController::class, 'lotsByOffre'])->name('lots-by-offre');
    Route::get('/{id}/lot/{slug}', [AppelOffreController::class, 'showLotByOffre'])->name('show-lot-by-offre');
});

/*
|--------------------------------------------------------------------------
| Routes API
|--------------------------------------------------------------------------
*/

Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {

    // API Appels d'Offres
    Route::prefix('appels-offres')->name('api.appels-offres.')->group(function () {
        Route::get('/', [AppelOffreController::class, 'index']);
        Route::post('/', [AppelOffreController::class, 'store']);
        Route::get('/{id}', [AppelOffreController::class, 'show']);
        Route::put('/{id}', [AppelOffreController::class, 'update']);
        Route::delete('/{id}', [AppelOffreController::class, 'destroy']);

        Route::post('/{id}/toggle-status', [AppelOffreController::class, 'toggleStatus']);
        Route::post('/{id}/publier', [AppelOffreController::class, 'publier']);
        Route::post('/{id}/cloturer', [AppelOffreController::class, 'cloturer']);
        Route::get('/{id}/statistiques', [AppelOffreController::class, 'statistiques']);
        Route::post('/{id}/duplicate', [AppelOffreController::class, 'duplicate']);
    });
});
