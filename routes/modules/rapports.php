<?php
use App\Http\Controllers\RapportController;
use App\Http\Controllers\RapportJournalierController;

Route::prefix('rapports')->group(function () {
    Route::controller(RapportController::class)->group(function () {
        Route::get('/','liste')->name('rapports.liste');
        //Mouvement de stock
        Route::get('/mouvement_stock', 'mouvement_stock')->name('rapports.mouvement-stock');
        Route::get('/achat-vente', 'achat_vente')->name('rapports.achat_vente');
        Route::get('/vente-produit', 'vente_produit')->name('rapports.vente-produit');
        Route::get('/achat-produit', 'achat_produit')->name('rapports.achat-produit');
        Route::get('/ca-client', 'ca_client')->name('rapports.ca-client');
        Route::get('/tendance-produit', 'tendance_produit')->name('rapports.tendance-produit');
        Route::get('/stock-produit', 'stock_produit')->name('rapports.stock-produit');
        Route::get('/stock-produit-legal', 'stock_produit_legal')->name('rapports.stock-produit-legal');
        Route::get('/tva', 'tva')->name('rapports.tva');
        Route::get('/annuel', 'annuel')->name('rapports.annuel');
        Route::get('/stock-produit-magasin', 'stock_produit_par_magasin')->name('rapports.stock-produit-magasin');
        Route::get('/commerciaux', 'commerciaux')->name('rapports.commerciaux');
        Route::get('/sessions', 'sessions')->name('rapports.sessions');
        Route::get('/sessions/ventes/{id}', 'afficher_session')->name('rapports.sessions.ventes');
        Route::get('/categorie-depense','categorie_depense')->name('rapports.categorie-depense');


        Route::get('/journalier', [RapportJournalierController::class, 'index'])->name('rapports.journalier');
        Route::post('/journalier', [RapportJournalierController::class, 'filtrer'])->name('rapports.journalier.filtrer');
        Route::get('/journalier/plain', [RapportJournalierController::class, 'printPlain'])->name('rapports.journalier.plain');

        // Optionnel: endpoints AJAX par section
        Route::get('/journalier/ac', [RapportJournalierController::class, 'ac'])->name('rapports.journalier.ac');
        Route::get('/journalier/af', [RapportJournalierController::class, 'af'])->name('rapports.journalier.af');
        Route::get('/journalier/cr', [RapportJournalierController::class, 'cr'])->name('rapports.journalier.cr');
        Route::get('/journalier/tr', [RapportJournalierController::class, 'tr'])->name('rapports.journalier.tr');
    });


});
