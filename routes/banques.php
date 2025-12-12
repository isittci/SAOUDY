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
    Route::get('/', [BanqueController::class, 'index'])
        ->name('index');

    // Formulaire de création
    Route::get('/create', [BanqueController::class, 'create'])
        ->name('create');

    // Enregistrer une nouvelle banque
    Route::post('/', [BanqueController::class, 'store'])
        ->name('store');

    // Afficher les détails d'une banque
    Route::get('/{banque}', [BanqueController::class, 'show'])
        ->name('show');

    // Formulaire d'édition
    Route::get('/{banque}/edit', [BanqueController::class, 'edit'])
        ->name('edit');

    // Mettre à jour une banque
    Route::put('/{banque}', [BanqueController::class, 'update'])
        ->name('update');

    Route::patch('/{banque}', [BanqueController::class, 'update']);

    // Supprimer une banque (soft delete)
    Route::delete('/{banque}', [BanqueController::class, 'destroy'])
        ->name('destroy');

    // =========================================================================
    // ROUTES SUPPLÉMENTAIRES
    // =========================================================================

    // Basculer le statut actif/inactif
    Route::patch('/{banque}/toggle-statut', [BanqueController::class, 'toggleStatut'])
        ->name('toggle-statut');

    // Dupliquer une banque
    Route::post('/{banque}/dupliquer', [BanqueController::class, 'dupliquer'])
        ->name('dupliquer');

    // Exporter les banques
    Route::get('/export/data', [BanqueController::class, 'export'])
        ->name('export');

    // =========================================================================
    // ROUTES CORBEILLE (TRASH)
    // =========================================================================

    // Afficher les banques supprimées
    Route::get('/corbeille/liste', [BanqueController::class, 'trashed'])
        ->name('trashed');

    // Restaurer une banque
    Route::post('/{id}/restore', [BanqueController::class, 'restore'])
        ->name('restore');

    // Supprimer définitivement une banque
    Route::delete('/{id}/force-delete', [BanqueController::class, 'forceDelete'])
        ->name('force-delete');

});

/*
|--------------------------------------------------------------------------
| Routes API pour les Banques (AJAX)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('api/{prestataireId}/banques')->name('api.banques.')->group(function () {

    // ✅ CORRECT - Plus besoin de /prestataire/{prestataireId}, tu l'as déjà dans le prefix
    Route::get('/', [BanqueController::class, 'getByPrestataire'])
        ->name('by-prestataire');

});
