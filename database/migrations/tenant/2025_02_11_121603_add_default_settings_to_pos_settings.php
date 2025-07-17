<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('pos_settings')) { // Vérifie si la table existe avant d'insérer
            if (!DB::table('pos_settings')->where('key', 'reduction')->exists()) {
                DB::table('pos_settings')->insert([
                    'key' => 'reduction',
                    'label' => 'Réduction',
                    'value' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pos_settings')) {
            DB::table('pos_settings')->where('key', 'reduction')->delete();
        }
    }
};
