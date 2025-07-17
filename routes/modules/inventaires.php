<?php

Route::prefix('stock')->group(function () {
    Route::controller(\App\Http\Controllers\StockController::class)->group(function () {
        //Importation
        Route::get('', 'liste')->name('inventaire-liste');
        Route::get('/ajouter', 'ajouter')->name('inventaire.ajouter');
        Route::get('/ajouter_manuellement', 'ajouter_manuellement')->name('inventaire.ajouter_manuellement');
        Route::post('/inventaire_importer_stocks_manuellement', 'inventaire_importer_stocks_manuellement')->name('inventaire-importer-stocks-manuellement');
        Route::get('/afficher/{id}', 'afficher')->name('inventaire.afficher');
        Route::get('/rollback/{id}', 'rollback')->name('inventaire.rollback');
        Route::post('/inventaire_exporter_stocks', 'inventaire_exporter_stocks')->name('inventaire-exporter-stocks');
        Route::post('/inventaire_importer_stocks', 'inventaire_importer_stocks')->name('inventaire-importer-stocks');
        Route::get('/load/{file}', 'loadFile')->name('inventaire.load');
    });
});

