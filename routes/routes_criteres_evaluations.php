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
    Route::get('criteres', [CritereEvaluationController::class, 'index'])
        ->name('criteres-evaluations.index');

    Route::get('criteres/create', [CritereEvaluationController::class, 'create'])
        ->name('criteres-evaluations.create');

    Route::post('criteres', [CritereEvaluationController::class, 'store'])
        ->name('criteres-evaluations.store');

    Route::get('criteres/{critere}', [CritereEvaluationController::class, 'show'])
        ->name('criteres-evaluations.show');

    Route::get('criteres/{critere}/edit', [CritereEvaluationController::class, 'edit'])
        ->name('criteres-evaluations.edit');

    Route::put('criteres/{critere}', [CritereEvaluationController::class, 'update'])
        ->name('criteres-evaluations.update');

    Route::delete('criteres/{critere}', [CritereEvaluationController::class, 'destroy'])
        ->name('criteres-evaluations.destroy');

    // Routes d'actions supplémentaires
    Route::post('criteres/{critere}/activer', [CritereEvaluationController::class, 'activer'])
        ->name('criteres-evaluations.activer');

    Route::post('criteres/{critere}/desactiver', [CritereEvaluationController::class, 'desactiver'])
        ->name('criteres-evaluations.desactiver');

    Route::post('criteres/{critere}/reordonner', [CritereEvaluationController::class, 'reordonner'])
        ->name('criteres-evaluations.reordonner');

    Route::post('criteres/{critere}/dupliquer', [CritereEvaluationController::class, 'dupliquer'])
        ->name('criteres-evaluations.dupliquer');

    // Route de statistiques
    Route::get('criteres-statistiques', [CritereEvaluationController::class, 'statistiques'])
        ->name('criteres-evaluations.statistiques');


        // Mise à jour en masse des ordres (pour drag-and-drop optimisé)
    Route::post('criteres-reordonner-batch', [CritereEvaluationController::class, 'reordonnerBatch'])
        ->name('criteres-evaluations.reordonner-batch');

    // Permutation de deux critères (pour boutons monter/descendre)
    Route::post('criteres-permuter', [CritereEvaluationController::class, 'permuter'])
        ->name('criteres-evaluations.permuter');

    // Route de statistiques
    Route::get('criteres-statistiques', [CritereEvaluationController::class, 'statistiques'])
        ->name('criteres-evaluations.statistiques');
});

/*
|--------------------------------------------------------------------------
| Exemples d'URLs générées
|--------------------------------------------------------------------------
|
| GET    /appels-offres/{uuid}/lots/{uuid}/criteres
| GET    /appels-offres/{uuid}/lots/{uuid}/criteres/create
| POST   /appels-offres/{uuid}/lots/{uuid}/criteres
| GET    /appels-offres/{uuid}/lots/{uuid}/criteres/{uuid}
| GET    /appels-offres/{uuid}/lots/{uuid}/criteres/{uuid}/edit
| PUT    /appels-offres/{uuid}/lots/{uuid}/criteres/{uuid}
| DELETE /appels-offres/{uuid}/lots/{uuid}/criteres/{uuid}
| POST   /appels-offres/{uuid}/lots/{uuid}/criteres/{uuid}/activer
| POST   /appels-offres/{uuid}/lots/{uuid}/criteres/{uuid}/desactiver
| POST   /appels-offres/{uuid}/lots/{uuid}/criteres/{uuid}/reordonner
| POST   /appels-offres/{uuid}/lots/{uuid}/criteres/{uuid}/dupliquer
| GET    /appels-offres/{uuid}/lots/{uuid}/criteres-statistiques
|
*/
