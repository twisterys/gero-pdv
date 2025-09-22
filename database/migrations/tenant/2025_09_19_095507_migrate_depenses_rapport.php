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
        if (!\DB::table('rapports')->where('route', 'categorie-depense')->exists()) {
            \DB::table('rapports')->insert([
                'nom' => 'Catégorie Dépense',
                'route' => 'categorie-depense',
                'description' => "Analyse détaillée des dépenses par catégorie, permettant d'identifier les postes de coûts principaux et d'optimiser la gestion financière.",
                'type' => 'statistiques'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
