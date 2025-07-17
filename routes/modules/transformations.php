<?php

Route::group(['prefix' => 'transformations','controller' => \App\Http\Controllers\TransformationController::class], function (){
    Route::get('/','liste')->name('transformations.liste');
    Route::get('/ajouter','ajouter')->name('transformations.ajouter');
    Route::post('/','sauvegarder')->name('transformations.sauvegarder');
    Route::get('/modifier/{id}','modifier')->name('transformations.modifier');
    Route::delete('/{id}','supprimer')->name('transformations.supprimer');
    Route::put('/{id}','mettre_a_jour')->name('transformations.mettre_a_jour');
    Route::get('/afficher/{id}','afficher')->name('transformations.afficher');
    Route::put('/{id}/annuler','annuler')->name('transformations.annuler');

});
