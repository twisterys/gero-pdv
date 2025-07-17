<?php

use App\Http\Controllers\CompteController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\ReleveBancaireController;
use \App\Http\Controllers\TransfertCaisseController;
// Comptes
Route::prefix('/tresorerie/comptes')->group(function () {
    Route::redirect("/", "comptes/liste");
    Route::controller(CompteController::class)->group(function () {
        Route::get('', 'liste')->name('comptes.liste');
        Route::get('ajouter', 'ajouter')->name('comptes.ajouter');
        Route::post('/sauvegarder', 'sauvegarder')->name('comptes.sauvegarder');
        Route::get('/{id}', 'afficher')->name('comptes.afficher');
        Route::get('/{id}/modifier', 'modifier')->name('comptes.modifier');
        Route::put('/{id}', 'mettre_a_jour')->name('comptes.mettre_a_jour');
        Route::delete('/{id}', 'supprimer')->name('comptes.supprimer');
    });
});

// Paiements
Route::prefix('/tresorerie/paiements')->group(function () {
    Route::redirect("/", "/liste");
    Route::controller(PaiementController::class)->group(function () {
        Route::delete('{id}', 'supprimer')->name('paiement.supprimer');
        Route::get('/liste', 'liste')->name('paiement.liste');
        Route::get('/afficher/{id}', 'afficher')->name('paiement.afficher');
        Route::post('/operation', 'sauvegarder_operation')->name('paiement.sauvegarder_operation');
        Route::get('{id}/modifer', 'modifier')->name('paiement.modifier');
        Route::put('{id}', 'mettre_a_jour')->name('paiement.mettre_a_jour');
    });
});


// Paiements
Route::prefix('/tresorerie/transferts-caisse')->group(function () {
    Route::controller(TransfertCaisseController::class)->group(function () {
        Route::post('/sauvegarder', 'sauvegarder')->name('transferts_caisse.sauvegarder');
    });
});

Route::prefix('/tresorerie/releve-bancaire')->group(function () {
    Route::get('/liste', [ReleveBancaireController::class, 'liste'])->name('releve-bancaire.liste');
    Route::post('/sauvegarder', [ReleveBancaireController::class, 'sauvegarder'])->name('releve-bancaire.sauvegarder');
    Route::delete('/{id}', [ReleveBancaireController::class, 'supprimer'])->name('releve-bancaire.supprimer');
});
