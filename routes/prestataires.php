<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrestataireController;
use App\Http\Controllers\CapaciteTechniqueController;
use App\Http\Controllers\SituationFinanciereController;

/*
|--------------------------------------------------------------------------
| Routes Web
|--------------------------------------------------------------------------
*/

// Routes pour les Appels d'Offres
Route::middleware(['auth'])->prefix('prestataires')->name('prestataires.')->group(function () {
    Route::get('/', [PrestataireController::class, 'index'])->name('index')->middleware('can:prestataires.read');
    Route::get('/create', [PrestataireController::class, 'create'])->name('create')->middleware('can:prestataires.create');
    Route::post('/', [PrestataireController::class, 'store'])->name('store')->middleware('can:prestataires.create');
    Route::get('/{id}', [PrestataireController::class, 'show'])->name('show')->middleware('can:prestataires.view-details');
    Route::get('/{id}/edit', [PrestataireController::class, 'edit'])->name('edit')->middleware('can:prestataires.update');

    Route::put('/{id}', [PrestataireController::class, 'update'])->name('update')->middleware('can:prestataires.update');
    Route::delete('/{id}', [PrestataireController::class, 'destroy'])->name('destroy')->middleware('can:prestataires.delete');

    // Actions spécifiques
    Route::post('/{id}/toggle-status', [PrestataireController::class, 'toggleStatus'])->name('toggle-status')->middleware('can:prestataires.toggle-status');
    Route::get('/{id}/statistiques', [PrestataireController::class, 'statistiques'])->name('statistiques')->middleware('can:prestataires.view-details');
    Route::post('/{id}/duplicate', [PrestataireController::class, 'duplicate'])->name('duplicate')->middleware('can:prestataires.update');


});


/*
|--------------------------------------------------------------------------
| Routes API
|--------------------------------------------------------------------------
*/

Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {

    // API Appels d'Offres
    Route::prefix('prestataires')->name('api.prestataires.')->group(function () {
        Route::get('/', [PrestataireController::class, 'index'])->name('index')->middleware('can:prestataires.read');
        Route::get('/create', [PrestataireController::class, 'create'])->name('create')->middleware('can:prestataires.create');
        Route::post('/', [PrestataireController::class, 'store'])->name('store')->middleware('can:prestataires.create');
        Route::get('/{id}', [PrestataireController::class, 'show'])->name('show')->middleware('can:prestataires.view-details');
        Route::get('/{id}/edit', [PrestataireController::class, 'edit'])->name('edit')->middleware('can:prestataires.update');
        Route::put('/{id}', [PrestataireController::class, 'update'])->name('update')->middleware('can:prestataires.update');
        Route::delete('/{id}', [PrestataireController::class, 'destroy'])->name('destroy')->middleware('can:prestataires.delete');

        // Actions spécifiques
        Route::post('/{id}/toggle-status', [PrestataireController::class, 'toggleStatus'])->name('toggle-status')->middleware('can:prestataires.toggle-status');
        Route::get('/{id}/statistiques', [PrestataireController::class, 'statistiques'])->name('statistiques')->middleware('can:prestataires.view-details');
        Route::post('/{id}/duplicate', [PrestataireController::class, 'duplicate'])->name('duplicate')->middleware('can:prestataires.update');
    });
});



Route::prefix('prestataires/{prestataire}/capacites-techniques')
    ->name('prestataires.capacites-techniques.')
    ->middleware(['auth'])
    ->group(function () {

        // Liste des capacités techniques
        Route::get('/', [CapaciteTechniqueController::class, 'index'])->name('index')->middleware('can:capacites_techniques.read');

        // Formulaire de création
        Route::get('/create', [CapaciteTechniqueController::class, 'create'])->name('create')->middleware('can:capacites_techniques.manage');

        // Enregistrer une capacité technique
        Route::post('/', [CapaciteTechniqueController::class, 'store'])->name('store')->middleware('can:capacites_techniques.manage');

        // Afficher une capacité technique
        Route::get('/{capacite}', [CapaciteTechniqueController::class, 'show'])->name('show')->middleware('can:capacites_techniques.read');

        // Formulaire d'édition
        Route::get('/{capacite}/edit', [CapaciteTechniqueController::class, 'edit'])->name('edit')->middleware('can:capacites_techniques.manage');

        // Mettre à jour une capacité technique
        Route::put('/{capacite}', [CapaciteTechniqueController::class, 'update'])->name('update')->middleware('can:capacites_techniques.manage');

        // Supprimer une capacité technique
        Route::delete('/{capacite}', [CapaciteTechniqueController::class, 'destroy'])->name('destroy')->middleware('can:capacites_techniques.manage');
    });

/*
|--------------------------------------------------------------------------
| Routes Situations Financières (nested resource sous prestataires)
|--------------------------------------------------------------------------
*/
Route::prefix('prestataires/{prestataire}/situations-financieres')
    ->name('prestataires.situations-financieres.')
    ->middleware(['auth'])
    ->group(function () {

        // Liste des situations financières
        Route::get('/', [SituationFinanciereController::class, 'index'])->name('index')->middleware('can:situations_financieres.read');

        // Formulaire de création
        Route::get('/create', [SituationFinanciereController::class, 'create'])->name('create')->middleware('can:situations_financieres.manage');

        // Enregistrer une situation financière
        Route::post('/', [SituationFinanciereController::class, 'store'])->name('store')->middleware('can:situations_financieres.manage');

        // Afficher une situation financière
        Route::get('/{situation}', [SituationFinanciereController::class, 'show'])->name('show')->middleware('can:situations_financieres.read');

        // Formulaire d'édition
        Route::get('/{situation}/edit', [SituationFinanciereController::class, 'edit'])->name('edit')->middleware('can:situations_financieres.manage');

        // Mettre à jour une situation financière
        Route::put('/{situation}', [SituationFinanciereController::class, 'update'])->name('update')->middleware('can:situations_financieres.manage');

        // Supprimer une situation financière
        Route::delete('/{situation}', [SituationFinanciereController::class, 'destroy'])->name('destroy')->middleware('can:situations_financieres.manage');

        // API: Données d'évolution pour graphique
        Route::get('/api/evolution', [SituationFinanciereController::class, 'evolution'])->name('evolution')->middleware('can:situations_financieres.read');
    });

