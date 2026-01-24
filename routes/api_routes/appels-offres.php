<?php

use App\Http\Controllers\TypeAppelOffreController;
use App\Http\Controllers\AppelOffreController;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| Routes API
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->group(function () {

    // API Appels d'Offres
    Route::prefix('appels-offres')->name('api.appels-offres.')->group(function () {
        Route::get('/', [AppelOffreController::class, 'index'])->name('index')->middleware('can:appels_offres.read');;
    Route::post('/', [AppelOffreController::class, 'store'])->name('store')->middleware('can:appels_offres.create');;
    Route::get('/{id}', [AppelOffreController::class, 'show'])->name('show')->middleware('can:appels_offres.view-details');;
    Route::put('/{id}', [AppelOffreController::class, 'update'])->name('update')->middleware('can:appels_offres.update');;
    Route::delete('/{id}', [AppelOffreController::class, 'destroy'])->name('destroy')->middleware('can:appels_offres.delete');;


    Route::post('/{id}/terminer', [AppelOffreController::class, 'terminer'])->name('terminer')->middleware('can:appels_offres.update');;
    Route::post('/{id}/rouvrir', [AppelOffreController::class, 'rouvrir'])->name('rouvrir')->middleware('can:appels_offres.update');;

    // Actions spécifiques
    Route::post('/{id}/toggle-status', [AppelOffreController::class, 'toggleStatus'])->name('toggle-status')->middleware('can:appels_offres.toggle-status');;
    Route::post('/{id}/publier', [AppelOffreController::class, 'publier'])->name('publier')->middleware('can:appels_offres.update');;
    Route::post('/{id}/cloturer', [AppelOffreController::class, 'cloturer'])->name('cloturer')->middleware('can:appels_offres.update');;
    Route::get('/{id}/statistiques', [AppelOffreController::class, 'statistiques'])->name('statistiques')->middleware('can:appels_offres.view-details');;
    Route::post('/{id}/duplicate', [AppelOffreController::class, 'duplicate'])->name('duplicate')->middleware('can:appels_offres.duplicate');;

    //Lots de l'appel d'offre
    Route::get('/{id}/lot', [AppelOffreController::class, 'lotsByOffre'])->name('lots-by-offre')->middleware('can:lots.read');;
    Route::get('/{id}/lot/{slug}', [AppelOffreController::class, 'showLotByOffre'])->name('show-lot-by-offre')->middleware('can:lots.view-details');;
    });
});
