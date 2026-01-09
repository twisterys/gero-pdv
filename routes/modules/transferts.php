<?php

use App\Http\Controllers\TransfertController;

Route::group(['prefix' => 'transferts','controller' => TransfertController::class],function (){
    Route::redirect("/", "/liste");
    Route::get('/liste', 'liste')->name('transferts.liste');
    Route::get('/{id}/afficher', 'afficher')->name('transferts.afficher');
    Route::get('/{id}/afficher-demande', 'afficher_demande')->name('transferts.afficher.demande');
    Route::get('/ajouter', 'ajouter')->name('transferts.ajouter');
    Route::post('/sauvegarder', 'sauvegarder')->name('transferts.sauvegarder');
    Route::get('/article-modal','article_select_modal')->name('transferts.article_select_modal');
    Route::get('/afficher-demandes', 'afficher_demandes')->name('transferts.afficher.demandes');
    Route::post('/{id}/controle', 'controle')->name('transferts.controle');

});
