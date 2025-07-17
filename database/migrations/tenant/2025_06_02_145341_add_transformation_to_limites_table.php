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
        $exists = DB::table('limites')->where('key', 'transformation')->exists();

        // Only insert if it doesn't exist
        if (!$exists) {
            DB::table('limites')->insert([
                'key' => 'transformation',
                'value' => 1,
            ]);
        }


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         DB::table('limites')->where('key', 'transformation')->delete();
    }
};
