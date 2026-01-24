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
    Route::get('/', [CaracteristiqueAppelOffreController::class, 'index'])->name('index')->middleware('can:caracteristiques_appels_offres.read');
    Route::get('/create', [CaracteristiqueAppelOffreController::class, 'create'])->name('create')->middleware('can:caracteristiques_appels_offres.create');
    Route::post('/', [CaracteristiqueAppelOffreController::class, 'store'])->name('store')->middleware('can:caracteristiques_appels_offres.create');
    Route::get('/{caracteristique}', [CaracteristiqueAppelOffreController::class, 'show'])->name('show')->middleware('can:caracteristiques_appels_offres.view-details');
    Route::get('/{caracteristique}/edit', [CaracteristiqueAppelOffreController::class, 'edit'])->name('edit')->middleware('can:caracteristiques_appels_offres.update');
    Route::put('/{caracteristique}', [CaracteristiqueAppelOffreController::class, 'update'])->name('update')->middleware('can:caracteristiques_appels_offres.update');
    Route::delete('/{caracteristique}', [CaracteristiqueAppelOffreController::class, 'destroy'])->name('destroy')->middleware('can:caracteristiques_appels_offres.delete');

    // Actions spécifiques
    Route::get('/{caracteristique}/historique', [CaracteristiqueAppelOffreController::class, 'historique'])->name('historique')->middleware('can:caracteristiques_appels_offres.view-history');
    Route::post('/{caracteristique}/versions/{version}/restaurer', [CaracteristiqueAppelOffreController::class, 'restaurerVersion'])->name('restaurer-version')->middleware('can:caracteristiques_appels_offres.view-history');
});

/*
|--------------------------------------------------------------------------
| Routes Web - Lots
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('lots')->name('lots.')->group(function () {
    Route::get('/', [LotController::class, 'index'])->name('index')->middleware('can:lots.read');
    Route::get('/create', [LotController::class, 'create'])->name('create')->middleware('can:lots.create');
    Route::post('/', [LotController::class, 'store'])->name('store')->middleware('can:lots.create');
    Route::get('/{id}', [LotController::class, 'show'])->name('show')->middleware('can:lots.view-details');
    Route::get('/{id}/edit', [LotController::class, 'edit'])->name('edit')->middleware('can:lots.update');
    Route::put('/{id}', [LotController::class, 'update'])->name('update')->middleware('can:lots.update');
    Route::delete('/{id}', [LotController::class, 'destroy'])->name('destroy')->middleware('can:lots.delete');


    Route::post('/{id}/attribuer', [LotController::class, 'attribuer'])->name('attribuer')->middleware('can:attributions_lots.assign');
    Route::post('/{id}/retirer', [LotController::class, 'retirer'])->name('retirer')->middleware('can:attributions_lots.withdraw');
    Route::get('/{id}/historique', [LotController::class, 'historique'])->name('historique')->middleware('can:lots.view-history');
    Route::get('/{id}/statistiques', [LotController::class, 'statistiques'])->name('statistiques')->middleware('can:lots.read');
    Route::post('/{id}/duplicate', [LotController::class, 'duplicate'])->name('duplicate')->middleware('can:lots.duplicate');
});

Route::middleware(['auth'])->prefix('appels-offres/{appel_offre}/lots')->name('lots-appels-offres.')->group(function () {
    Route::get('/', [LotAppelOffreController::class, 'index'])->name('index')->middleware('can:lots.read');
    Route::get('/create', [LotAppelOffreController::class, 'create'])->name('create')->middleware('can:lots.create');
    Route::post('/', [LotAppelOffreController::class, 'store'])->name('store')->middleware('can:lots.create');
    Route::get('/{id}', [LotAppelOffreController::class, 'show'])->name('show')->middleware('can:lots.view-details');
    Route::get('/{id}/edit', [LotAppelOffreController::class, 'edit'])->name('edit')->middleware('can:lots.update');
    Route::put('/{id}', [LotAppelOffreController::class, 'update'])->name('update')->middleware('can:lots.update');
    Route::delete('/{id}', [LotAppelOffreController::class, 'destroy'])->name('destroy')->middleware('can:lots.delete');

    // Actions spécifiques
    Route::post('/{id}/attribuer', [LotAppelOffreController::class, 'attribuer'])->name('attribuer')->middleware('can:attributions_lots.assign');
    Route::post('/{id}/retirer', [LotAppelOffreController::class, 'retirer'])->name('retirer')->middleware('can:attributions_lots.withdraw');
    Route::get('/{id}/historique', [LotAppelOffreController::class, 'historique'])->name('historique')->middleware('can:lots.view-history');
    Route::get('/{id}/statistiques', [LotAppelOffreController::class, 'statistiques'])->name('statistiques')->middleware('can:lots.view-details');
    Route::post('/{id}/duplicate', [LotAppelOffreController::class, 'duplicate'])->name('duplicate')->middleware('can:lots.duplicate');
});


