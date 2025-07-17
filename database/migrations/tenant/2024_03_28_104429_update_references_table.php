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
        Schema::table('references', function (Blueprint $table) {
            $table->dropColumn('prefixe');
            $table->dropColumn('format_date');
            $table->dropColumn('separateur');
            $table->dropColumn('emplacement_separateur');
            $table->dropColumn('format_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reference',function (Blueprint $table){
            $table->string('prefixe')->nullable();
            $table->string('format_date')->nullable();
            $table->string('separateur')->nullable();
            $table->string('emplacement_separateur')->nullable();
            $table->string('format_number')->nullable();
        });
    }
};
