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
        Schema::create('demande_transferts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('magasin_entree_id')->references('id')->on('magasins')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('magasin_sortie_id')->references('id')->on('magasins')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('reference');
            $table->string('statut');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demande_transferts');
    }
};
