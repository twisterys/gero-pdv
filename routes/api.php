<?php

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



Route::group([
    'prefix' => 'v-management',
    'namespace' => 'App\Http\Controllers\Api\management',
    'middleware' => 'auth.static_token'
], function () {
    Route::prefix('health')->group(function () {
        Route::get('/version', 'HealthController@version_info');
        Route::get('/up', 'HealthController@up');
        Route::get('/instance-status/{id}', 'HealthController@instance_status');
    });
    Route::prefix('backup')->group(function () {
        Route::post('/', 'BackUpController@backup');
    });
    Route::prefix('tenants')->group(function () {
        Route::post('', 'TenantController@creer');
        Route::delete('/{tenantId}', 'TenantController@supprimer');
        Route::put('/{tenantId}/subdomain', 'TenantController@modifier_sous_domaine');
        Route::put('/{tenantId}/expiration-date', 'TenantController@modifier_date_expiration');
    });
    Route::prefix('users')->group(function () {
        Route::get("/{tenantId}", 'UserController@liste');
        Route::get("/{tenantId}/{userId}", 'UserController@afficher');
        Route::post('/{tenantId}/activate', 'UserController@activer');
        Route::post('/{tenantId}/deactivate', 'UserController@desactiver');
        Route::post('/{tenantId}/reset-password', 'UserController@modifier_mot_de_passe');
    });
    Route::prefix('limites')->group(function () {
        Route::get("/{tenantId}", 'LimiteController@liste');
        Route::put("/{tenantId}", 'LimiteController@sauvegarder');
    });
});



