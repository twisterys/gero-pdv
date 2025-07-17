<?php



Route::group(['prefix' => 'abonnements','controller' => \App\Http\Controllers\AbonnementController::class],function (){

    Route::redirect("/", "/liste");

    // Use a wildcard to capture the type in the URL
    Route::get('/liste', 'liste')->name('abonnements.liste');
    Route::get('/renew-modal/{id}', 'renew_modal')->name('abonnements.renew_modal');
    Route::post('/renouveler', 'renouveler')->name('abonnements.renouveler');
    Route::get('/ajouter', 'ajouter')->name('abonnements.ajouter');
    Route::get('/afficher/{id}', 'afficher')->name('abonnements.afficher');
    Route::post('/','sauvegarder')->name('abonnements.sauvegarder');
    Route::get('/{id}/modifier','modifier')->name('abonnements.modifier');
    Route::put('/{id}/modifier','mettre_a_jour')->name('abonnements.mettre_a_jour');
    Route::delete('/{id}/supprimer','supprimer')->name('abonnements.supprimer');
    Route::delete('/renouvellements/{id}','supprimer_renouvellement')->name("abonnements.supprimer_renouvellement");

    Route::put('/{id}/archiver', 'archiver')->name('abonnements.archiver');
    Route::get('/archives', 'archives')->name('abonnements.archives');

    Route::put('/{id}/desarchiver', 'desarchiver')->name('abonnements.desarchiver');




});


