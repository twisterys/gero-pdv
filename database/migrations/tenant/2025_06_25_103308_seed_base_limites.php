<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $usersLimit = DB::table('limites')->where('key', 'users')->first();
        if (!$usersLimit || $usersLimit->value == 0) {
            if ($usersLimit) {
                // Update existing record with value 0
                DB::table('limites')->where('key', 'users')->update(['value' => 2]);
            } else {
                // Insert new record
                DB::table('limites')->insert([
                    'key' => 'users',
                    'value' => 2
                ]);
            }
        }

        // Insert 'stock' limit only if the value is 0 or record doesn't exist
        $stockLimit = DB::table('limites')->where('key', 'stock')->first();
        if (!$stockLimit || $stockLimit->value == 0) {
            if ($stockLimit) {
                // Update existing record with value 0
                DB::table('limites')->where('key', 'stock')->update(['value' => 1]);
            } else {
                // Insert new record
                DB::table('limites')->insert([
                    'key' => 'stock',
                    'value' => 1
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('limites', function (Blueprint $table) {
            //
        });
    }
};
