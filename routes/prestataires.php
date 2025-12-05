<?php


use App\Http\Controllers\PrestataireController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes Web
|--------------------------------------------------------------------------
*/

// Routes pour les Appels d'Offres
Route::middleware(['auth'])->prefix('prestataires')->name('prestataires.')->group(function () {
    Route::get('/', [PrestataireController::class, 'index'])->name('index');
    Route::get('/create', [PrestataireController::class, 'create'])->name('create');
    Route::post('/', [PrestataireController::class, 'store'])->name('store');
    Route::get('/{id}', [PrestataireController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [PrestataireController::class, 'edit'])->name('edit');
    Route::put('/{id}', [PrestataireController::class, 'update'])->name('update');
    Route::delete('/{id}', [PrestataireController::class, 'destroy'])->name('destroy');

    // Actions spécifiques
    Route::post('/{id}/toggle-status', [PrestataireController::class, 'toggleStatus'])->name('toggle-status');
    Route::get('/{id}/statistiques', [PrestataireController::class, 'statistiques'])->name('statistiques');
    Route::post('/{id}/duplicate', [PrestataireController::class, 'duplicate'])->name('duplicate');
});

/*
|--------------------------------------------------------------------------
| Routes API
|--------------------------------------------------------------------------
*/

Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {

    // API Appels d'Offres
    Route::prefix('prestataires')->name('api.prestataires.')->group(function () {
        Route::get('/', [PrestataireController::class, 'index'])->name('index');
        Route::get('/create', [PrestataireController::class, 'create'])->name('create');
        Route::post('/', [PrestataireController::class, 'store'])->name('store');
        Route::get('/{id}', [PrestataireController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PrestataireController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PrestataireController::class, 'update'])->name('update');
        Route::delete('/{id}', [PrestataireController::class, 'destroy'])->name('destroy');

        // Actions spécifiques
        Route::post('/{id}/toggle-status', [PrestataireController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/{id}/statistiques', [PrestataireController::class, 'statistiques'])->name('statistiques');
        Route::post('/{id}/duplicate', [PrestataireController::class, 'duplicate'])->name('duplicate');
    });
});
