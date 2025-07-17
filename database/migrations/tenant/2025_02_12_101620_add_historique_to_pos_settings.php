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
        if (Schema::hasTable('pos_settings')) {
            if (!DB::table('pos_settings')->where('key', 'historique')->exists()) {
                DB::table('pos_settings')->insert([
                    'key' => 'historique',
                    'label' => 'Historique',
                    'value' => 1,
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
            DB::table('pos_settings')->where('key', 'historique')->delete();
        }
    }
};
