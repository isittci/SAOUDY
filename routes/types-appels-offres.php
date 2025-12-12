<?php

use App\Http\Controllers\TypeAppelOffreController;
use App\Http\Controllers\AppelOffreController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes Web
|--------------------------------------------------------------------------
*/

// Routes pour les Types d'Appels d'Offres
Route::middleware(['auth'])->prefix('types-appels-offres')->name('types-appels-offres.')->group(function () {
    Route::get('/', [TypeAppelOffreController::class, 'index'])->name('index');
    Route::post('/', [TypeAppelOffreController::class, 'store'])->name('store');
    Route::get('/{id}', [TypeAppelOffreController::class, 'show'])->name('show');
    Route::put('/{id}', [TypeAppelOffreController::class, 'update'])->name('update');
    Route::delete('/{id}', [TypeAppelOffreController::class, 'destroy'])->name('destroy');

    // Actions spécifiques
    Route::post('/{id}/toggle-status', [TypeAppelOffreController::class, 'toggleStatus'])->name('toggle-status');
    Route::post('/check-montant', [TypeAppelOffreController::class, 'checkMontant'])->name('check-montant');
    Route::get('/{id}/generer-numero', [TypeAppelOffreController::class, 'genererNumero'])->name('generer-numero');
    Route::get('/export/{format?}', [TypeAppelOffreController::class, 'export'])->name('export');

    Route::prefix('{id}/appels-offres')->name('appels-offres.')->group( function  (){
        Route::get('/', [TypeAppelOffreController::class, 'fetchAOByTAO'])->name('index');
    });
});


/*
|--------------------------------------------------------------------------
| Routes API types_appels_offres.appels_offres
|--------------------------------------------------------------------------
*/

Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {

    // API Types d'Appels d'Offres
    Route::prefix('types-appels-offres')->name('api.types-appels-offres.')->group(function () {
        Route::get('/', [TypeAppelOffreController::class, 'index']);
        Route::post('/', [TypeAppelOffreController::class, 'store']);
        Route::get('/{id}', [TypeAppelOffreController::class, 'show']);
        Route::put('/{id}', [TypeAppelOffreController::class, 'update']);
        Route::delete('/{id}', [TypeAppelOffreController::class, 'destroy']);

        Route::post('/{id}/toggle-status', [TypeAppelOffreController::class, 'toggleStatus']);
        Route::post('/check-montant', [TypeAppelOffreController::class, 'checkMontant']);
        Route::get('/{id}/generer-numero', [TypeAppelOffreController::class, 'genererNumero']);
    });
});
