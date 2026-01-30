<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Revert all decimals to 3 digits after decimal point
        $this->changeDecimal('achats', ['total_ht', 'total_ttc', 'total_tva', 'total_reduction', 'debit', 'credit'], 18, 3);
        $this->changeDecimal('achat_lignes', ['ht', 'quantite', 'reduction', 'total_ttc'], 18, 3);
        $this->changeDecimal('ventes', ['total_ht', 'total_ttc', 'total_tva', 'total_reduction', 'solde', 'encaisser'], 18, 3);
        $this->changeDecimal('vente_lignes', ['ht', 'quantite', 'reduction', 'total_ttc', 'revient'], 18, 3);
        $this->changeDecimal('paiements', ['encaisser', 'decaisser'], 18, 3);
        $this->changeDecimal('articles', ['prix_achat', 'prix_revient', 'prix_vente'], 18, 3);
        $this->changeDecimal('cheques', ['montant'], 18, 3);
        $this->changeDecimal('clients', ['limite_de_credit'], 18, 3);
        $this->changeDecimal('fournisseurs', ['limite_de_credit'], 18, 3);
        $this->changeDecimal('commercials', ['objectif'], 18, 3);
        $this->changeDecimal('depenses', ['montant', 'encaisser', 'solde'], 18, 3);
        $this->changeDecimal('renouvellements', ['montant'], 18, 3);
        $this->changeDecimal('stock_entrees', ['quantite'], 20, 3);
        $this->changeDecimal('transaction_stocks', ['qte_sortir', 'qte_entree', 'valeur_sortir', 'valeur_entrer'], 20, 3);
        $this->changeDecimal('transfert_caisse', ['montant'], 18, 3);
        $this->changeDecimal('transfert_lignes', ['qte'], 20, 3);
        $this->changeDecimal('transformation_lignes', ['quantite'], 20, 3);
    }

    public function down(): void
    {
        // rollbakc to 2 digits after decimal point
        // Revert all decimals to 3 digits after decimal point
        $this->changeDecimal('achats', ['total_ht', 'total_ttc', 'total_tva', 'total_reduction', 'debit', 'credit'], 18, 3);
        $this->changeDecimal('achat_lignes', ['ht', 'quantite', 'reduction', 'total_ttc'], 18, 3);
        $this->changeDecimal('ventes', ['total_ht', 'total_ttc', 'total_tva', 'total_reduction', 'solde', 'encaisser'], 18, 3);
        $this->changeDecimal('vente_lignes', ['ht', 'quantite', 'reduction', 'total_ttc', 'revient'], 18, 3);
        $this->changeDecimal('paiements', ['encaisser', 'decaisser'], 18, 3);
        $this->changeDecimal('articles', ['prix_achat', 'prix_revient', 'prix_vente'], 18, 3);
        $this->changeDecimal('cheques', ['montant'], 18, 3);
        $this->changeDecimal('clients', ['limite_de_credit'], 18, 3);
        $this->changeDecimal('fournisseurs', ['limite_de_credit'], 18, 3);
        $this->changeDecimal('commercials', ['objectif'], 18, 3);
        $this->changeDecimal('depenses', ['montant', 'encaisser', 'solde'], 18, 3);
        $this->changeDecimal('renouvellements', ['montant'], 18, 3);
        $this->changeDecimal('stock_entrees', ['quantite'], 20, 3);
        $this->changeDecimal('transaction_stocks', ['qte_sortir', 'qte_entree', 'valeur_sortir', 'valeur_entrer'], 20, 3);
        $this->changeDecimal('transfert_caisse', ['montant'], 18, 3);
        $this->changeDecimal('transfert_lignes', ['qte'], 20, 3);
        $this->changeDecimal('transformation_lignes', ['quantite'], 20, 3);
    }

    private function changeDecimal(string $tableName, array $columns, int $precision, int $scale): void
    {
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
    }
};
