<?php

use App\Http\Controllers\RebutController;

// Dans web.php
Route::group(['prefix' => 'rebuts'], function () {
    Route::get('/', [RebutController::class, 'liste'])->name('rebuts.liste');
    Route::get('/ajouter', [RebutController::class, 'ajouter'])->name('rebuts.ajouter');
    Route::post('/sauvegarder', [RebutController::class, 'sauvegarder'])->name('rebuts.sauvegarder');
    Route::get('/{id}', [RebutController::class, 'afficher'])->name('rebuts.afficher');
    Route::get('/rollback/{id}', [RebutController::class, 'rollback'])->name('rebuts.rollback');
});
