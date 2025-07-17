<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;


Route::get('se-connecter',[LoginController::class,'se_connecter'])->name('auth.se-connecter');
Route::post('authentifier',[LoginController::class,'authentifier'])->name('auth.authentifier');
Route::post('se-deconnecter',[LoginController::class,'se_deconnecter'])->name('auth.se-deconnecter')->middleware(['auth']);
Route::get('modifier-mot-de-passe',[LoginController::class,'modifier_mot_de_passe'])->name('auth.modifier');
Route::post('mettre_a_jour_mot_de_passe',[LoginController::class,'sauvegarder'])->name('auth.sauvegarder')->middleware(['auth']);
Route::post('sauvegarder_email',[LoginController::class,'sauvegarder_email'])->name('auth.sauvegarder_email');

Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
