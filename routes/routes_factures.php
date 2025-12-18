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
    Route::get('/', [FactureController::class, 'index'])->name('index');

    // Formulaire de création
    Route::get('/create', [FactureController::class, 'create'])->name('create');

    // Enregistrement d'une nouvelle facture
    Route::post('/', [FactureController::class, 'store'])->name('store');

    // Affichage des détails d'une facture
    Route::get('/{facture}', [FactureController::class, 'show'])->name('show');

    // Formulaire de modification
    Route::get('/{facture}/edit', [FactureController::class, 'edit'])->name('edit');

    // Mise à jour d'une facture
    Route::put('/{facture}', [FactureController::class, 'update'])->name('update');

    // Suppression d'une facture
    Route::delete('/{facture}', [FactureController::class, 'destroy'])->name('destroy');

    // =========================================================================
    // ROUTES D'ACTIONS MÉTIER
    // =========================================================================

    // Valider une facture (passage de "en_attente" à "validée")
    Route::post('/{facture}/valider', [FactureController::class, 'valider'])->name('valider');

    // Rejeter une facture avec motif
    Route::post('/{facture}/rejeter', [FactureController::class, 'rejeter'])->name('rejeter');

    // Annuler une facture avec motif
    Route::post('/{facture}/annuler', [FactureController::class, 'annuler'])->name('annuler');

    // Remettre une facture rejetée en attente
    Route::post('/{facture}/remettre-en-attente', [FactureController::class, 'remettreEnAttente'])->name('remettre-en-attente');

    // Dupliquer une facture
    Route::post('/{facture}/dupliquer', [FactureController::class, 'dupliquer'])->name('dupliquer');

    // =========================================================================
    // ROUTES API / UTILITAIRES
    // =========================================================================

    // Récupérer les informations d'une proforma (AJAX)
    Route::get('/api/proforma/{proforma}', [FactureController::class, 'getProformaInfo'])->name('api.proforma-info');

    // Statistiques des factures
    Route::get('/statistiques/dashboard', [FactureController::class, 'statistiques'])->name('statistiques');
});
