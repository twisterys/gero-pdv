<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pos_settings')) {
            if (!DB::table('pos_settings')->where('key', 'double_ticket_template')->exists()) {
                DB::table('pos_settings')->insert([
                    'key' => 'double_ticket_template',
                    'label' => 'billet avec deux copier',
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
            DB::table('pos_settings')->where('key', 'double_ticket_template')->delete();
        }
    }
};
