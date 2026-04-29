<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttributionLotPrestataireController;

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
        Route::get('/', [AttributionLotPrestataireController::class, 'index'])->name('index');

        // Création
        Route::post('/', [AttributionLotPrestataireController::class, 'store'])->name('store');

        // Affichage et Modification
        Route::get('/{attribution}', [AttributionLotPrestataireController::class, 'show'])->name('show');
        Route::get('/{attribution}/edit', [AttributionLotPrestataireController::class, 'edit'])->name('edit');
        Route::put('/{attribution}', [AttributionLotPrestataireController::class, 'update'])->name('update');

        // Actions métier
        Route::post('/{attribution}/ajout_date_effective_fin', [AttributionLotPrestataireController::class, 'ajoutDateEffectiveFin'])->name('ajout_date_effective_fin');
        Route::post('/{attribution}/suspendre', [AttributionLotPrestataireController::class, 'suspendre'])->name('suspendre');
        Route::post('/{attribution}/reprendre', [AttributionLotPrestataireController::class, 'reprendre'])->name('reprendre');
        Route::post('/{attribution}/retirer', [AttributionLotPrestataireController::class, 'retirer'])->name('retirer');
        Route::post('/{attribution}/terminer', [AttributionLotPrestataireController::class, 'terminer'])->name('terminer');
        Route::post('/{attribution}/avancement', [AttributionLotPrestataireController::class, 'mettreAJourAvancement'])->name('avancement');

        // Réattribution
        Route::get('/{attribution}/reattribuer', [AttributionLotPrestataireController::class, 'reattribuerForm'])->name('reattribuer.form');
        Route::post('/{attribution}/reattribuer', [AttributionLotPrestataireController::class, 'reattribuer'])->name('reattribuer');

        // Historiques
        Route::get('/lot/{lot}/historique', [AttributionLotPrestataireController::class, 'historiqueLot'])->name('historique.lot');
        Route::get('/prestataire/{prestataire}/historique', [AttributionLotPrestataireController::class, 'historiquePrestataire'])->name('historique.prestataire');
    });

    // ==================== ROUTES CONTEXTUELLES ====================

    // Depuis un lot
    Route::prefix('lots/{lot}')->name('lots.')->group(function () {
        Route::get('/attributions', [AttributionLotPrestataireController::class, 'historiqueLot'])->name('attributions');
        Route::get('/attribuer', [AttributionLotPrestataireController::class, 'create'])->name('attribuer');
        Route::post('/attribuer', [AttributionLotPrestataireController::class, 'store'])->name('attribuer.store');
    });

    // Depuis un prestataire
    Route::prefix('prestataires/{prestataire}')->name('prestataires.')->group(function () {
        Route::get('/attributions', [AttributionLotPrestataireController::class, 'historiquePrestataire'])->name('attributions');
        Route::get('/lots-attribues', [AttributionLotPrestataireController::class, 'lotsAttribuesPrestataire'])->name('lots-attribues');
    });

});



