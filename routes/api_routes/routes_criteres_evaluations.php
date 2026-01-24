<?php

use App\Http\Controllers\CritereEvaluationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes des Critères d'Évaluation
|--------------------------------------------------------------------------
|
| Ces routes gèrent les critères d'évaluation des lots d'appels d'offres.
| Structure hiérarchique : Appel d'Offres → Lot → Critère d'Évaluation
|
*/

Route::middleware(['auth:sanctum'])->prefix('appels-offres/{appel_offre}/lots/{lot}')->name('api.criteres-evaluations.')->group(function () {

    // Routes CRUD standards
    Route::get('criteres', [CritereEvaluationController::class, 'index'])->name('index')->middleware('can:criteres_evaluations.read');

    Route::get('criteres/create', [CritereEvaluationController::class, 'create'])->name('create')->middleware('can:criteres_evaluations.create');

    Route::post('criteres', [CritereEvaluationController::class, 'store'])->name('store')->middleware('can:criteres_evaluations.create');

    Route::get('criteres/{critere}', [CritereEvaluationController::class, 'show'])->name('show')->middleware('can:criteres_evaluations.view-details');

    Route::get('criteres/{critere}/edit', [CritereEvaluationController::class, 'edit'])->name('edit')->middleware('can:criteres_evaluations.update');

    Route::put('criteres/{critere}', [CritereEvaluationController::class, 'update'])->name('update')->middleware('can:criteres_evaluations.update');

    Route::delete('criteres/{critere}', [CritereEvaluationController::class, 'destroy'])->name('destroy')->middleware('can:criteres_evaluations.delete');

    // Routes d'actions supplémentaires
    Route::post('criteres/{critere}/activer', [CritereEvaluationController::class, 'activer'])->name('activer')->middleware('can:criteres_evaluations.update');

    Route::post('criteres/{critere}/desactiver', [CritereEvaluationController::class, 'desactiver'])->name('desactiver')->middleware('can:criteres_evaluations.update');

    Route::post('criteres/{critere}/reordonner', [CritereEvaluationController::class, 'reordonner'])->name('reordonner')->middleware('can:criteres_evaluations.update');

    Route::post('criteres/{critere}/dupliquer', [CritereEvaluationController::class, 'dupliquer'])->name('dupliquer')->middleware('can:criteres_evaluations.update');

    // Route de statistiques
    Route::get('criteres-statistiques', [CritereEvaluationController::class, 'statistiques'])->name('statistiques')->middleware('can:criteres_evaluations.view-details');


        // Mise à jour en masse des ordres (pour drag-and-drop optimisé)
    Route::post('criteres-reordonner-batch', [CritereEvaluationController::class, 'reordonnerBatch'])->name('reordonner-batch')->middleware('can:criteres_evaluations.update');

    // Permutation de deux critères (pour boutons monter/descendre)
    Route::post('criteres-permuter', [CritereEvaluationController::class, 'permuter'])->name('permuter')->middleware('can:criteres_evaluations.update');

    // Route de statistiques
    // Route::get('criteres-statistiques', [CritereEvaluationController::class, 'statistiques'])->name('statistiques')->middleware('can:criteres_evaluations.');
});
