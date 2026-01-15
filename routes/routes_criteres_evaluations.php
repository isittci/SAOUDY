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

Route::middleware(['auth'])->prefix('appels-offres/{appel_offre}/lots/{lot}')->group(function () {

    // Routes CRUD standards
    Route::get('criteres', [CritereEvaluationController::class, 'index'])->name('criteres-evaluations.index')->middleware('can:criteres_evaluations.read');

    Route::get('criteres/create', [CritereEvaluationController::class, 'create'])->name('criteres-evaluations.create')->middleware('can:criteres_evaluations.create');

    Route::post('criteres', [CritereEvaluationController::class, 'store'])->name('criteres-evaluations.store')->middleware('can:criteres_evaluations.create');

    Route::get('criteres/{critere}', [CritereEvaluationController::class, 'show'])->name('criteres-evaluations.show')->middleware('can:criteres_evaluations.view-details');

    Route::get('criteres/{critere}/edit', [CritereEvaluationController::class, 'edit'])->name('criteres-evaluations.edit')->middleware('can:criteres_evaluations.update');

    Route::put('criteres/{critere}', [CritereEvaluationController::class, 'update'])->name('criteres-evaluations.update')->middleware('can:criteres_evaluations.update');

    Route::delete('criteres/{critere}', [CritereEvaluationController::class, 'destroy'])->name('criteres-evaluations.destroy')->middleware('can:criteres_evaluations.delete');

    // Routes d'actions supplémentaires
    Route::post('criteres/{critere}/activer', [CritereEvaluationController::class, 'activer'])->name('criteres-evaluations.activer')->middleware('can:criteres_evaluations.update');

    Route::post('criteres/{critere}/desactiver', [CritereEvaluationController::class, 'desactiver'])->name('criteres-evaluations.desactiver')->middleware('can:criteres_evaluations.update');

    Route::post('criteres/{critere}/reordonner', [CritereEvaluationController::class, 'reordonner'])->name('criteres-evaluations.reordonner')->middleware('can:criteres_evaluations.update');

    Route::post('criteres/{critere}/dupliquer', [CritereEvaluationController::class, 'dupliquer'])->name('criteres-evaluations.dupliquer')->middleware('can:criteres_evaluations.update');

    // Route de statistiques
    Route::get('criteres-statistiques', [CritereEvaluationController::class, 'statistiques'])->name('criteres-evaluations.statistiques')->middleware('can:criteres_evaluations.view-details');


        // Mise à jour en masse des ordres (pour drag-and-drop optimisé)
    Route::post('criteres-reordonner-batch', [CritereEvaluationController::class, 'reordonnerBatch'])->name('criteres-evaluations.reordonner-batch')->middleware('can:criteres_evaluations.update');

    // Permutation de deux critères (pour boutons monter/descendre)
    Route::post('criteres-permuter', [CritereEvaluationController::class, 'permuter'])->name('criteres-evaluations.permuter')->middleware('can:criteres_evaluations.update');

    // Route de statistiques
    // Route::get('criteres-statistiques', [CritereEvaluationController::class, 'statistiques'])->name('criteres-evaluations.statistiques')->middleware('can:criteres_evaluations.');
});
