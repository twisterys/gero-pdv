<?php

use App\Http\Controllers\AchatController;
use App\Http\Controllers\DepenseController;
use App\Http\Middleware\ActiveModuleMiddleware;

Route::group(['prefix' => 'achats','controller' => AchatController::class,'middleware' => ActiveModuleMiddleware::class],function (){

    Route::redirect("/", "/liste");

    // Use a wildcard to capture the type in the URL
    Route::get('/{type}/liste', 'liste')->name('achats.liste');
    Route::get('{type}/ajouter', 'ajouter')->name('achats.ajouter');
    Route::post('{type}/sauvegarder', 'sauvegarder')->name('achats.sauvegarder');
    Route::get('{type}/{id}', 'afficher')->name('achats.afficher');
    Route::get('{type}/editer/{id}', 'modifier')->name('achats.modifier');
    Route::put('{type}/{id}', 'mettre_a_jour')->name('achats.mettre_a_jour');
    Route::delete('{type}/supprimer/{id}', 'supprimer')->name('achats.supprimer');
    Route::get('{type}/telecharger/{id}', 'telecharger')->name('achats.telecharger');
    Route::get('{type}/piece-jointe/{id}', 'piece_jointe')->name('achats.piece_jointe');

    //AJAX
    Route::get('{type}/valider/{id}','validation_modal')->name('achats.validation_modal');
    Route::get('{type}/devalider/{id}','devalidation_modal')->name('achats.devalidation_modal');
    Route::put('{type}/valider/{id}','valider')->name('achats.valider');
    Route::put('{type}/devalider/{id}','devalider')->name('achats.devalider');
    Route::get('{type}/confrimer/{id}','confirmation_modal')->name('achats.confirmation_modal');
    Route::put('{type}/confirmer/{id}','confirmer')->name('achats.confirmer');
    Route::get('{type}/payer/{id}','paiement_modal')->name('achats.paiement_modal');
    Route::post('{type}/payer/{id}','payer')->name('achats.payer');
    Route::post('{type}/cloner/{id}','cloner')->name('achats.cloner');
    Route::get('{type}/cloner/{id}','clone_modal')->name('achats.clone_modal');
    Route::get('{type}/historique/{id}','history_modal')->name('achats.history_modal');
    Route::post('{type}/controle/{id}','controle')->name('achats.controle');
    Route::get('{type}/convertir/{id}','conversion_modal')->name('achats.conversion_modal');
    Route::post('{type}/convertir/{id}','convertir')->name('achats.convertir');


    Route::post('/{type}/attacher-piece-jointe/{id}','attacher_piece_jointe')->name('achats.attacher.piece_jointe');
    Route::delete('/{type}/supprimer-piece-jointe/{id}','supprimer_piece_jointe')->name('achats.supprimer.piece_jointe');

});

Route::group(['prefix' => 'depenses','controller' => DepenseController::class],function (){
    Route::redirect('/', '/liste');

    Route::get('/liste', 'liste')->name('depenses.liste');
    Route::get('ajouter', 'ajouter')->name('depenses.ajouter');
    Route::post('sauvegarder', 'sauvegarder')->name('depenses.sauvegarder');
    Route::get('afficher/{id}', 'afficher')->name('depenses.afficher');
    Route::get('editer/{id}', 'modifier')->name('depenses.modifier');
    Route::put('mettre-a-jour/{id}','mettre_a_jour')->name('depenses.mettre-a-jour');
    Route::delete('supprimer/{id}', 'supprimer')->name('depenses.supprimer');

    //ajax
    Route::get('payer/{id}', 'paiement_modal')->name('depenses.paiement_modal');
    Route::post('payer/{id}', 'payer')->name('depenses.payer');
    Route::post('controle/{id}', 'controle')->name('depenses.controle');
});
