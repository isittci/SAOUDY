<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrestataireLotController;

/*
|--------------------------------------------------------------------------
| Routes Attributions de Lots aux Prestataires
|--------------------------------------------------------------------------
|
| À ajouter dans routes/web.php ou à inclure via:
| require __DIR__.'/attributions.php';
|
*/

Route::middleware(['auth'])->group(function () {

    // ==================== ROUTES PRINCIPALES ====================

    Route::prefix('attributions')->name('attributions.')->group(function () {

        // Liste et Dashboard
        Route::get('/', [PrestataireLotController::class, 'index'])->name('index');
        Route::get('/dashboard', [PrestataireLotController::class, 'dashboard'])->name('dashboard');

        // Création
        // Route::get('/create', [PrestataireLotController::class, 'create'])->name('create');
        Route::post('/', [PrestataireLotController::class, 'store'])->name('store');

        // Affichage et Modification
        Route::get('/{attribution}', [PrestataireLotController::class, 'show'])->name('show');
        Route::get('/{attribution}/edit', [PrestataireLotController::class, 'edit'])->name('edit');
        Route::put('/{attribution}', [PrestataireLotController::class, 'update'])->name('update');

        // Actions métier
        Route::post('/{attribution}/suspendre', [PrestataireLotController::class, 'suspendre'])->name('suspendre');
        Route::post('/{attribution}/reprendre', [PrestataireLotController::class, 'reprendre'])->name('reprendre');
        Route::post('/{attribution}/retirer', [PrestataireLotController::class, 'retirer'])->name('retirer');
        Route::post('/{attribution}/terminer', [PrestataireLotController::class, 'terminer'])->name('terminer');
        Route::post('/{attribution}/avancement', [PrestataireLotController::class, 'mettreAJourAvancement'])->name('avancement');

        // Réattribution
        Route::get('/{attribution}/reattribuer', [PrestataireLotController::class, 'reattribuerForm'])->name('reattribuer.form');
        Route::post('/{attribution}/reattribuer', [PrestataireLotController::class, 'reattribuer'])->name('reattribuer');

        // Historiques
        Route::get('/lot/{lot}/historique', [PrestataireLotController::class, 'historiqueLot'])->name('historique.lot');
        Route::get('/prestataire/{prestataire}/historique', [PrestataireLotController::class, 'historiquePrestataire'])->name('historique.prestataire');
    });

    // ==================== ROUTES CONTEXTUELLES ====================

    // Depuis un lot
    Route::prefix('lots/{lot}')->name('lots.')->group(function () {
        Route::get('/attributions', [PrestataireLotController::class, 'historiqueLot'])->name('attributions');
        Route::get('/attribuer', [PrestataireLotController::class, 'create'])->name('attribuer');
        Route::post('/attribuer', [PrestataireLotController::class, 'store'])->name('attribuer.store');
    });

    // Depuis un prestataire
    Route::prefix('prestataires/{prestataire}')->name('prestataires.')->group(function () {
        Route::get('/attributions', [PrestataireLotController::class, 'historiquePrestataire'])->name('attributions');
    });

});

