<?php

Route::get('point-de-vente', [\App\Http\Controllers\PosController::class, 'pos'])->name('pos');
Route::get('point-de-vente/demandes', [\App\Http\Controllers\PosController::class, 'demandes'])->name('pos.demandes');
Route::get('session-point-de-vente', [\App\Http\Controllers\PosSessionContoller::class, 'ajouter'])->name('pos.ajouter');
Route::post('session-point-de-vente', [\App\Http\Controllers\PosSessionContoller::class, 'commencer'])->name('pos.commencer');
