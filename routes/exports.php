<?php

use App\Http\Controllers\RapportExportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes d'Export des Rapports
|--------------------------------------------------------------------------
|
| À ajouter dans routes/web.php :
| require __DIR__.'/exports.php';
|
| Ou copier le contenu dans votre fichier routes/web.php
|
*/

Route::middleware(['auth'])->prefix('exports')->name('exports.')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | RAPPORT 1: Lots en cours avec avancement
    |--------------------------------------------------------------------------
    */
    Route::get('/lots-en-cours/excel', [RapportExportController::class, 'lotsEnCoursExcel'])
        ->name('lots-en-cours.excel');

    Route::get('/lots-en-cours/pdf', [RapportExportController::class, 'lotsEnCoursPdf'])
        ->name('lots-en-cours.pdf');

    Route::get('/lots-en-cours/data', [RapportExportController::class, 'lotsEnCoursData'])
        ->name('lots-en-cours.data');

    /*
    |--------------------------------------------------------------------------
    | RAPPORT 2: Factures et paiements d'un prestataire
    |--------------------------------------------------------------------------
    */
    Route::get('/prestataires/{prestataire}/factures/excel', [RapportExportController::class, 'facturesPaiementsExcel'])
        ->name('prestataires.factures.excel');

    Route::get('/prestataires/{prestataire}/factures/pdf', [RapportExportController::class, 'facturesPaiementsPdf'])
        ->name('prestataires.factures.pdf');

    Route::get('/prestataires/{prestataire}/factures/data', [RapportExportController::class, 'facturesPaiementsData'])
        ->name('prestataires.factures.data');

    /*
    |--------------------------------------------------------------------------
    | RAPPORT 3: Fiche prestataire avec lots attribués (NOUVEAU)
    |--------------------------------------------------------------------------
    */
    Route::get('/prestataires/{prestataire}/fiche/excel', [RapportExportController::class, 'fichePrestataireExcel'])
        ->name('prestataires.fiche.excel');

    Route::get('/prestataires/{prestataire}/fiche/pdf', [RapportExportController::class, 'fichePrestatairePdf'])
        ->name('prestataires.fiche.pdf');

    Route::get('/prestataires/{prestataire}/fiche/data', [RapportExportController::class, 'fichePrestataireData'])
        ->name('prestataires.fiche.data');

    /*
    |--------------------------------------------------------------------------
    | RAPPORT 4: Fiche facture détaillée (NOUVEAU)
    |--------------------------------------------------------------------------
    */
    Route::get('/factures/{facture}/fiche/excel', [RapportExportController::class, 'ficheFactureExcel'])
        ->name('factures.fiche.excel');

    Route::get('/factures/{facture}/fiche/pdf', [RapportExportController::class, 'ficheFacturePdf'])
        ->name('factures.fiche.pdf');

    Route::get('/factures/{facture}/fiche/data', [RapportExportController::class, 'ficheFactureData'])
        ->name('factures.fiche.data');
});
