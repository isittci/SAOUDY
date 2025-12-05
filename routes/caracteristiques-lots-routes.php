<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LotController;
use App\Http\Controllers\LotAppelOffreController;
use App\Http\Controllers\CaracteristiqueAppelOffreController;

/*
|--------------------------------------------------------------------------
| Routes Web - Caractéristiques des Appels d'Offres
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('appels-offres/{appel_offre}/caracteristiques')->name('caracteristiques-appels-offres.')->group(function () {
    Route::get('/', [CaracteristiqueAppelOffreController::class, 'index'])->name('index');
    Route::get('/create', [CaracteristiqueAppelOffreController::class, 'create'])->name('create');
    Route::post('/', [CaracteristiqueAppelOffreController::class, 'store'])->name('store');
    Route::get('/{caracteristique}', [CaracteristiqueAppelOffreController::class, 'show'])->name('show');
    Route::get('/{caracteristique}/edit', [CaracteristiqueAppelOffreController::class, 'edit'])->name('edit');
    Route::put('/{caracteristique}', [CaracteristiqueAppelOffreController::class, 'update'])->name('update');
    Route::delete('/{caracteristique}', [CaracteristiqueAppelOffreController::class, 'destroy'])->name('destroy');

    // Actions spécifiques
    Route::get('/{caracteristique}/historique', [CaracteristiqueAppelOffreController::class, 'historique'])->name('historique');
    Route::post('/{caracteristique}/versions/{version}/restaurer', [CaracteristiqueAppelOffreController::class, 'restaurerVersion'])->name('restaurer-version');
});

/*
|--------------------------------------------------------------------------
| Routes Web - Lots
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('lots')->name('lots.')->group(function () {
    Route::get('/', [LotController::class, 'index'])->name('index');
    Route::get('/create', [LotController::class, 'create'])->name('create');
    Route::post('/', [LotController::class, 'store'])->name('store');
    Route::get('/{id}', [LotController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [LotController::class, 'edit'])->name('edit');
    Route::put('/{id}', [LotController::class, 'update'])->name('update');
    Route::delete('/{id}', [LotController::class, 'destroy'])->name('destroy');

    // Actions spécifiques
    // Route::post('/{id}/attribuer', [LotController::class, 'attribuer'])->name('attribuer');
    Route::post('/{id}/retirer', [LotController::class, 'retirer'])->name('retirer');
    Route::get('/{id}/historique', [LotController::class, 'historique'])->name('historique');
    Route::get('/{id}/statistiques', [LotController::class, 'statistiques'])->name('statistiques');
    Route::post('/{id}/duplicate', [LotController::class, 'duplicate'])->name('duplicate');
});

Route::middleware(['auth'])->prefix('appels-offres/{appel_offre}/lots')->name('lots-appels-offres.')->group(function () {
    Route::get('/', [LotAppelOffreController::class, 'index'])->name('index');
    Route::get('/create', [LotAppelOffreController::class, 'create'])->name('create');
    Route::post('/', [LotAppelOffreController::class, 'store'])->name('store');
    Route::get('/{id}', [LotAppelOffreController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [LotAppelOffreController::class, 'edit'])->name('edit');
    Route::put('/{id}', [LotAppelOffreController::class, 'update'])->name('update');
    Route::delete('/{id}', [LotAppelOffreController::class, 'destroy'])->name('destroy');

    // Actions spécifiques
    Route::post('/{id}/attribuer', [LotAppelOffreController::class, 'attribuer'])->name('attribuer');
    Route::post('/{id}/retirer', [LotAppelOffreController::class, 'retirer'])->name('retirer');
    Route::get('/{id}/historique', [LotAppelOffreController::class, 'historique'])->name('historique');
    Route::get('/{id}/statistiques', [LotAppelOffreController::class, 'statistiques'])->name('statistiques');
    Route::post('/{id}/duplicate', [LotAppelOffreController::class, 'duplicate'])->name('duplicate');
});

/*
|--------------------------------------------------------------------------
| Routes API - Caractéristiques des Appels d'Offres
|--------------------------------------------------------------------------
*/

Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {

    // API Caractéristiques
    Route::prefix('appels-offres/{appel_offre}/caracteristiques')->name('api.caracteristiques-appels-offres.')->group(function () {
        Route::get('/', [CaracteristiqueAppelOffreController::class, 'index']);
        Route::post('/', [CaracteristiqueAppelOffreController::class, 'store']);
        Route::get('/{caracteristique}', [CaracteristiqueAppelOffreController::class, 'show']);
        Route::put('/{caracteristique}', [CaracteristiqueAppelOffreController::class, 'update']);
        Route::delete('/{caracteristique}', [CaracteristiqueAppelOffreController::class, 'destroy']);

        Route::get('/{caracteristique}/historique', [CaracteristiqueAppelOffreController::class, 'historique']);
        Route::post('/{caracteristique}/versions/{version}/restaurer', [CaracteristiqueAppelOffreController::class, 'restaurerVersion']);
    });

    // API Lots
    // Route::prefix('lots')->name('api.lots.')->group(function () {
    //     Route::get('/', [LotController::class, 'index']);
    //     Route::post('/', [LotController::class, 'store']);
    //     Route::get('/{id}', [LotController::class, 'show']);
    //     Route::put('/{id}', [LotController::class, 'update']);
    //     Route::delete('/{id}', [LotController::class, 'destroy']);

    //     // Route::post('/{id}/attribuer', [LotController::class, 'attribuer']);
    //     Route::post('/{id}/retirer', [LotController::class, 'retirer']);
    //     Route::get('/{id}/historique', [LotController::class, 'historique']);
    //     Route::get('/{id}/statistiques', [LotController::class, 'statistiques']);
    //     Route::post('/{id}/duplicate', [LotController::class, 'duplicate']);
    // });

    Route::prefix('appels-offres/{appel_offre}/lots')->name('api.lots-appels-offres.')->group(function () {
        Route::get('/', [LotAppelOffreController::class, 'index'])->name('index');
        Route::get('/create', [LotAppelOffreController::class, 'create'])->name('create');
        Route::post('/', [LotAppelOffreController::class, 'store'])->name('store');
        Route::get('/{id}', [LotAppelOffreController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [LotAppelOffreController::class, 'edit'])->name('edit');
        Route::put('/{id}', [LotAppelOffreController::class, 'update'])->name('update');
        Route::delete('/{id}', [LotAppelOffreController::class, 'destroy'])->name('destroy');

        // Actions spécifiques
        Route::post('/{id}/attribuer', [LotAppelOffreController::class, 'attribuer'])->name('attribuer');
        Route::post('/{id}/retirer', [LotAppelOffreController::class, 'retirer'])->name('retirer');
        Route::get('/{id}/historique', [LotAppelOffreController::class, 'historique'])->name('historique');
        Route::get('/{id}/statistiques', [LotAppelOffreController::class, 'statistiques'])->name('statistiques');
        Route::post('/{id}/duplicate', [LotAppelOffreController::class, 'duplicate'])->name('duplicate');
    });
});
