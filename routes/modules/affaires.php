<?php
Route::group(['prefix' => 'affaire','controller' => \App\Http\Controllers\AffaireController::class],function (){
    Route::get('/liste','liste')->name('affaire.liste');
    Route::get('/ajouter','ajouter')->name('affaire.ajouter');
    Route::get('/afficher/{id}','afficher')->name('affaire.afficher');
    Route::post('/','sauvegarder')->name('affaire.sauvegarder');
    Route::get('/{id}/modifier','modifier')->name('affaire.modifier');
    Route::put('/{id}/modifier','mettre_a_jour')->name('affaire.mettre_a_jour');
    Route::delete('/{id}/supprimer','supprimer')->name('affaire.supprimer');
    Route::get('/{id}/{type}/vente','ajouter_vente')->name('affaire.ajouter.vente');

    Route::post('/{id}/attacher','attacher')->name('affaire.attacher');
    Route::get('/{id}/attacher-recherche','attachement_modal_search')->name('affaire.attacher.recherche');

    Route::get('/jalon/{id}','jalon_modal')->name('affaire.jalon_modal');

});
