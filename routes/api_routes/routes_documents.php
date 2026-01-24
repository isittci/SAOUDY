<?php

/*
|--------------------------------------------------------------------------
| Routes pour la gestion des documents des lots
|--------------------------------------------------------------------------
|
| À ajouter dans routes/web.php
|
*/

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;

// Routes pour les documents des lots (nested resource)
Route::middleware(['auth:sanctum'])->prefix('lots/{lotId}/documents')->name('api.lots.documents.')->group(function () {

    // Liste des documents
    Route::get('/', [DocumentController::class, 'index'])->name('index')->middleware('can:documents_lots.read');

    // Formulaire de création
    Route::get('/create', [DocumentController::class, 'create'])->name('create')->middleware('can:documents_lots.create');

    // Enregistrer un document
    Route::post('/', [DocumentController::class, 'store'])->name('store')->middleware('can:documents_lots.create');

    // Upload multiple
    Route::post('/upload-multiple', [DocumentController::class, 'uploadMultiple'])->name('uploadMultiple')->middleware('can:documents_lots.update');

    // Afficher un document
    Route::get('/{document}', [DocumentController::class, 'show'])->name('show')->middleware('can:documents_lots.read');

    // Formulaire d'édition
    Route::get('/{document}/edit', [DocumentController::class, 'edit'])->name('edit')->middleware('can:documents_lots.update');

    // Mettre à jour un document
    Route::put('/{document}', [DocumentController::class, 'update'])->name('update')->middleware('can:documents_lots.update');

    // Supprimer un document
    Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('destroy')->middleware('can:documents_lots.delete');

    // Télécharger un document
    Route::get('/{document}/download', [DocumentController::class, 'download'])->name('download')->middleware('can:documents_lots.download');

    // Prévisualiser un document
    Route::get('/{document}/preview', [DocumentController::class, 'preview'])->name('preview')->middleware('can:documents_lots.read');

    // Valider un document
    Route::patch('/{document}/valider', [DocumentController::class, 'valider'])->name('valider')->middleware('can:documents_lots.toggle-status');

    // Invalider un document
    Route::patch('/{document}/invalider', [DocumentController::class, 'invalider'])->name('invalider')->middleware('can:documents_lots.toggle-status');
});
