<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Add decimal_length to global_settings if table and column checks pass
        if (Schema::hasTable('global_settings') && !Schema::hasColumn('global_settings', 'decimal_length')) {
            Schema::table('global_settings', function (Blueprint $table) {
                $table->integer('decimal_length')->default(2);
            });
        }

        // Helper closures to reduce repetition
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

        // Apply changes safely only if tables/columns exist (use wider precision to avoid overflow from DOUBLE → DECIMAL)
        $changeDecimal('achats', ['total_ht', 'total_ttc', 'total_tva', 'total_reduction', 'debit', 'credit'], 18, 5);
        $changeDecimal('achat_lignes', ['ht', 'quantite', 'reduction', 'total_ttc'], 18, 5);
        $changeDecimal('ventes', ['total_ht', 'total_ttc', 'total_tva', 'total_reduction', 'solde', 'encaisser'], 18, 5);
        $changeDecimal('vente_lignes', ['ht', 'quantite', 'reduction', 'total_ttc', 'revient'], 18, 5);
        $changeDecimal('paiements', ['encaisser', 'decaisser'], 18, 5);
        $changeDecimal('articles', ['prix_achat', 'prix_revient', 'prix_vente'], 18, 5);
        $changeDecimal('cheques', ['montant'], 18, 5);
        $changeDecimal('clients', ['limite_de_credit'], 18, 5);
        $changeDecimal('fournisseurs', ['limite_de_credit'], 18, 5);
        $changeDecimal('commercials', ['objectif'], 18, 5);
        $changeDecimal('depenses', ['montant', 'encaisser', 'solde'], 18, 5);
        $changeDecimal('renouvellements', ['montant'], 18, 5);
        $changeDecimal('stock_entrees', ['quantite'], 20, 5);
        $changeDecimal('transaction_stocks', ['qte_sortir', 'qte_entree', 'valeur_sortir', 'valeur_entrer'], 20, 5);
        $changeDecimal('transfert_caisse', ['montant'], 18, 5);
        $changeDecimal('transfert_lignes', ['qte'], 20, 5);
        $changeDecimal('transformation_lignes', ['quantite'], 20, 5);
    }

    public function down(): void
    {
        // No down migration provided because original types/lengths may vary across installations
    }
};
