<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $changeDecimal = function (string $tableName, array $columns, int $precision, int $scale): void {
            if (!Schema::hasTable($tableName)) {
                return;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName, $columns, $precision, $scale) {
                foreach ($columns as $col) {
                    if (Schema::hasColumn($tableName, $col)) {
                        $table->decimal($col, $precision, $scale)->nullable()->change();
                    }
                }
            });
        };

        // Revert all decimals to 2 digits after decimal point
        $changeDecimal('achats', ['total_ht', 'total_ttc', 'total_tva', 'total_reduction', 'debit', 'credit'], 18, 2);
        $changeDecimal('achat_lignes', ['ht', 'quantite', 'reduction', 'total_ttc'], 18, 2);
        $changeDecimal('ventes', ['total_ht', 'total_ttc', 'total_tva', 'total_reduction', 'solde', 'encaisser'], 18, 2);
        $changeDecimal('vente_lignes', ['ht', 'quantite', 'reduction', 'total_ttc', 'revient'], 18, 2);
        $changeDecimal('paiements', ['encaisser', 'decaisser'], 18, 2);
        $changeDecimal('articles', ['prix_achat', 'prix_revient', 'prix_vente'], 18, 2);
        $changeDecimal('cheques', ['montant'], 18, 2);
        $changeDecimal('clients', ['limite_de_credit'], 18, 2);
        $changeDecimal('fournisseurs', ['limite_de_credit'], 18, 2);
        $changeDecimal('commercials', ['objectif'], 18, 2);
        $changeDecimal('depenses', ['montant', 'encaisser', 'solde'], 18, 2);
        $changeDecimal('renouvellements', ['montant'], 18, 2);
        $changeDecimal('stock_entrees', ['quantite'], 20, 2);
        $changeDecimal('transaction_stocks', ['qte_sortir', 'qte_entree', 'valeur_sortir', 'valeur_entrer'], 20, 2);
        $changeDecimal('transfert_caisse', ['montant'], 18, 2);
        $changeDecimal('transfert_lignes', ['qte'], 20, 2);
        $changeDecimal('transformation_lignes', ['quantite'], 20, 2);


    }

    public function down(): void
    {

    }
};
