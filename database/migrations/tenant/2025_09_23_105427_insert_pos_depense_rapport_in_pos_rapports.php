<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
       DB::table('pos_rapports')->where('cle','dp')->existsOr(function (){
           DB::table('pos_rapports')->insert([
               'cle' => 'dp',
               'nom' => 'Rapport des Dépenses',
               'actif' => 1
           ]);
       });
    }

    public function down(): void
    {
        Schema::table('pos_rapports', function (Blueprint $table) {
            //
        });
    }
};
