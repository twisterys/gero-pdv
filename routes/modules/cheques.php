<?php
Route::group(['prefix' => 'cheques'], function () {
    Route::get('encaisser', 'App\Http\Controllers\ChequeController@encaisser_liste')->name('cheques.encaisser_liste');
    Route::get('decaisser', 'App\Http\Controllers\ChequeController@decaisser_liste')->name('cheques.decaisser_liste');
    Route::post('sauvegarder/{type}', 'App\Http\Controllers\ChequeController@sauvegarder')->name('cheques.sauvegarder');
    Route::put("mettre_a_jour/{id}", 'App\Http\Controllers\ChequeController@mettre_a_jour')->name('cheques.mettre_a_jour');
    Route::get('encaisser/{id}', 'App\Http\Controllers\ChequeController@modifier')->name('cheques.modifier');

    Route::post('encaisser/{id}', 'App\Http\Controllers\ChequeController@encaisser')->name('cheques.encaisser');
    Route::post('decaisser/{id}', 'App\Http\Controllers\ChequeController@decaisser')->name('cheques.decaisser');
    Route::post('annuler/{id}', 'App\Http\Controllers\ChequeController@annuler')->name('cheques.annuler');
});
