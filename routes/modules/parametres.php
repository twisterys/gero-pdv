<?php

use App\Http\Controllers\DocumentsParametresController;
use App\Http\Controllers\MethodesPaiementController;
use App\Http\Controllers\ReferenceController;
use App\Http\Controllers\UniteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MagasinController;
Route::prefix('parametres')->group(function () {
    Route::view('/','parametres.liste')->name('parametres.liste');

    Route::group(['prefix' => 'references', 'controller' => ReferenceController::class], function () {
        Route::get('/', 'liste')->name('references.liste');
        Route::get('/{id}/modifier', 'modifier')->name('references.modifier');
        Route::put('/{id}', 'mettre_a_jour')->name('references.mettre_a_jour');
        Route::post('/modifier', 'modifier_global')->name('references.modifier.global');
    });
    Route::group(['prefix' => 'documents', 'controller' => DocumentsParametresController::class], function () {
        Route::get('/', 'modifier')->name('documents.modifier');
        Route::put('/{id}', 'mettre_a_jour')->name('documents.mettre_a_jour');

        Route::get('/voir/{id}','preview')->name('documents.preview');
        Route::get('load/{file?}','loadPicture')->name('documents.load');
        Route::get('template','templateSettings')->name('documents.template');
    });
    Route::group(['prefix' => 'modules', 'controller' => \App\Http\Controllers\ModuleController::class], function () {
        Route::get('/', 'modifier')->name('modules.modifier');
        Route::put('/', 'mettre_a_jour')->name('modules.mettre_a_jour');
    });
    Route::group(['prefix' => 'methodes_paiement', 'controller' => MethodesPaiementController::class], function () {
        Route::get('/', 'liste')->name('methodes_paiement.liste');
        Route::post('/', 'sauvegarder')->name('methodes_paiement.sauvegarder');
        Route::get('/{id}/modifier', 'modifier')->name('methodes_paiement.modifier');
        Route::put('/{id}', 'mettre_a_jour')->name('methodes_paiement.mettre_a_jour');
        Route::delete('/{id}', 'supprimer')->name('methodes_paiement.supprimer');
        Route::post('/modifier_active', 'modifier_active')->name('methodes_paiement.modifier_active');
    });

    Route::group(['prefix' => 'magasin', 'controller' => MagasinController::class], function () {
        Route::get('/', 'liste')->name('magasin.liste');
        Route::post('/', 'sauvegarder')->name('magasin.sauvegarder');
        Route::get('/{id}/modifier', 'modifier')->name('magasin.modifier');
        Route::put('/{id}', 'mettre_a_jour')->name('magasin.mettre_a_jour');
        Route::delete('/{id}', 'supprimer')->name('magasin.supprimer');
        Route::post('/modifier-active', 'modifier_active')->name('magasin.modifier_active');
        Route::post('/modifier-active', 'modifier_active')->name('magasin.modifier_active');
        Route::get('/magasin-select', 'magasin_select')->name('magasins.select');

    });
    Route::group(['prefix' => 'unites', 'controller' => UniteController::class], function () {
        Route::get('/', 'liste')->name('unites.liste');
        Route::post('/', 'sauvegarder')->name('unites.sauvegarder');
        Route::get('/{id}/modifier', 'modifier')->name('unites.modifier');
        Route::put('/{id}', 'mettre_a_jour')->name('unites.mettre_a_jour');
        Route::delete('/{id}', 'supprimer')->name('unites.supprimer');
        Route::post('/modifier-active', 'modifier_active')->name('unites.modifier_active');
        //    ajax
        Route::get('/unite-select', 'unite_select')->name('unites.select');
    });

    Route::group(['prefix' => 'balises','controller' => \App\Http\Controllers\TagController::class],function (){
        Route::get('/','liste')->name('balises.liste');
        Route::post('/', 'sauvegarder')->name('balises.sauvegarder');
        Route::get('/modifier/{id}', 'modifier')->name('balises.modifier');
        Route::put('/{id}', 'mettre_a_jour')->name('balises.mettre_a_jour');
        Route::delete('/{id}', 'supprimer')->name('balises.supprimer');

        // ajax
        Route::get('/balise-select', 'balise_select')->name('balises.select');
    });

    Route::group(['prefix'=>'categories','controller'=>\App\Http\Controllers\CategorieDepenseController::class],function(){
        Route::get('/','liste')->name('categories.liste');
        Route::post('/', 'sauvegarder')->name('categories.sauvegarder');
        Route::get('/{id}/modifier', 'modifier')->name('categories.modifier');
        Route::put('/{id}', 'mettre_a_jour')->name('categories.mettre_a_jour');
        Route::delete('/{id}', 'supprimer')->name('categories.supprimer');
        Route::post('/modifier-active', 'modifier_active')->name('categories.modifier_active');

        Route::get('/afficher-ajax/{id}', 'afficher_ajax')->name('categories.afficher_ajax');
        Route::get('/categorie-select', 'categorie_select')->name('categories.select');


    });

    Route::group(['prefix' => 'forme_juridique', 'controller' => \App\Http\Controllers\FormeJuridiqueController::class], function () {
        Route::get('/', 'liste')->name('formes_juridique.liste');
        Route::post('/', 'sauvegarder')->name('formes_juridique.sauvegarder');
        Route::get('/{id}/modifier', 'modifier')->name('formes_juridique.modifier');
        Route::put('/{id}', 'mettre_a_jour')->name('formes_juridique.mettre_a_jour');
        Route::delete('/{id}', 'supprimer')->name('formes_juridique.supprimer');
        Route::post('/modifier_active', 'modifier_active')->name('formes_juridique.modifier_active');
    });

    Route::group(['prefix' => 'operations', 'controller' => \App\Http\Controllers\OperationController::class], function () {
        Route::get('/', 'liste')->name('operations.liste');
        Route::post('/', 'sauvegarder')->name('operations.sauvegarder');
        Route::delete('/{operation}', 'supprimer')->name('operations.supprimer');
    });

    Route::group(['prefix' => 'fonctionnalites','controller' => \App\Http\Controllers\FonctionnaliteController::class],function (){
        Route::get('/','modifier')->name('fonctionnalites.modifier');
        Route::post('/','sauvegarder')->name('fonctionnalites.sauvegarder');
    });

    Route::group(['prefix' => 'compteurs','controller' => \App\Http\Controllers\CompteurController::class],function (){
        Route::get('/','modifier')->name('compteurs.modifier');
        Route::post('/','sauvegarder')->name('compteurs.sauvegarder');
    });

    Route::group(['prefix' => 'pos-settings','controller' => \App\Http\Controllers\PosSettingsController::class],function (){
        Route::get('/','modifier')->name('pos-settings.modifier');
        Route::post('/','sauvegarder')->name('pos-settings.sauvegarder');
    });

    Route::group(['prefix'=>'informations','controller' => \App\Http\Controllers\InformationEntrepriseController::class],function (){
        Route::get('/', 'modifier')->name('informations.modifier');
        Route::put('/', 'mettre_a_jour')->name('informations.mettre_a_jour');
    });

    Route::group(['prefix' => 'produit-settings','controller' => \App\Http\Controllers\ProduitSettingsController::class],function (){
        Route::get('/','modifier')->name('produits-settings.modifier');
        Route::post('/','sauvegarder')->name('produits-settings.sauvegarder');

    });

    Route::group(['prefix' => 'methodes-livraison','controller' => \App\Http\Controllers\MethodeLivraisonController::class],function (){
        Route::get('/','liste')->name('methodes-livraison.liste');
        Route::post('/','sauvegarder')->name('methodes-livraison.sauvegarder');
        Route::get('/editer/{id}','modifier')->name('methodes-livraison.modifier');
        Route::put('/{id}','mettre_a_jour')->name('methodes-livraison.mettre_a_jour');
        Route::get('/livraison-select', 'livraison_select')->name('livraison.select');

    });
    Route::group(['prefix' => 'tableau-de-bord','controller' => \App\Http\Controllers\TableauBordController::class],function (){
        Route::get('/','modifier')->name('tableau_bord.modifier');
        Route::put('/','mettre_a_jour')->name('tableau_bord.mettre_a_jour');
    });

    Route::group(['prefix' => 'smtp-settings','controller' => \App\Http\Controllers\SMTPSettingController::class],function (){
        Route::get('/','modifier')->name('smtpSettings.modifier');
        Route::post('/','mettre_a_jour')->name('smtpSettings.mettre_a_jour');
    });

    Route::group(['prefix' => 'abonnements-settings','controller' => \App\Http\Controllers\AbonnementSettingsController::class],function (){
        Route::get('/','modifier')->name('abonnementsSettings.modifier');
        Route::post('/','mettre_a_jour')->name('abonnementsSettings.mettre_a_jour');
    });

    Route::group(['prefix' => 'banques','controller' => \App\Http\Controllers\BanqueController::class],function (){
        Route::get('/','liste')->name('banques.liste');
        Route::get('/modifier/{id}','modifier')->name('banques.modifier');
        Route::put('/mettre_a_jour/{id}','mettre_a_jour')->name('banques.mettre_a_jour');
        Route::post('/','sauvegarder')->name('banques.sauvegarder');
        Route::delete('/{id}','supprimer')->name('banques.supprimer');
    });

    Route::group(['prefix' => 'woocommerce','controller' => \App\Http\Controllers\WoocommerceController::class],function (){
        Route::get('/','parametres')->name('woocommerce.parametres');
        Route::post('/','mettre_a_jour')->name('woocommerce.mettre_a_jour');
        Route::post('test-connection','testConnection')->name('woocommerce.testConnection');
    });


    Route::group(['prefix' => 'relance','controller' => \App\Http\Controllers\RelanceController::class],function (){
        Route::get('/','liste')->name('relance.liste');
        Route::get('/ajouter','ajouter')->name('relance.ajouter');
        Route::post('/ajouter','sauvegarder')->name('relance.sauvegarder');
        Route::get('/modifier/{id}','modifier')->name('relance.modifier');
        Route::put('/modifier{id}','mettre_a_jour')->name('relance.mettre_a_jour');
        Route::delete('/{id}', 'supprimer')->name('relance.supprimer');
        Route::post('/modifier-active/{id}', 'modifier_active')->name('relance.modifier_active');

    });
});


