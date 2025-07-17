<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\CheckTenantForMaintenanceMode;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    CheckTenantForMaintenanceMode::class
])->group(function () {
    Route::get('/faker',function (){
        \App\Models\Article::factory(100)->create();
        \App\Models\Fournisseur::factory(10)->create();
        \App\Models\Achat::factory(100)->withLignes()->create();
    });
//    Route::get('/', function () {
//        return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
//    });
//Auth
    require_once __DIR__.'/modules/auth.php';
    require_once __DIR__.'/modules/patchs.php';

    Route::group(['middleware'=>['auth','exercice','vendeur']],function (){
        Route::get('/', [\App\Http\Controllers\TableauBordController::class,'liste'])->name('tableau_bord.liste');

//        Route::get('pos',[\App\Http\Controllers\PosController::class,'pos']);

        //POS
        require_once __DIR__ . '/modules/pos.php';

        require_once __DIR__ . '/modules/abonnements.php';

        require_once __DIR__ . '/modules/transferts.php';
// Contact
        require_once __DIR__.'/modules/contact.php';

// Tresorerie
        require_once __DIR__ . '/modules/tresorerie.php';

// Produits
        require_once __DIR__ . '/modules/produits.php';

// parametres
        require_once __DIR__ . '/modules/parametres.php';


// Vent
        require_once __DIR__ . '/modules/ventes.php';
// Achat
        require_once __DIR__ . '/modules/achats.php';


// Importation/Exportation
        require_once __DIR__ . '/modules/importation.php';
        require_once __DIR__ . '/modules/exportation.php';
// Rapports
        require_once __DIR__ . '/modules/rapports.php';
        require_once __DIR__ . '/modules/inventaires.php';


// exercice
        require_once __DIR__ . '/modules/exercice.php';

//    utilisateurs
        require_once __DIR__ . '/modules/utilisateurs.php';
        require_once __DIR__ . '/modules/authentication_logs.php';

        // permissions
        require_once  __DIR__ .'/modules/permission.php';

        require_once __DIR__ . '/modules/promesse.php';
        // Affaires
        require_once  __DIR__ .'/modules/affaires.php';
        require_once  __DIR__ .'/modules/events.php';

        require_once  __DIR__ .'/modules/cheques.php';

        require_once  __DIR__ .'/modules/transformations.php';
    });
});
