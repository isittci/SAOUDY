<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppelOffreController;
use App\Http\Controllers\TypeAppelOffreController;



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


/*
|--------------------------------------------------------------------------
| Routes API
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
