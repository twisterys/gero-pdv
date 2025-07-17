<?php


    Route::group(['prefix' => 'utilisateurs','controller' => \App\Http\Controllers\UserController::class], function (){
    Route::get('/','liste')->name('utilisateurs.liste');
    Route::get('ajouter','ajouter')->name('utilisateurs.ajouter');
    Route::post('sauvegarder','sauvegarder')->name('utilisateurs.sauvegarder');
    Route::put('mettre_a_jour/{id}','mettre_a_jour')->name('utilisateurs.mettre_jour');
    Route::get('modifier/{id}','modifier')->name('utilisateurs.modifier');
    Route::delete('supprimer/{id}','supprimer')->name('utilisateurs.supprimer');
    Route::get('/connexion/{id}','connexion')->name('utilisateurs.connexion');
    Route::get('/ma-licence','maLicence')->name('utilisateurs.ma_licence');
});
