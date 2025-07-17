<?php

use App\Http\Controllers\CompteController;
use App\Http\Controllers\PaiementController;


// Paiements
Route::prefix('/promesse')->group(function () {
    Route::controller(\App\Http\Controllers\PromesseController::class)->group(function () {
        Route::delete('{id}', 'supprimer')->name('promesse.supprimer');
        Route::post('respecter/{id}','respecter')->name('promesse.respecter');
        Route::post('rompre/{id}','rompre')->name('promesse.rompre');
    });
});
