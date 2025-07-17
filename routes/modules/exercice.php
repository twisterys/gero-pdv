<?php

use App\Http\Controllers\ExerciceController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'exercice','controller'=> ExerciceController::class],function (){
    Route::get('/','changer')->name('exercice.changer');
    Route::get('/ajouter','ajouter')->name('exercice.ajouter');
    Route::post('/','sauvegarder')->name('exercice.sauvegarder');
    Route::put('/','mettre_en_place')->name('exercice.mettre_en_place');
});
