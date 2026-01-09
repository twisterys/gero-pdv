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
        $tables = ['transferts', 'depenses', 'rebuts'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->boolean('is_controled')->default(false);
                $table->timestamp('controled_at')->nullable();
                $table->unsignedBigInteger('controled_by')->nullable();
                $table->foreign('controled_by')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['transferts', 'depenses', 'rebuts'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign([$table . '_controled_by_foreign']);
                $table->dropColumn(['is_controled', 'controled_at', 'controled_by']);
            });
        }
    }
};
