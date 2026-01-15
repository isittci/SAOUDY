<?php

use App\Http\Controllers\FactureController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes pour la gestion des Factures
|--------------------------------------------------------------------------
|
| Ces routes gèrent toutes les opérations CRUD et les actions métier
| sur les factures (validation, rejet, annulation, etc.)
|
*/

Route::prefix('factures')->name('factures.')->middleware(['auth'])->group(function () {

    // =========================================================================
    // ROUTES CRUD STANDARD
    // =========================================================================

    // Liste des factures avec filtres et pagination
    Route::get('/', [FactureController::class, 'index'])->name('index')->middleware('can:factures.read');

    // Formulaire de création
    Route::get('/create', [FactureController::class, 'create'])->name('create')->middleware('can:factures.create');

    // Enregistrement d'une nouvelle facture
    Route::post('/', [FactureController::class, 'store'])->name('store')->middleware('can:factures.create');

    // Affichage des détails d'une facture
    Route::get('/{facture}', [FactureController::class, 'show'])->name('show')->middleware('can:factures.view-details');

    // Formulaire de modification
    Route::get('/{facture}/edit', [FactureController::class, 'edit'])->name('edit')->middleware('can:factures.update');

    // Mise à jour d'une facture
    Route::put('/{facture}', [FactureController::class, 'update'])->name('update')->middleware('can:factures.update');

    // Suppression d'une facture
    Route::delete('/{facture}', [FactureController::class, 'destroy'])->name('destroy')->middleware('can:factures.delete');

    // =========================================================================
    // ROUTES D'ACTIONS MÉTIER
    // =========================================================================

    // Valider une facture (passage de "en_attente" à "validée")
    Route::post('/{facture}/valider', [FactureController::class, 'valider'])->name('valider')->middleware('can:factures.validate');

    // Rejeter une facture avec motif
    Route::post('/{facture}/rejeter', [FactureController::class, 'rejeter'])->name('rejeter')->middleware('can:factures.reject');

    // Annuler une facture avec motif
    Route::post('/{facture}/annuler', [FactureController::class, 'annuler'])->name('annuler')->middleware('can:factures.cancel');

    // Remettre une facture rejetée en attente
    Route::post('/{facture}/remettre-en-attente', [FactureController::class, 'remettreEnAttente'])->name('remettre-en-attente')->middleware('can:factures.update');

    // Dupliquer une facture
    Route::post('/{facture}/dupliquer', [FactureController::class, 'dupliquer'])->name('dupliquer')->middleware('can:factures.duplicate');

    // =========================================================================
    // ROUTES API / UTILITAIRES
    // =========================================================================

    // Récupérer les informations d'une proforma (AJAX)
    Route::get('/api/proforma/{proforma}', [FactureController::class, 'getProformaInfo'])->name('api.proforma-info')->middleware('can:factures.view-details');

    // Statistiques des factures
    Route::get('/statistiques/dashboard', [FactureController::class, 'statistiques'])->name('statistiques')->middleware('can:factures.view-details');
});
