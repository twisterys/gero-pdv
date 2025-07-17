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
            $table->unsignedBigInteger('magasin_id')->nullable();
            $table->foreign('magasin_id')->references('id')->on('magasins')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vente_lignes', function (Blueprint $table) {
            $table->dropForeign(['magasin_id']);
            $table->dropColumn('magasin_id');
        });
    }
};
