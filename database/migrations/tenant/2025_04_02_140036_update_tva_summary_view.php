<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS tva_summary');

        // Create the new view
        DB::statement('
         CREATE VIEW tva_summary AS
            SELECT
                paiements.date_paiement AS date_paiement,
                ventes.id AS vente_id,
                achats.id AS achat_id,
                SUM((paiements.encaisser / ventes.total_ttc) * ventes.total_tva) AS tva_ventes,
                SUM((paiements.decaisser / achats.total_ttc) * achats.total_tva) AS tva_achats
            FROM paiements
            LEFT JOIN ventes ON paiements.payable_id = ventes.id
            LEFT JOIN achats ON paiements.payable_id = achats.id
            WHERE ventes.type_document = "fa"
            OR achats.type_document = "faa"
            GROUP BY paiements.date_paiement, ventes.id, achats.id;
        ');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS tva_summary');
    }
};
