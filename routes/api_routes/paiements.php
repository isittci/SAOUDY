<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaiementController;



Route::middleware(['auth:sanctum'])->group(function () {

    // Route globale pour tous les paiements
    Route::get('/paiements', [PaiementController::class, 'allPaiements'])->name('api.paiements.all')->middleware('can:paiements.read');

});


Route::middleware(['auth:sanctum'])->prefix('factures/{factureId}/paiements')->name('api.paiements.')->group(function () {
    // Routes CRUD de base
    Route::get('/', [PaiementController::class, 'index'])->name('index')->middleware('can:paiements.read');
    Route::get('/create', [PaiementController::class, 'create'])->name('create')->middleware('can:paiements.create');
    // Corbeille
    Route::get('/trashed', [PaiementController::class, 'trashed'])->name('trashed')->middleware('can:paiements.view-history');

    // Paiements par facture
    Route::get('/by-facture', [PaiementController::class, 'getByFacture'])->name('by-facture')->middleware('can:paiements.read');

    Route::get('/export', [PaiementController::class, 'export'])->name('export')->middleware('can:paiements.read');

    // Actions en masse
    Route::post('/valider-masse', [PaiementController::class, 'validerMasse'])->name('valider-masse')->middleware('can:paiements.validate');
    Route::post('/confirmer-masse', [PaiementController::class, 'confirmerMasse'])->name('confirmer-masse')->middleware('can:paiements.confirm');

    // Statistiques et rapports
    Route::get('/statistiques', [PaiementController::class, 'statistiques'])->name('statistiques')->middleware('can:paiements.read');
    Route::get('/statistiques/banques', [PaiementController::class, 'statistiquesParBanque'])->name('stats-banques')->middleware('can:paiements.read');
    Route::get('/statistiques/mois', [PaiementController::class, 'statistiquesParMois'])->name('stats-mois')->middleware('can:paiements.read');


    Route::post('/', [PaiementController::class, 'store'])->name('store')->middleware('can:paiements.create');
    Route::get('/{paiement}', [PaiementController::class, 'show'])->name('show')->middleware('can:paiements.view-details');
    Route::get('/{paiement}/edit', [PaiementController::class, 'edit'])->name('edit')->middleware('can:paiements.update');
    Route::put('/{paiement}', [PaiementController::class, 'update'])->name('update')->middleware('can:paiements.update');
    Route::delete('/{paiement}', [PaiementController::class, 'destroy'])->name('destroy')->middleware('can:paiements.delete');

    // Routes spécifiques au workflow
    Route::post('/{paiement}/valider', [PaiementController::class, 'valider'])->name('valider')->middleware('can:paiements.validate');
    Route::post('/{paiement}/traitement', [PaiementController::class, 'mettreEnTraitement'])->name('traitement')->middleware('can:paiements.process');
    Route::post('/{paiement}/confirmer', [PaiementController::class, 'confirmerPaiement'])->name('confirmer')->middleware('can:paiements.confirm');
    Route::post('/{paiement}/rejeter', [PaiementController::class, 'rejeter'])->name('rejeter')->middleware('can:paiements.reject');
    Route::post('/{paiement}/annuler', [PaiementController::class, 'annuler'])->name('annuler')->middleware('can:paiements.cancel');
    Route::post('/{paiement}/remettre-en-attente', [PaiementController::class, 'remettreEnAttente'])->name('remettre-attente')->middleware('can:paiements.pending');

    Route::post('/{id}/restore', [PaiementController::class, 'restore'])->name('restore')->middleware('can:paiements.delete');
    Route::delete('/{id}/force-delete', [PaiementController::class, 'forceDelete'])->name('force-delete')->middleware('can:paiements.delete');
});