Route::group(['middleware' => ['auth:sanctum', InitializeTenancyByDomain::class, PreventAccessFromCentralDomains::class,\App\Http\Middleware\ApiSessionMiddleware::class,]], function () {
    Route::post('/sync-commandes', [\App\Http\Controllers\Api\sync\GeroSyncController::class, 'handleSyncCommandes']);
    Route::group(['prefix' => 'v-parfums', 'namespace' => 'App\Http\Controllers\Api\parfums'], function () {
        Route::get('articles',  'ArticleController@recherche_par_reference');
        Route::get('articles/{id}', 'ArticleController@afficher');
        Route::get('articles-liste', 'ArticleController@recherche_liste');
        Route::post('articles-clients-rapport','RapportController@article_client_rapport');
        Route::post('articles-fournisseurs-rapport','RapportController@article_fournisseur_rapport');
        Route::post('creance-rapport','RapportController@creance_rapport');
        Route::post('tresorie-rapport','RapportController@tresorie_rapport');


        Route::get('clients-liste',  'ClientController@recherche_liste');

        Route::post('ventes', 'VenteController@sauvegarder');
        Route::get('ventes/ticket/{id}', 'VenteController@ticket');
        Route::post('ventes-paiement', 'VenteController@sauvegarder_avec_paiement');
        Route::post('ventes-ajouter-paiement', 'VenteController@ajouter_paiement');
        Route::get('history', 'VenteController@history');

        Route::post('clients', 'ClientController@sauvegarder');
        Route::delete('pos-session/terminer', 'PosController@terminer');
        Route::get('pos-session/cloture', 'PosController@cloture');

        Route::post('demande-transfert','DemandeTransfertController@sauvegarder');
        Route::get('demande-transfert','DemandeTransfertController@liste');
        Route::get('mes-demandes','DemandeTransfertController@mes_demandes');
        Route::get('demandes-externe','DemandeTransfertController@demandes_externe');
        Route::get('demandes-externe-print/{id}','DemandeTransfertController@printDemande');
        Route::get('demande-transfert/{id}','DemandeTransfertController@afficher');
        Route::post('demande-transfert/{id}/refuser','DemandeTransfertController@refuser');
        Route::post('demande-transfert/{id}/livrer','DemandeTransfertController@livrer');
        Route::post('demande-transfert/{id}/accepter','DemandeTransfertController@accepter');
        Route::post('demande-transfert/{id}/annuler','DemandeTransfertController@annuler');

        Route::post('depense','DepenseController@sauvegarder');
        Route::get('depense','DepenseController@liste');
        Route::post('articles-stock-rapport','RapportController@stock');
    });

    Route::group(['prefix' => 'v-commercial', 'namespace' => 'App\Http\Controllers\Api\commercial'], function () {
        Route::get('articles',  'ArticleController@recherche_par_reference');
        Route::get('articles/{id}', 'ArticleController@afficher');
        Route::get('articles-liste', 'ArticleController@recherche_liste');
        Route::post('articles-clients-rapport','RapportController@article_client_rapport');
        Route::post('articles-fournisseurs-rapport','RapportController@article_fournisseur_rapport');
        Route::post('creance-rapport','RapportController@creance_rapport');
        Route::post('tresorie-rapport','RapportController@tresorie_rapport');

        Route::get('clients-liste',  'ClientController@recherche_liste');

        Route::post('ventes', 'VenteController@sauvegarder');
        Route::get('history', 'VenteController@history');
        Route::post('ventes-paiement', 'VenteController@sauvegarder_avec_paiement');
        Route::post('ventes-ajouter-paiement', 'VenteController@ajouter_paiement');


        Route::post('clients', 'ClientController@sauvegarder');
        Route::delete('pos-session/terminer', 'PosController@terminer');

        Route::get('commercials', 'CommercialController@index');
        Route::get('methodes-livraison', 'MethodeLivraisonController@index');
        Route::post('articles-stock-rapport','RapportController@stock');
    });

    Route::group(['prefix' => 'v-classic', 'namespace' => 'App\Http\Controllers\Api\classic'], function () {
        Route::get('/',function (){
             echo 'Classic';
        });
        Route::get('articles',  'ArticleController@recherche_par_reference');
        Route::get('articles/{id}', 'ArticleController@afficher');
        Route::get('articles-liste', 'ArticleController@recherche_liste');
        Route::get('articles-all', 'ArticleController@liste');
        Route::get('articles-clients-rapport','RapportController@article_client_rapport');
        Route::get('articles-fournisseurs-rapport','RapportController@article_fournisseur_rapport');
        Route::get('creance-rapport','RapportController@creance_rapport');
        Route::get('tresorie-rapport','RapportController@tresorie_rapport');

        Route::get('clients-liste',  'ClientController@recherche_liste');

        Route::post('ventes', 'VenteController@sauvegarder_vente');
        Route::get('ventes/ticket/{id}', 'VenteController@ticket');
        Route::post('ventes-paiement', 'VenteController@sauvegarder_avec_paiement');
        Route::post('ventes-ajouter-paiement', 'VenteController@ajouter_paiement');
        Route::get('history', 'VenteController@history');

        Route::post('clients', 'ClientController@sauvegarder');
        Route::delete('pos-session/terminer', 'PosController@terminer');
        Route::get('pos-session/cloture', 'PosController@cloture');

        Route::post('demande-transfert','DemandeTransfertController@sauvegarder');
        Route::get('demande-transfert','DemandeTransfertController@liste');
        Route::get('mes-demandes','DemandeTransfertController@mes_demandes');
        Route::get('demandes-externe','DemandeTransfertController@demandes_externe');
        Route::get('demandes-externe-print/{id}','DemandeTransfertController@printDemande');
        Route::get('demande-transfert/{id}','DemandeTransfertController@afficher');
        Route::post('demande-transfert/{id}/refuser','DemandeTransfertController@refuser');
        Route::post('demande-transfert/{id}/livrer','DemandeTransfertController@livrer');
        Route::post('demande-transfert/{id}/accepter','DemandeTransfertController@accepter');
        Route::post('demande-transfert/{id}/annuler','DemandeTransfertController@annuler');

        Route::post('depense','DepenseController@sauvegarder');
        Route::get('depense','DepenseController@liste');

        Route::get('familles','ArticleController@familles');
        Route::get('marques','ArticleController@marques');
        Route::get('articles-stock-rapport','RapportController@stock');
        Route::get('comptes','CompteController@liste');
        Route::get('/methodes-paiement','MethodePaiementController@liste');
        Route::get('/depense-categories','DepenseCategoryController@liste');
        Route::get('/magasins','MagasinController@liste');
    });

    Route::group(['prefix' => 'v-caisse', 'namespace' => 'App\Http\Controllers\Api\caisse'], function () {
        Route::get('articles',  'ArticleController@recherche_par_reference');
        Route::get('articles/{id}', 'ArticleController@afficher');
        Route::get('articles-liste', 'ArticleController@recherche_liste');
        Route::get('articles-all', 'ArticleController@liste');
        Route::get('clients-liste',  'ClientController@recherche_liste');
        Route::get('articles-liste-type', 'ArticleController@recherche_liste_type');
        Route::post('articles-clients-rapport','RapportController@article_client_rapport');
        Route::post('articles-fournisseurs-rapport','RapportController@article_fournisseur_rapport');
        Route::post('creance-rapport','RapportController@creance_rapport');
        Route::post('tresorie-rapport','RapportController@tresorie_rapport');


        Route::post('ventes', 'VenteController@sauvegarder');
        Route::get('ventes/ticket/{id}', 'VenteController@ticket');
        Route::post('ventes-paiement', 'VenteController@sauvegarder_avec_paiement');
        Route::post('ventes-ajouter-paiement', 'VenteController@ajouter_paiement');
        Route::get('history', 'VenteController@history');

        Route::post('clients', 'ClientController@sauvegarder');
        Route::delete('pos-session/terminer', 'PosController@terminer');
        Route::get('pos-session/cloture', 'PosController@cloture');
//
//        Route::post('demande-transfert','DemandeTransfertController@sauvegarder');
//        Route::get('demande-transfert','DemandeTransfertController@liste');
//        Route::get('mes-demandes','DemandeTransfertController@mes_demandes');
//        Route::get('demandes-externe','DemandeTransfertController@demandes_externe');
//        Route::get('demandes-externe-print/{id}','DemandeTransfertController@printDemande');
//        Route::get('demande-transfert/{id}','DemandeTransfertController@afficher');
//        Route::post('demande-transfert/{id}/refuser','DemandeTransfertController@refuser');
//        Route::post('demande-transfert/{id}/livrer','DemandeTransfertController@livrer');
//        Route::post('demande-transfert/{id}/accepter','DemandeTransfertController@accepter');
//        Route::post('demande-transfert/{id}/annuler','DemandeTransfertController@annuler');

        Route::post('depense','DepenseController@sauvegarder');
        Route::get('depense','DepenseController@liste');

        Route::get('familles','ArticleController@familles');
        Route::get('marques','ArticleController@marques');
        Route::post('articles-stock-rapport','RapportController@stock');

    });
});
