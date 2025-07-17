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
            'nom' => 'Rapport de TVA',
            'route' => 'tva',
            'description' => 'Rapport détaillé de la TVA pour la comptabilité',
            'type' => 'comptabilité',
        ]);
    }

};
