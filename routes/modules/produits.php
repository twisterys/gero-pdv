<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\FamilleController;
use App\Http\Controllers\TaxeController;
use App\Http\Controllers\UniteController;

Route::group(['prefix' => 'articles', 'controller' => ArticleController::class], function () {
    Route::get('/', 'liste')->name('articles.liste');
    Route::get('/{id}/afficher', 'afficher')->name('articles.afficher');
    Route::get('/ajouter', 'ajouter')->name('articles.ajouter');
    Route::post('/', 'sauvegarder')->name('articles.sauvegarder');
    Route::get('/{id}/modifier', 'modifier')->name('articles.modifier');
    Route::put('/{id}', 'mettre_a_jour')->name('articles.mettre_a_jour');
    Route::delete('/{id}', 'supprimer')->name('articles.supprimer');
    Route::get('/selectionner-par-reference', 'selectionner_par_reference')->name('articles.selectionner_par_reference');
    Route::post('/modal_recherche/{type}', 'modal_recherche')->name('articles.modal_recherche');
    Route::get('/article-modal/{type}/{magasin_id?}', 'article_select_modal')->name('articles.article_select_modal');
    Route::get('load/{file?}', 'load_article_image')->name('article.image.load');
    Route::post('historique-prix/', 'historique_prix_modal')->name('article.historique_prix_modal');
    Route::get('/article-select', 'article_select')->name('article.select');
    Route::get('/afficher-ajax/{id}', 'afficher_ajax')->name('article.afficher_ajax');
    Route::get('/imprimer-code-barre/{code}', [ArticleController::class, 'imprimerCodeBarre'])->name('articles.imprimer_code_barre');

});
Route::group(['prefix' => 'familles', 'controller' => FamilleController::class], function () {
    Route::get('/', 'liste')->name('familles.liste');
    Route::post('/', 'sauvegarder')->name('familles.sauvegarder');
    Route::get('/{id}/modifier', 'modifier')->name('familles.modifier');
    Route::put('/{id}', 'mettre_a_jour')->name('familles.mettre_a_jour');
    Route::delete('/{id}', 'supprimer')->name('familles.supprimer');
    //    ajax
    Route::get('/famille-select', 'famille_select')->name('familles.select');
});
Route::group(['prefix' => 'taxes', 'controller' => TaxeController::class], function () {
    Route::get('/', 'liste')->name('taxes.liste');
    Route::post('/', 'sauvegarder')->name('taxes.sauvegarder');
    Route::get('/{valeur}/modifier', 'modifier')->name('taxes.modifier');
    Route::put('/{valeur}', 'mettre_a_jour')->name('taxes.mettre_a_jour');
    Route::delete('/{valeur}', 'supprimer')->name('taxes.supprimer');
    Route::post('/modifier-active', 'modifier_active')->name('taxes.modifier_active');
    //    ajax
    Route::get('/taxe-select', 'taxe_select')->name('taxes.select');
});

Route::group(['prefix' => 'marques','controller' => \App\Http\Controllers\MarqueController::class],function (){
   Route::get('/','liste')->name('marques.liste');
   Route::post('/','sauvegarder')->name('marques.sauvegarder');
   Route::get('/{id}/modifier','modifier')->name('marques.modifier');
   Route::put('/{id}','mettre_a_jour')->name('marques.mettre_a_jour');
   Route::delete('/{id}','supprimer')->name('marques.supprimer');
});
