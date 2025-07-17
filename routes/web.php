<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\CompteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


//Patches
require_once __DIR__.'/modules/patches.php';


//Auth
require_once __DIR__.'/modules/auth.php';




Route::group(['middleware'=>['auth','exercice']],function (){
    Route::get('/', [\App\Http\Controllers\TableauBordController::class,'liste'])->name('tableau_bord.liste');
// Contact
    require_once __DIR__.'/modules/contact.php';

// Tresorerie
    require_once __DIR__ . '/modules/tresorerie.php';
    require_once __DIR__ . '/modules/abonnements.php';

// Produits
    require_once __DIR__ . '/modules/produits.php';
// parametres
    require_once __DIR__ . '/modules/parametres.php';
// Vente
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
    //POS
    require_once __DIR__ . '/modules/pos.php';

//    utilisateurs
    require_once __DIR__ . '/modules/utilisateurs.php';
    require_once __DIR__ . '/modules/transferts.php';
    require_once __DIR__ . '/modules/authentication_logs.php';
    require_once __DIR__ . '/modules/promesse.php';
    // permissions
    require_once  __DIR__ .'/modules/permission.php';

    require_once  __DIR__ .'/modules/affaires.php';
    require_once  __DIR__ .'/modules/transformations.php';


});
