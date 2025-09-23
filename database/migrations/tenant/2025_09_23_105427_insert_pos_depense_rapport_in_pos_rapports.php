<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!DB::table('pos_rapports')->where('cle','dp')->exists()) {
            DB::table('pos_rapports')->insert([
                'cle' => 'dp',
                'nom' => 'Rapport des Dépenses',
                'actif' => 1
            ]);
        }
    }

    public function down(): void
    {
        // Optional: you can delete the inserted row on rollback
        // DB::table('pos_rapports')->where('cle','dp')->delete();
    }
};
