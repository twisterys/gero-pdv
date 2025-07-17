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
        DB::table('rapports')->insert([
            'nom' => 'Rapport annuel arrêté',
            'route' => 'annuel',
            'description' => 'Rapport annuel arreté',
            'type' => 'statistiques',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rapport', function (Blueprint $table) {
            //
        });
    }
};
