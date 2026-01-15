<?php

use App\Http\Controllers\BanqueController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes pour la gestion des Banques
|--------------------------------------------------------------------------
|
| Ces routes gèrent toutes les opérations CRUD pour les banques,
| ainsi que les fonctionnalités supplémentaires comme la duplication,
| le changement de statut, et la gestion de la corbeille.
|
*/

Route::middleware(['auth'])->prefix('{prestataireId}/banques')->name('banques.')->group(function () {

    // =========================================================================
    // ROUTES CRUD STANDARD
    // =========================================================================

    // Liste des banques
    Route::get('/', [BanqueController::class, 'index'])->name('index')->middleware('can:banques_prestataires.read');

    // Formulaire de création
    Route::get('/create', [BanqueController::class, 'create'])->name('create')->middleware('can:banques_prestataires.create');

    // Enregistrer une nouvelle banque
    Route::post('/', [BanqueController::class, 'store'])->name('store')->middleware('can:banques_prestataires.create');

    // Afficher les détails d'une banque
    Route::get('/{banque}', [BanqueController::class, 'show'])->name('show')->middleware('can:banques_prestataires.view-details');

    // Formulaire d'édition
    Route::get('/{banque}/edit', [BanqueController::class, 'edit'])->name('edit')->middleware('can:banques_prestataires.update');

    // Mettre à jour une banque
    Route::put('/{banque}', [BanqueController::class, 'update'])->name('update')->middleware('can:banques_prestataires.update');

    Route::patch('/{banque}', [BanqueController::class, 'update'])->middleware('can:banques_prestataires.update');

    // Supprimer une banque (soft delete)
    Route::delete('/{banque}', [BanqueController::class, 'destroy'])->name('destroy')->middleware('can:banques_prestataires.delete');

    // =========================================================================
    // ROUTES SUPPLÉMENTAIRES
    // =========================================================================

    // Basculer le statut actif/inactif
    Route::patch('/{banque}/toggle-statut', [BanqueController::class, 'toggleStatut'])->name('toggle-statut')->middleware('can:banques_prestataires.toggle-status');

    // Dupliquer une banque
    Route::post('/{banque}/dupliquer', [BanqueController::class, 'dupliquer'])->name('dupliquer')->middleware('can:banques_prestataires.duplicate');

    // Exporter les banques
    Route::get('/export/data', [BanqueController::class, 'export'])->name('export')->middleware('can:banques_prestataires.read');

    // =========================================================================
    // ROUTES CORBEILLE (TRASH)
    // =========================================================================

    // Afficher les banques supprimées
    Route::get('/corbeille/liste', [BanqueController::class, 'trashed'])->name('trashed')->middleware('can:banques_prestataires.read');

    // Restaurer une banque
    Route::post('/{id}/restore', [BanqueController::class, 'restore'])->name('restore')->middleware('can:banques_prestataires.update');

    // Supprimer définitivement une banque
    Route::delete('/{id}/force-delete', [BanqueController::class, 'forceDelete'])->name('force-delete')->middleware('can:banques_prestataires.update');

});

/*
|--------------------------------------------------------------------------
| Routes API pour les Banques (AJAX)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('api/{prestataireId}/banques')->name('api.banques.')->group(function () {

    // ✅ CORRECT - Plus besoin de /prestataire/{prestataireId}, tu l'as déjà dans le prefix
    Route::get('/', [BanqueController::class, 'getByPrestataire'])->name('by-prestataire')->middleware('can:banques_prestataires.read');

});
