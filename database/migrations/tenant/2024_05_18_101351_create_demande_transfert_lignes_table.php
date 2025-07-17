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
        Schema::create('demande_transfert_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demande_transfert_id')->references('id')->on('demande_transferts')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('article_id')->references('id')->on('articles')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('quantite_demande')->nullable();
            $table->string('quantite_livre')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demande_transfert_lignes');
    }
};
