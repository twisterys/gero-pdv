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
        Schema::table('fournisseurs', function (Blueprint $table) {
            $table->unsignedBigInteger('forme_juridique_id')->nullable()->after('id');

            $table->foreign('forme_juridique_id')
                ->references('id')
                ->on('forme_juridique')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fournisseur', function (Blueprint $table) {
            $table->dropForeign(['forme_juridique_id']);
            $table->dropColumn('forme_juridique_id');
        });
    }
};
