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
    Route::get('/', [TypeAppelOffreController::class, 'index'])->name('index')->middleware('can:type_appels_offres.read');
    Route::post('/', [TypeAppelOffreController::class, 'store'])->name('store')->middleware('can:type_appels_offres.create');
    Route::get('/{id}', [TypeAppelOffreController::class, 'show'])->name('show')->middleware('can:type_appels_offres.view-details');
    Route::put('/{id}', [TypeAppelOffreController::class, 'update'])->name('update')->middleware('can:type_appels_offres.update');
    Route::delete('/{id}', [TypeAppelOffreController::class, 'destroy'])->name('destroy')->middleware('can:type_appels_offres.delete');

    // Actions spécifiques
    Route::post('/{id}/toggle-status', [TypeAppelOffreController::class, 'toggleStatus'])->name('toggle-status')->middleware('can:type_appels_offres.toggle-status');
    Route::post('/check-montant', [TypeAppelOffreController::class, 'checkMontant'])->name('check-montant')->middleware('can:type_appels_offres.read');
    Route::get('/{id}/generer-numero', [TypeAppelOffreController::class, 'genererNumero'])->name('generer-numero')->middleware('can:type_appels_offres.create');
    Route::get('/export/{format?}', [TypeAppelOffreController::class, 'export'])->name('export')->middleware('can:type_appels_offres.download');

    Route::prefix('{id}/appels-offres')->name('appels-offres.')->group( function  (){
        Route::get('/', [TypeAppelOffreController::class, 'fetchAOByTAO'])->name('index')->middleware('can:appels_offres.read');
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
        Route::get('/', [TypeAppelOffreController::class, 'index'])->middleware('can:type_appels_offres.read');
        Route::post('/', [TypeAppelOffreController::class, 'store'])->middleware('can:type_appels_offres.create');
        Route::get('/{id}', [TypeAppelOffreController::class, 'show'])->middleware('can:type_appels_offres.view-details');
        Route::put('/{id}', [TypeAppelOffreController::class, 'update'])->middleware('can:type_appels_offres.update');
        Route::delete('/{id}', [TypeAppelOffreController::class, 'destroy'])->middleware('can:type_appels_offres.delete');

        Route::post('/{id}/toggle-status', [TypeAppelOffreController::class, 'toggleStatus'])->middleware('can:type_appels_offres.toggle-status');
        Route::post('/check-montant', [TypeAppelOffreController::class, 'checkMontant'])->middleware('can:type_appels_offres.read');
        Route::get('/{id}/generer-numero', [TypeAppelOffreController::class, 'genererNumero'])->middleware('can:type_appels_offres.create');
    });
});
