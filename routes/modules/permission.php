<?php
Route::group(['prefix' => 'permissions','controller' => \App\Http\Controllers\PermissionController::class],function (){
    Route::get('/liste','liste')->name('permissions.liste');
    Route::get('/ajouter','ajouter')->name('permissions.ajouter');
    Route::post('/','sauvegarder')->name('permissions.sauvegarder');
    Route::get('/{id}/modifier','modifier')->name('permissions.modifier');
    Route::put('/{id}/modifier','mettre_a_jour')->name('permissions.mettre_a_jour');
});
