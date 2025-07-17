<?php

use App\Http\Controllers\VenteController;
use App\Http\Middleware\ActiveModuleMiddleware;

$types = ['dv', 'fa', 'av', 'bl', 'bca', 'br', 'be', 'bs'];
Route::group(['prefix' => 'ventes','controller' => VenteController::class,'middleware' => ActiveModuleMiddleware::class],function (){
    Route::redirect("/", "/liste");

    // Use a wildcard to capture the type in the URL
    Route::get('/{type}/liste', 'liste')->name('ventes.liste');
    Route::get('{type}/ajouter', 'ajouter')->name('ventes.ajouter');
    Route::post('{type}/sauvegarder', 'sauvegarder')->name('ventes.sauvegarder');
    Route::get('{type}/afficher/{id}', 'afficher')->name('ventes.afficher');
    Route::get('{type}/editer/{id}', 'modifier')->name('ventes.modifier');
    Route::put('{type}/mettre-a-jour/{id}', 'mettre_a_jour')->name('ventes.mettre_a_jour');
    Route::delete('{type}/supprimer/{id}', 'supprimer')->name('ventes.supprimer');
    Route::get('{type}/telecharger/{id}', 'telecharger')->name('ventes.telecharger');
    Route::get('{type}/piece-jointe/{id}', 'piece_jointe')->name('ventes.piece_jointe');
    Route::post('{type}/solde/{id}','solde')->name('ventes.solde');
    Route::get('{type}/relancer/{id}', 'relancer_modal')->name('ventes.relancer_modal');
    Route::get('{type}/relancer_edit/{id}', 'edit_template_relancer_modal')->name('ventes.edit_template_relancer_modal');
    Route::put('{type}/relancer/{id}', 'relancer')->name('ventes.relancer');



    //AJAX
    Route::get('{type}/valider/{id}','validation_modal')->name('ventes.validation_modal');
    Route::get('{type}/devalider/{id}','devalidation_modal')->name('ventes.devalidation_modal');
    Route::put('{type}/valider/{id}','valider')->name('ventes.valider');
    Route::put('{type}/devalider/{id}','devalider')->name('ventes.devalider');
    Route::get('{type}/confrimer/{id}','confirmation_modal')->name('ventes.confirmation_modal');
    Route::put('{type}/confirmer/{id}','confirmer')->name('ventes.confirmer');
    Route::get('{type}/payer/{id}','paiement_modal')->name('ventes.paiement_modal');
    Route::get('{type}/promesse/{id}','promesse_modal')->name('ventes.promesse_modal');
    Route::post('{type}/payer/{id}','payer')->name('ventes.payer');
    Route::post('{type}/promesse/{id}','promesse')->name('ventes.promesse');
    Route::get('{type}/convertir/{id}','conversion_modal')->name('ventes.conversion_modal');
    Route::get('{type}/changer-statut/{id}','statut_com_modal')->name('ventes.statut_com_modal');
    Route::post('{type}/changer-statut/{id}','changer_status')->name('ventes.changer_status');
    Route::get('{type}/cloner/{id}','clone_modal')->name('ventes.clone_modal');
    Route::get('{type}/historique/{id}','history_modal')->name('ventes.history_modal');
    Route::post('{type}/convertir/{id}','convertir')->name('ventes.convertir');
    Route::post('{type}/cloner/{id}','cloner')->name('ventes.cloner');
    Route::post('{type}/convertir-multi','convertir_multi_modal')->name('ventes.convertir_multi_modal');
    Route::put('{type}/convertir-multi','convertir_multi')->name('ventes.convertir_multi');
    Route::get('{type}/solde-select','solde_select')->name('ventes.solde_select');
    Route::post('{type}/controle/{id}','controle')->name('ventes.controle');
    Route::post('/{type}/attacher-piece-jointe/{id}','attacher_piece_jointe')->name('ventes.attacher.piece_jointe');
    Route::delete('/{type}/supprimer-piece-jointe/{id}','supprimer_piece_jointe')->name('ventes.supprimer.piece_jointe');

});
