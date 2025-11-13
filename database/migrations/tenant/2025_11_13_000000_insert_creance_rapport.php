<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $exists = DB::table('rapports')->where('route', 'rapport-creances')->exists();

        $details = "Rapport des créances\n\n".
            "DESCRIPTION :\n".
            "Visualisez les créances clients : total, année courante et années précédentes. ".
            "Ce rapport aide à suivre le reste dû par client et à prioriser les relances.\n\n".
            "CONTENU :\n".
            "- Nom du client\n".
            "- Numéro de téléphone\n".
            "- Montant total des créances (reste dû global)\n".
            "- Créances de l’année courante (N)\n".
            "- Créances cumulées des années précédentes (N-x)\n\n".
            "FONCTIONNALITÉS :\n".
            "- Tri par défaut du plus grand au plus petit montant total\n".
            "- Filtres : client (nom), montant min/max (Total, N, N-x) avec critère obligatoire\n".
            "- Filtre 'Documents inclus' pour choisir les types de documents de vente (par défaut : facture (fa))\n\n".
            "INDICATEURS :\n".
            "- Total général des créances\n".
            "- Total des créances de l’année en cours (N)\n".
            "- Total des créances des années précédentes (N-x)\n\n".
            "IMPORTANT :\n".
            "Seuls les documents validés sont pris en compte (factures de vente 'fa' par défaut). ".
            "Les paiements partiels réduisent la créance au prorata des encaissements.\n".
            "Les montants affichés sont TTC.";

        if (! $exists) {
            DB::table('rapports')->insert([
                'nom' => 'Rapport des créances',
                'route' => 'rapport-creances',
                'description' => 'Suivi consolidé des créances clients (année courante et antérieures).',
                'type' => 'statistiques',
                'details' => $details,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table('rapports')
                ->where('route', 'rapport-creances')
                ->update([
                    'description' => 'Suivi consolidé des créances clients (année courante et antérieures).',
                    'details' => $details,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        DB::table('rapports')->where('route', 'rapport-creances')->delete();
    }
};
