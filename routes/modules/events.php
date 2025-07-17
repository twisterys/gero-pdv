<?php
Route::group(['prefix' => 'evenements','controller' => \App\Http\Controllers\EventController::class],function (){
    Route::post('/','sauvegarder')->name('events.sauvegarder');
    Route::get('/','liste')->name('events.liste');
    Route::get('/modifier/{id}','modifier')->name('events.modifier');
    Route::put('/{id}','mettre_a_jour')->name('events.mettre_a_jour');
    Route::delete('/{id}','supprimer')->name('events.supprimer');
    Route::get('/afficher/{id}','afficher')->name('events.afficher');
});
