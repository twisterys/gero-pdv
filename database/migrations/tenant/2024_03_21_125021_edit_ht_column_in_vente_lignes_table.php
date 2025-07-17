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
        Schema::table('vente_lignes', function (Blueprint $table) {
            $table->decimal('ht', 18, 6)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vente_lignes', function (Blueprint $table) {
            $table->decimal('ht', 10, 2)->default(0)->change();
        });
    }
};
