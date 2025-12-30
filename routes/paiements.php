<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaiementController;


Route::middleware(['auth'])->prefix('factures/{factureId}/paiements')->name('paiements.')->group(function () {
    // Routes CRUD de base


    Route::get('/', [PaiementController::class, 'index'])->name('index');
    Route::get('/create', [PaiementController::class, 'create'])->name('create');
    // Corbeille
    Route::get('/trashed', [PaiementController::class, 'trashed'])->name('trashed');
    Route::post('/', [PaiementController::class, 'store'])->name('store');
    Route::get('/{paiement}', [PaiementController::class, 'show'])->name('show');
    Route::get('/{paiement}/edit', [PaiementController::class, 'edit'])->name('edit');
    Route::put('/{paiement}', [PaiementController::class, 'update'])->name('update');
    Route::delete('/{paiement}', [PaiementController::class, 'destroy'])->name('destroy');

    // Routes spécifiques au workflow
    Route::post('/{paiement}/valider', [PaiementController::class, 'valider'])->name('valider');
    Route::post('/{paiement}/traitement', [PaiementController::class, 'mettreEnTraitement'])->name('traitement');
    Route::post('/{paiement}/confirmer', [PaiementController::class, 'confirmerPaiement'])->name('confirmer');
    Route::post('/{paiement}/rejeter', [PaiementController::class, 'rejeter'])->name('rejeter');
    Route::post('/{paiement}/annuler', [PaiementController::class, 'annuler'])->name('annuler');
    Route::post('/{paiement}/remettre-en-attente', [PaiementController::class, 'remettreEnAttente'])->name('remettre-attente');

    // Actions en masse
    Route::post('/valider-masse', [PaiementController::class, 'validerMasse'])->name('valider-masse');
    Route::post('/confirmer-masse', [PaiementController::class, 'confirmerMasse'])->name('confirmer-masse');

    // Statistiques et rapports
    Route::get('/statistiques', [PaiementController::class, 'statistiques'])->name('statistiques');
    Route::get('/statistiques/banques', [PaiementController::class, 'statistiquesParBanque'])->name('stats-banques');
    Route::get('/statistiques/mois', [PaiementController::class, 'statistiquesParMois'])->name('stats-mois');
    Route::get('/export', [PaiementController::class, 'export'])->name('export');

    // Paiements par facture
    Route::get('/by-facture/{factureId}', [PaiementController::class, 'getByFacture'])->name('by-facture');


    Route::post('/{id}/restore', [PaiementController::class, 'restore'])->name('restore');
    Route::delete('/{id}/force-delete', [PaiementController::class, 'forceDelete'])->name('force-delete');
});
