<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\CommercialController;
use App\Http\Controllers\FournisseurController;

Route::prefix('clients')->group(function () {
    Route::redirect("/", "clients/liste");
    Route::controller(ClientController::class)->group(function () {
        Route::get('/liste', 'liste')->name('clients.liste');
        Route::get('ajouter', 'ajouter')->name('clients.ajouter');
        Route::post('/sauvegarder', 'sauvegarder')->name('clients.sauvegarder');
        Route::get('/afficher/{id}', 'afficher')->name('clients.afficher');
        Route::get('/modifier/{id}', 'modifier')->name('clients.modifier');
        Route::put('/{id}', 'mettre_a_jour')->name('clients.mettre_a_jour');
        Route::delete('/supprimer/{id}', 'supprimer')->name('clients.supprimer');

        // AJAX ROUTES
        Route::get('/afficher-ajax/{id}', 'afficher_ajax')->name('clients.afficher_ajax');
        Route::get('/client-select', 'client_select')->name('clients.select');
    });
});

Route::prefix('commercials')->group(function () {
    Route::redirect("/", "commercials/liste");
    Route::controller(CommercialController::class)->group(function () {
        Route::get('/liste', 'liste')->name('commercials.liste');
        Route::get('/ajouter', 'ajouter')->name('commercials.ajouter');
        Route::post('/sauvegarder', 'sauvegarder')->name('commercials.sauvegarder');
        Route::get('/{id}', 'afficher')->name('commercials.afficher');
        Route::get('/modifier/{id}', 'modifier')->name('commercials.modifier');
        Route::put('/{id}', 'mettre_a_jour')->name('commercials.mettre_a_jour');
        Route::delete('/supprimer/{id}', 'supprimer')->name('commercials.supprimer');

        // AJAX ROUTES
        Route::get('/afficher-ajax/{id}', 'afficher_ajax')->name('commercials.afficher_ajax');
        Route::get('load/{file?}', 'load_article_image')->name('commercials.image.load');

    });
});
Route::get('/commercials-select', [CommercialController::class, 'commercial_select'])->name('commercials.select');



Route::prefix('fournisseurs')->group(function () {
    Route::redirect("/", "fournisseurs/liste");
    Route::controller(FournisseurController::class)->group(function () {
        Route::get('/liste', 'liste')->name('fournisseurs.liste');
        Route::get('ajouter', 'ajouter')->name('fournisseurs.ajouter');
        Route::get('/afficher/{id}', 'afficher')->name('fournisseurs.afficher');
        Route::post('/sauvegarder', 'sauvegarder')->name('fournisseurs.sauvegarder');
        Route::get('/modifier/{id}', 'modifier')->name('fournisseurs.modifier');
        Route::put('/{id}', 'mettre_a_jour')->name('fournisseurs.mettre_a_jour');
        Route::delete('/supprimer/{id}', 'supprimer')->name('fournisseurs.supprimer');

        // AJAX ROUTES
        Route::get('/afficher-ajax/{id}', 'afficher_ajax')->name('fournisseurs.afficher_ajax');
        Route::get('/fournisseur-select', 'fournisseur_select')->name('fournisseurs.select');
    });
});
