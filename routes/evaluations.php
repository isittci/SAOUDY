<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EvaluationController;

/*
|--------------------------------------------------------------------------
| Routes du module Évaluation
|--------------------------------------------------------------------------
|
| Ces routes gèrent toutes les fonctionnalités liées aux évaluations
| des prestataires pour les lots attribués.
|
*/

Route::prefix('evaluations')->name('evaluations.')->middleware(['auth'])->group(function () {

    // ==================== ROUTES PRINCIPALES ====================

    // Liste de toutes les évaluations
    Route::get('/', [EvaluationController::class, 'index'])->name('index')->middleware('can:evaluations_attributions.read');

    // ==================== ROUTES LIÉES À UNE ATTRIBUTION ====================

    // Évaluations pour une attribution spécifique
    Route::get('/attribution/{attribution}', [EvaluationController::class, 'pourAttribution'])->name('pour-attribution')->middleware('can:evaluations_attributions.read');

    // Formulaire de création pour une attribution
    Route::get('/attribution/{attribution}/create', [EvaluationController::class, 'create'])->name('create')->middleware('can:evaluations_attributions.create');

    // Enregistrer une nouvelle évaluation pour une attribution
    Route::post('/attribution/{attribution}', [EvaluationController::class, 'store'])->name('store')->middleware('can:evaluations_attributions.create');

    // ==================== ROUTES CRUD ÉVALUATION ====================

    // Détails d'une évaluation
    Route::get('/{evaluation}', [EvaluationController::class, 'show'])->name('show')->middleware('can:evaluations_attributions.view-details');

    // Formulaire de modification
    Route::get('/{evaluation}/edit', [EvaluationController::class, 'edit'])->name('edit')->middleware('can:evaluations_attributions.evaluate');

    // Mettre à jour une évaluation
    Route::put('/{evaluation}', [EvaluationController::class, 'update'])->name('update')->middleware('can:evaluations_attributions.evaluate');

    // Supprimer une évaluation
    Route::delete('/{evaluation}', [EvaluationController::class, 'destroy'])->name('destroy')->middleware('can:evaluations_attributions.delete');

    // ==================== ROUTES D'ACTIONS ====================

    // Démarrer une évaluation
    Route::post('/{evaluation}/demarrer', [EvaluationController::class, 'demarrer'])->name('demarrer')->middleware('can:evaluations_attributions.update');

    // Terminer une évaluation
    Route::post('/{evaluation}/terminer', [EvaluationController::class, 'terminer'])->name('terminer')->middleware('can:evaluations_attributions.evaluate');

    // Valider une évaluation
    Route::post('/{evaluation}/valider', [EvaluationController::class, 'valider'])->name('valider')->middleware('can:evaluations_attributions.validate');

    // Rejeter une évaluation
    Route::post('/{evaluation}/rejeter', [EvaluationController::class, 'rejeter'])->name('rejeter')->middleware('can:evaluations_attributions.reject');

    // Reprendre une évaluation rejetée
    Route::post('/{evaluation}/reprendre', [EvaluationController::class, 'reprendre'])->name('reprendre')->middleware('can:evaluations_attributions.evaluate');

    // ==================== ROUTES VERSIONING ====================

    // Créer une nouvelle version
    Route::post('/{evaluation}/creer-version', [EvaluationController::class, 'creerVersion'])->name('creer-version')->middleware('can:evaluations_attributions.evaluate');

    // ==================== ROUTES RAPPORTS ====================

    // Classement des évaluations pour un lot
    Route::get('/lot/{lot}/classement', [EvaluationController::class, 'classementLot'])->name('classement-lot')->middleware('can:evaluations_attributions.read');

    // Générer un rapport PDF
    Route::get('/{evaluation}/rapport', [EvaluationController::class, 'genererRapport'])->name('rapport')->middleware('can:evaluations_attributions.read');
});

/*
|--------------------------------------------------------------------------
| Routes API (si nécessaire)
|--------------------------------------------------------------------------
*/

Route::prefix('api/evaluations')->name('api.evaluations.')->middleware(['auth:sanctum'])->group(function () {

    Route::get('/', [EvaluationController::class, 'index'])->name('index')->middleware('can:evaluations_attributions.read');

    Route::get('/{evaluation}', [EvaluationController::class, 'show'])->name('show')->middleware('can:evaluations_attributions.view-details');

    Route::post('/attribution/{attribution}', [EvaluationController::class, 'store'])->name('store')->middleware('can:evaluations_attributions.evaluate');

    Route::put('/{evaluation}', [EvaluationController::class, 'update'])->name('update')->middleware('can:evaluations_attributions.evaluate');

    Route::post('/{evaluation}/demarrer', [EvaluationController::class, 'demarrer'])->name('demarrer')->middleware('can:evaluations_attributions.evaluate');

    Route::post('/{evaluation}/terminer', [EvaluationController::class, 'terminer'])->name('terminer')->middleware('can:evaluations_attributions.validate');

    Route::post('/{evaluation}/valider', [EvaluationController::class, 'valider'])->name('valider')->middleware('can:evaluations_attributions.validate');

    Route::post('/{evaluation}/rejeter', [EvaluationController::class, 'rejeter'])->name('rejeter')->middleware('can:evaluations_attributions.reject');
});
