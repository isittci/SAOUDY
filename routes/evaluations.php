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
    Route::get('/', [EvaluationController::class, 'index'])->name('index');

    // ==================== ROUTES LIÉES À UNE ATTRIBUTION ====================

    // Évaluations pour une attribution spécifique
    Route::get('/attribution/{attribution}', [EvaluationController::class, 'pourAttribution'])->name('pour-attribution');

    // Formulaire de création pour une attribution
    Route::get('/attribution/{attribution}/create', [EvaluationController::class, 'create'])->name('create');

    // Enregistrer une nouvelle évaluation pour une attribution
    Route::post('/attribution/{attribution}', [EvaluationController::class, 'store'])->name('store');

    // ==================== ROUTES CRUD ÉVALUATION ====================

    // Détails d'une évaluation
    Route::get('/{evaluation}', [EvaluationController::class, 'show'])->name('show');

    // Formulaire de modification
    Route::get('/{evaluation}/edit', [EvaluationController::class, 'edit'])->name('edit');

    // Mettre à jour une évaluation
    Route::put('/{evaluation}', [EvaluationController::class, 'update'])->name('update');

    // Supprimer une évaluation
    Route::delete('/{evaluation}', [EvaluationController::class, 'destroy'])->name('destroy');

    // ==================== ROUTES D'ACTIONS ====================

    // Démarrer une évaluation
    Route::post('/{evaluation}/demarrer', [EvaluationController::class, 'demarrer'])->name('demarrer');

    // Terminer une évaluation
    Route::post('/{evaluation}/terminer', [EvaluationController::class, 'terminer'])->name('terminer');

    // Valider une évaluation
    Route::post('/{evaluation}/valider', [EvaluationController::class, 'valider'])->name('valider');

    // Rejeter une évaluation
    Route::post('/{evaluation}/rejeter', [EvaluationController::class, 'rejeter'])->name('rejeter');

    // Reprendre une évaluation rejetée
    Route::post('/{evaluation}/reprendre', [EvaluationController::class, 'reprendre'])->name('reprendre');

    // ==================== ROUTES VERSIONING ====================

    // Créer une nouvelle version
    Route::post('/{evaluation}/creer-version', [EvaluationController::class, 'creerVersion'])->name('creer-version');

    // ==================== ROUTES RAPPORTS ====================

    // Classement des évaluations pour un lot
    Route::get('/lot/{lot}/classement', [EvaluationController::class, 'classementLot'])->name('classement-lot');

    // Générer un rapport PDF
    Route::get('/{evaluation}/rapport', [EvaluationController::class, 'genererRapport'])->name('rapport');
});

/*
|--------------------------------------------------------------------------
| Routes API (si nécessaire)
|--------------------------------------------------------------------------
*/

Route::prefix('api/evaluations')->name('api.evaluations.')->middleware(['auth:sanctum'])->group(function () {

    Route::get('/', [EvaluationController::class, 'index'])->name('index');

    Route::get('/{evaluation}', [EvaluationController::class, 'show'])->name('show');

    Route::post('/attribution/{attribution}', [EvaluationController::class, 'store'])->name('store');

    Route::put('/{evaluation}', [EvaluationController::class, 'update'])->name('update');

    Route::post('/{evaluation}/demarrer', [EvaluationController::class, 'demarrer'])->name('demarrer');

    Route::post('/{evaluation}/terminer', [EvaluationController::class, 'terminer'])->name('terminer');

    Route::post('/{evaluation}/valider', [EvaluationController::class, 'valider'])->name('valider');

    Route::post('/{evaluation}/rejeter', [EvaluationController::class, 'rejeter'])->name('rejeter');
});
