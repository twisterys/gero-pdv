<?php

Route::prefix('Exportation')->group(function () {
    Route::controller(\App\Http\Controllers\ExportController::class)->group(function () {
        //Exportation
        Route::get('', 'liste')->name('exporter-liste');
        Route::get('/clients', 'exporter_client')->name('exporter-client');
        Route::get('/fournisseurs', 'exporter_fournisseur')->name('exporter-fournisseur');
        Route::get('/produits', 'exporter_produit')->name('exporter-produit');

        Route::post('/stocks', 'exporter_stock')->name('exporter-stock');
        Route::get('/stocks/liste', 'exporter_stock_page')->name('exporter-stock-page');

        Route::post('/ventes', 'exporter_vente')->name('exporter-vente');
        Route::get('/ventes', 'exporter_vente_page')->name('exporter-vente-page');

        Route::post('/achats', 'exporter_achat')->name('exporter-achat');
        Route::get('/achats', 'exporter_achat_page')->name('exporter-achat-page');

        Route::post('/paiements', 'exporter_paiement')->name('exporter-paiement');
        Route::get('/paiements', 'exporter_paiement_page')->name('exporter-paiement-page');


    });
});

