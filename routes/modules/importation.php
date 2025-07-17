<?php

    Route::prefix('importation')->group(function () {
        Route::controller(\App\Http\Controllers\ImportController::class)->group(function () {
            //Importation
            Route::get('', 'liste')->name('importer-liste');
            Route::get('/afficher/{id}', 'afficher')->name('importations.afficher');
            //client
            Route::get('/clients', 'client_page')->name('importer-client-page');
            Route::post('/importer_client', 'importer_client')->name('importer-client');
//            //vente
            Route::get('/ventes', 'vente_page')->name('importer-vente-page');
            Route::post('/importer_vente', 'importer_vente')->name('importer-vente');

            //achats
            Route::get('/achats', 'achat_page')->name('importer-achat-page');
            Route::post('/importer_achat', 'importer_achat')->name('importer-achat');
            //Produit
            Route::get('/produits', 'produit_page')->name('importer-produit-page');
            Route::post('/importer_produit', 'importer_produit')->name('importer-produit');

            //Fournisseur
            Route::get('/fournisseurs', 'fournisseur_page')->name('importer-fournisseur-page');
            Route::post('/importer_fournisseur', 'importer_fournisseur')->name('importer-fournisseur');

            //Stock
            Route::get('/stocks', 'stock_page')->name('importer-stock-page');
            Route::post('/importer_stock', 'importer_stock')->name('importer-stock');

            //Paiements
            Route::get('/paiements', 'paiement_page')->name('importer-paiement-page');
            Route::post('/importer_paiement', 'importer_paiement')->name('importer-paiement');

            //telecharger
            Route::get('/load/{file}', 'load_import_file')->name('import.file.load');
        });
        Route::group(['prefix' => 'woocommerce','controller' => \App\Http\Controllers\WoocommerceImportController::class],function (){
           Route::get('/','liste')->name('woocommerce.import.liste');
           Route::post('/importer-produits','importProducts')->name('woocommerce.importer-produits');
           Route::post('/importer-ventes','importOrders')->name('woocommerce.importer-ventes');
        });
    });

