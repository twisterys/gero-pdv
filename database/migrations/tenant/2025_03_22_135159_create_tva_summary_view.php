<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::statement('
    CREATE VIEW tva_summary AS
    SELECT
        ventes.id AS vente_id,
        achats.id AS achat_id,
        ventes.date_emission AS vente_date,
        achats.date_emission AS achat_date,
        SUM((paiements.encaisser / ventes.total_ttc) * ventes.total_tva) AS tva_ventes,
        SUM((paiements.decaisser / achats.total_ttc) * achats.total_tva) AS tva_achats
    FROM paiements
    LEFT JOIN ventes ON paiements.payable_id = ventes.id
    LEFT JOIN achats ON paiements.payable_id = achats.id
    WHERE ventes.type_document = "fa"
    OR achats.type_document = "faa"
    GROUP BY ventes.id, achats.id;
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
