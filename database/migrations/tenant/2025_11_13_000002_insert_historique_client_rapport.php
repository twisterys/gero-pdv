<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!DB::table('rapports')->where('route', 'historique-client')->exists()) {
            DB::table('rapports')->insert([
                'nom' => 'Rapport Historique Client',
                'route' => 'historique-client',
                'description' => 'Historique annuel du chiffre d\'affaires, encaissements, prix de revient et crédit par client.',
                'type' => 'statistiques',
                'details' => "Rapport Historique Client\n\nAffiche pour un client: CA annuel, total encaissements, total prix de revient (si activé) et crédit de l'année (non cumulé).\n\nColonnes :\n- Année\n- Chiffre d'affaires (CA)\n- Encaissements\n- Prix de revient (optionnel)\n- Crédit de l'année (solde des ventes de l'année)\n\nFiltres :\n- Sélection du client obligatoire\n- Documents inclus (types de documents de vente à considérer, par défaut : fa)\n\nTri par année décroissante par défaut.\n\nIMPORTANT :\nSeuls les documents validés sont pris en compte (par défaut factures de vente 'fa').\nLes montants affichés sont TTC.",
            ]);
        }
    }

    public function down(): void
    {
        DB::table('rapports')->where('route', 'historique-client')->delete();
    }
};
