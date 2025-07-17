<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pos_rapports', function (Blueprint $table) {

        });
        DB::table('pos_rapports')->insert([
            'nom' => 'Rapport Articles-Fournisseurs',
            'cle' => 'af',
            'actif' => 1
        ]);
        DB::table('pos_rapports')->insert([
            'nom' => 'Rapport des Paiements et Créances',
            'cle' => 'cr',
            'actif' => 1
        ]);
        DB::table('pos_rapports')->insert([
            'nom' => 'Rapport de Trésorerie',
            'cle' => 'tr',
            'actif' => 1
        ]);
        DB::table('pos_rapports')->where('cle', 'ac')->update(['nom' => 'Rapport de vente par article et client']);

    }

    public function down(): void
    {
        Schema::table('pos_rapports', function (Blueprint $table) {
            //
        });
    }
};
