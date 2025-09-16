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
        if (!\DB::table('rapports')->where('route', 'sessions')->exists()) {
            \DB::table('rapports')->insert([
                'nom' => 'Rapport de session',
                'route' => 'sessions',
                'description' => 'Suivi des sessions de caisse POS : ouverture et fermeture des caisses par utilisateur et par magasin.',
                'type' => 'pos'
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
