<?php

declare(strict_types=1);

use App\Models\Abonnement;
use App\Models\Achat;
use App\Models\Cheque;
use App\Models\Client;
use App\Models\Commercial;
use App\Models\Depense;
use App\Models\Paiement;
use App\Models\ReleveBancaire;
use App\Models\Vente;
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
    Route::get('/reset-somelaar', function () {
        try {
            DB::transaction(function () {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');

                // Define tables to truncate in logical order (child tables first)
                $tables = [
                    'ventes_relations',
                    \App\Models\VenteLigne::class,
                    \App\Models\AchatLigne::class,
                    Vente::class,
                    Achat::class,
                    Paiement::class,
                    ReleveBancaire::class,
                    Client::class,
                    Commercial::class,
                    Depense::class,
                    Cheque::class,
                    Abonnement::class,
                ];

                foreach ($tables as $table) {
                    if (class_exists($table)) {
                        $table::truncate();
                    } else {
                        DB::table($table)->truncate();
                    }
                }

                // Reset counters
                DB::table('compteurs')
                    ->whereNotIn('type', ['fr', 'art'])
                    ->update(['compteur' => 1]);

                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            });

            // Log the reset operation for audit purposes
            Log::info('Database reset operation completed successfully');

            return response()->json([
                'success' => true,
                'message' => 'Reset successful!',
                'timestamp' => now()->toDateTimeString()
            ]);

        } catch (\Throwable $e) {
            Log::error('Reset failed: '.$e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Reset failed: '.$e->getMessage(),
                'timestamp' => now()->toDateTimeString()
            ], 500);
        }
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

        require_once  __DIR__ .'/modules/rebuts.php';

    });
});
